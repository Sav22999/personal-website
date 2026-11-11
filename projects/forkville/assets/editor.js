/*
 * Forkville — build mode. The player edits a draft of the layout (place, move, rotate, sell, floors and
 * walls); nothing is spent until "Save", when the whole draft goes to game.layout and the server bills
 * the new pieces and refunds the sold ones (the same rules as here, see apply_layout in lib/economy.php).
 */
import * as THREE from '../vendor/three.module.min.js';
import {furnitureModel} from './models.js';

export class Editor {
    constructor(world, catalog, hooks) {
        this.world = world;
        this.catalog = catalog;
        this.hooks = hooks; // {changed(), selected(item|null)}
        this.active = false;
        world.on('hover', e => this.active && this.hover(e));
        world.on('tap', e => this.active && this.tap(e));
    }

    begin(r) {
        this.active = true;
        this.r = r;
        this.original = new Map(r.layout.map(it => [it.id, it]));
        this.draft = r.layout.map(it => ({...it}));
        this.floor = r.floor;
        this.wall = r.wall;
        this.tempId = -1;
        this.placing = null;
        this.selected = null;
        this.grid = new THREE.GridHelper(r.size, r.size, '#8A6A4A', '#8A6A4A');
        this.grid.position.set(r.size / 2, 0.05, r.size / 2);
        this.grid.material.opacity = 0.28;
        this.grid.material.transparent = true;
        this.world.root.add(this.grid);
        this.marks = new THREE.Group();
        this.world.root.add(this.marks);
        // The door tile is always kept free: show it
        this.doorMark = this.tileMark(this.world.door, r.size - 1, '#3E7CB4', 0.35);
        this.world.root.add(this.doorMark);
        // The outdoor area, shown faintly while building
        this.outdoorMarks = new THREE.Group();
        const O = this.catalog.outdoor || 3;
        for (let x = 0; x < r.size + O; x++) for (let z = 0; z < r.size + O; z++) if (this.outdoorTile(x, z)) this.outdoorMarks.add(this.tileMark(x, z, '#7A8B3A', 0.13));
        this.world.root.add(this.outdoorMarks);
        this.hooks.changed();
    }

    end() {
        this.cancelPlacing();
        this.select(null);
        this.world.root.remove(this.grid, this.marks, this.doorMark, this.outdoorMarks);
        this.active = false;
    }

    tileMark(x, z, color, opacity = 0.45) {
        const m = new THREE.Mesh(new THREE.PlaneGeometry(0.96, 0.96), new THREE.MeshBasicMaterial({color, transparent: true, opacity, depthWrite: false}));
        m.rotation.x = -Math.PI / 2;
        m.position.set(x + 0.5, 0.06, z + 0.5);
        return m;
    }

    // ------------------------------------------------------------ rules (same as clean_layout on the server)

    /** Tiles taken on one layer: rugs lie under furniture (and on the door tile), never on each other. */
    occupied(exceptId, rugs = false) {
        const set = new Set(rugs ? [] : [this.world.door + ',' + (this.r.size - 1)]);
        for (const it of this.draft) {
            if (it.id === exceptId || (this.catalog.items[it.k].cat === 'rug') !== rugs) continue;
            for (const [x, z] of this.world.footprint(it, this.catalog.items).tiles) set.add(x + ',' + z);
        }
        return set;
    }

    /** The outdoor area (same rule as outdoor_tile in lib/economy.php): terrace in front, yard at the side, never the path to the door. */
    outdoorTile(x, z) {
        const n = this.r.size, O = this.catalog.outdoor || 3;
        const front = x >= 0 && x < n + O && z >= n && z < n + O;
        const side = x >= n && x < n + O && z >= 0 && z < n;
        return (front || side) && !(x === this.world.door && z >= n);
    }

    fits(it) {
        const cat = this.catalog.items[it.k].cat;
        const occ = this.occupied(it.id, cat === 'rug');
        const n = this.r.size;
        const inside = (x, z) => cat === 'outdoor' ? this.outdoorTile(x, z) : x >= 0 && z >= 0 && x < n && z < n;
        return this.world.footprint(it, this.catalog.items).tiles.every(([x, z]) => inside(x, z) && !occ.has(x + ',' + z));
    }

    cost() {
        let spend = 0, refund = 0;
        const kept = new Set();
        for (const it of this.draft) {
            if (it.id > 0 && this.original.has(it.id)) kept.add(it.id);
            else spend += this.catalog.items[it.k].price;
        }
        for (const [id, it] of this.original) if (!kept.has(id)) refund += Math.floor(this.catalog.items[it.k].price * this.catalog.rules.sellBack);
        for (const kind of ['floor', 'wall']) if (this[kind] !== this.r[kind] && !this.r.finishes.includes(this[kind])) spend += this.catalog.items[this[kind]].price;
        return {spend, refund, net: spend - refund};
    }

    payload() {
        return {layout: this.draft.map(it => ({id: it.id > 0 ? it.id : 0, k: it.k, x: it.x, z: it.z, r: it.r})), floor: this.floor, wall: this.wall};
    }

    // ------------------------------------------------------------ placing

    /** Start placing a new piece from the shop (or an existing one being moved). */
    startPlacing(key, existing = null) {
        this.cancelPlacing();
        this.select(null);
        const outdoor = this.catalog.items[key].cat === 'outdoor';
        const it = existing ? {...existing} : {id: this.tempId--, k: key, x: outdoor ? 1 : Math.floor(this.r.size / 2), z: outdoor ? this.r.size + 1 : Math.floor(this.r.size / 2), r: 0};
        if (existing) {
            this.draft = this.draft.filter(d => d.id !== existing.id);
            this.world.removeItem(existing.id);
        }
        const ghost = furnitureModel(it.k);
        ghost.traverse(o => {
            if (o.isMesh) {
                o.material = o.material.clone();
                o.material.transparent = true;
                o.material.opacity = 0.7;
                o.castShadow = false;
            }
        });
        this.world.root.add(ghost);
        this.placing = {it, ghost, moving: existing ? {...existing} : null};
        this.updateGhost();
        this.hooks.changed();
    }

    cancelPlacing() {
        if (!this.placing) return;
        const p = this.placing;
        this.world.root.remove(p.ghost);
        this.marks.clear();
        if (p.moving) { // put the piece back where it was
            this.draft.push(p.moving);
            this.world.addItem(p.moving);
        }
        this.placing = null;
        this.hooks.changed();
    }

    updateGhost() {
        const p = this.placing;
        this.world.placeObject(p.ghost, p.it, this.catalog.items);
        p.ghost.position.y = 0.04;
        this.marks.clear();
        const ok = this.fits(p.it);
        for (const [x, z] of this.world.footprint(p.it, this.catalog.items).tiles) this.marks.add(this.tileMark(x, z, ok ? '#6BBF59' : '#E04F4F'));
        p.ok = ok;
    }

    rotate() {
        if (this.placing) {
            this.placing.it.r = (this.placing.it.r + 1) % 4;
            this.updateGhost();
        } else if (this.selected) {
            const it = this.selected;
            const next = {...it, r: (it.r + 1) % 4};
            if (!this.fits(next)) return this.hooks.toast?.('no_room');
            it.r = next.r;
            this.world.placeObject(this.world.items.get(it.id), it, this.catalog.items);
            this.highlight();
        }
        this.hooks.changed();
    }

    hover(e) {
        if (!this.placing) return;
        const t = this.world.tileAt(e);
        if (!t) return;
        const {w, d} = this.world.footprint(this.placing.it, this.catalog.items);
        const x = Math.round(t.px - w / 2), z = Math.round(t.pz - d / 2);
        if (x === this.placing.it.x && z === this.placing.it.z) return;
        this.placing.it.x = x;
        this.placing.it.z = z;
        this.updateGhost();
    }

    tap(e) {
        if (this.placing) {
            this.hover(e);
            if (!this.placing.ok) return this.hooks.toast?.('no_room');
            const p = this.placing;
            const it = {...p.it};
            this.draft.push(it);
            this.world.addItem(it);
            this.world.root.remove(p.ghost);
            this.marks.clear();
            this.placing = null;
            // Keep placing more of the same new piece (chairs come in groups); a moved piece is done
            if (!p.moving && this.cost().net <= this.hooks.coins()) this.startPlacing(it.k);
            this.hooks.changed();
            return;
        }
        const id = this.world.itemAt(e);
        this.select(id != null ? this.draft.find(d => d.id === id) || null : null);
    }

    select(it) {
        this.selected = it;
        this.highlight();
        this.hooks.selected(it);
    }

    highlight() {
        this.marks.clear();
        if (!this.selected) return;
        for (const [x, z] of this.world.footprint(this.selected, this.catalog.items).tiles) this.marks.add(this.tileMark(x, z, '#E2B23A', 0.55));
    }

    moveSelected() {
        if (this.selected) this.startPlacing(this.selected.k, this.selected);
    }

    sellSelected() {
        if (!this.selected) return;
        this.draft = this.draft.filter(d => d.id !== this.selected.id);
        this.world.removeItem(this.selected.id);
        this.select(null);
        this.hooks.changed();
    }

    setFinish(key) {
        const cat = this.catalog.items[key].cat;
        this[cat] = key;
        if (cat === 'floor') this.world.setFloor(key);
        else this.world.setWall(key);
        this.hooks.changed();
    }
}
