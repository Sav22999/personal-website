/*
 * Forkville — build mode. The player edits a draft of the layout (place, move, rotate, sell, floors and
 * walls); nothing is spent until "Save", when the whole draft goes to game.layout and the server bills
 * the new pieces and refunds the sold ones (the same rules as here, see apply_layout in lib/economy.php).
 */
import * as THREE from '../vendor/three.module.min.js';
import {furnitureModel, useArrow} from './models.js';
import {FH} from './scene.js';

export class Editor {
    constructor(world, catalog, hooks) {
        this.world = world;
        this.catalog = catalog;
        this.hooks = hooks; // {changed(), selected(item|null)}
        this.active = false;
        world.on('hover', e => this.active && this.hover(e));
        world.on('tap', e => this.active && this.tap(e));
        world.on('rtap', () => this.active && (this.placing || this.selected) && this.rotate(1)); // right click turns the piece
        world.on('frame', () => this.active && this.updateLabel());
        // Dragging: a piece can be picked up and dropped elsewhere; on touch screens the piece being placed follows the finger
        world.onPress = (e) => this.press(e);
        world.onDragMove = (e) => this.hover(e, true);
        world.onDragEnd = (e, moved) => this.release(e, moved);
        // "Place several": on by default only for chairs (they come in groups); remembered per category
        try {
            this.repeatPrefs = JSON.parse(localStorage.getItem('forkville-repeat') || '{}');
        } catch (e) {
            this.repeatPrefs = {};
        }
        window.addEventListener('keydown', e => this.key(e));
    }

    /** Whether placing a piece of this category starts another one right after. */
    repeatFor(cat) {
        return this.repeatPrefs[cat] ?? cat === 'chair';
    }

    setRepeat(cat, on) {
        this.repeatPrefs[cat] = on;
        try {
            localStorage.setItem('forkville-repeat', JSON.stringify(this.repeatPrefs));
        } catch (e) { /* storage blocked */ }
        this.hooks.changed();
    }

    /** Keyboard: R / E turn right, Q turns left, M moves, C copies, Delete sells, Esc cancels, Enter places. */
    key(e) {
        if (!this.active || e.target.closest?.('input, textarea, select')) return;
        const k = e.key.toLowerCase();
        if (this.doorMove && k === 'escape') return this.stopDoorMove();
        if (k === 'r' || k === 'e') this.rotate(e.shiftKey ? -1 : 1);
        else if (k === 'q') this.rotate(-1);
        else if (k === 'escape') this.placing ? this.cancelPlacing() : this.select(null);
        else if ((k === 'delete' || k === 'backspace') && this.selected && !this.placing) this.sellSelected();
        else if (k === 'm' && this.selected && !this.placing) this.moveSelected();
        else if (k === 'c' && this.selected && !this.placing) this.copySelected();
        else if (k === 'enter' && this.placing) this.confirm();
        else return;
        e.preventDefault();
    }

    /** A press on the canvas: grab a piece to drag it (or, on touch, the piece being placed). True = the press is ours. */
    press(e) {
        if (!this.active || this.doorMove) return false;
        if (this.placing) {
            if (e.pointerType !== 'mouse') {
                this.hover(e, true);
                return true;
            }
            return false; // with a mouse a click places it
        }
        const id = this.world.itemAt(e);
        const it = id != null ? this.draft.find(d => d.id === id && (d.f || 0) === this.level) : null;
        if (!it) return false;
        this.select(it);
        this.dragging = {it, startX: e.clientX, startY: e.clientY, started: false};
        return true;
    }

    release(e, moved) {
        const d = this.dragging;
        this.dragging = null;
        if (d) {
            if (!d.started) return; // a simple tap: it stays selected
            if (this.placing?.ok) this.place();
            else this.cancelPlacing();
            return;
        }
        if (this.placing && !moved && e.pointerType !== 'mouse') this.updateGhost(); // touch: the piece waits for "Place here"
    }

    begin(r) {
        this.active = true;
        this.r = r;
        this.original = new Map(r.layout.map(it => [it.id, it]));
        this.draft = r.layout.map(it => ({...it}));
        this.floor = r.floor;
        this.wall = r.wall;
        this.level = this.world.viewFloor || 0; // the storey being built on (0 = ground floor)
        this.tempId = -1;
        this.storage = {...(r.storage || {})}; // pieces put away before this session
        this.stored = new Set(); // ids of placed pieces put away in this session (not sold)
        this.placing = null;
        this.selected = null;
        this.W = r.size;
        this.D = r.depth || r.size;
        this.marks = new THREE.Group();
        this.world.root.add(this.marks);
        this.doorW = this.world.doorW || 1;
        this.world.showBuildMarks?.(true); // window markers on the floor, only while building
        this.drawFloorMarks();
        this.snapshot = JSON.stringify(this.payload());
        this.doorMove = false;
        this.hooks.changed();
    }

    /** Unsaved changes in the draft? */
    dirty() {
        return JSON.stringify(this.payload()) !== this.snapshot;
    }

    /** Where the door may go: a front tile (not in a corner) free inside, with the path outside free. */
    doorSpotOk(x) {
        const dw = this.doorW || 1;
        if (x < 1 || x + dw - 1 > this.W - 2) return false;
        for (const it of this.draft) {
            const cat = this.catalog.items[it.k].cat;
            if (cat === 'rug' || cat === 'ground' || it.f || (cat === 'window' && it.r !== 2)) continue;
            if (this.world.footprint(it, this.catalog.items).tiles.some(([tx, tz]) => tx >= x && tx < x + dw && (tz === this.D - 1 || (cat === 'outdoor' && tz >= this.D)))) return false;
        }
        return true;
    }

    /** Choosing a new place for the door: the front row lights up (green where it can go). */
    startDoorMove() {
        this.cancelPlacing();
        this.select(null);
        if (this.level) this.setLevel(0);
        this.doorMove = true;
        this.marks.clear();
        for (let x = 1; x < this.W - 1; x++) if (x !== this.world.door) this.marks.add(this.tileMark(x, this.D - 1, this.doorSpotOk(x) ? '#6BBF59' : '#E04F4F', 0.4));
        this.hooks.changed();
    }

    stopDoorMove() {
        this.doorMove = false;
        this.marks.clear();
        this.hooks.changed();
    }

    /** Grid, door and outdoor area of the storey being built on. */
    drawFloorMarks() {
        this.world.root.remove(this.grid, this.doorMark, this.outdoorMarks);
        const rect = this.world.floorRect(this.level);
        const pts = [];
        for (let x = 0; x <= rect.w; x++) pts.push(rect.x0 + x, 0, 0, rect.x0 + x, 0, rect.d);
        for (let z = 0; z <= rect.d; z++) pts.push(rect.x0, 0, z, rect.x0 + rect.w, 0, z);
        this.grid = new THREE.LineSegments(new THREE.BufferGeometry().setAttribute('position', new THREE.Float32BufferAttribute(pts, 3)), new THREE.LineBasicMaterial({color: '#8A6A4A', transparent: true, opacity: 0.28}));
        this.grid.position.set(0, this.level * FH + 0.05, 0);
        this.world.root.add(this.grid);
        this.doorMark = new THREE.Group();
        this.outdoorMarks = new THREE.Group();
        if (!this.level) {
            for (let k = 0; k < (this.doorW || 1); k++) this.doorMark.add(this.tileMark(this.world.door + k, this.D - 1, '#3E7CB4', 0.35)); // the door tiles are always kept free
            const O = this.catalog.outdoor || 3;
            for (let x = 0; x < this.W + O; x++) for (let z = -O; z < this.D + O; z++) if (this.outdoorTile(x, z)) this.outdoorMarks.add(this.tileMark(x, z, '#7A8B3A', 0.13));
        } else for (const key of [...this.world.shaftTiles(this.level, this.draft, this.catalog.items), ...this.world.stairExits(this.level, this.draft, this.catalog.items)]) { // the way up and off the stairs: kept free
            const [x, z] = key.split(',').map(Number);
            this.doorMark.add(this.tileMark(x, z, '#3E7CB4', 0.3));
        }
        this.world.root.add(this.doorMark, this.outdoorMarks);
    }

    /** Build on another storey (the draft is kept). */
    setLevel(f) {
        this.cancelPlacing();
        this.select(null);
        this.level = f;
        this.drawFloorMarks();
        this.hooks.changed();
    }

    end() {
        this.cancelPlacing();
        this.select(null);
        this.dragging = null;
        if (this.hoverMarks) this.world.root.remove(this.hoverMarks);
        this.hoverMarks = null;
        this.hoverId = null;
        this.world.canvas.style.cursor = '';
        if (this.label) this.label.style.display = 'none';
        this.world.root.remove(this.grid, this.marks, this.doorMark, this.outdoorMarks);
        this.grid = this.doorMark = this.outdoorMarks = null;
        this.active = false;
        this.world.showBuildMarks?.(false);
    }

    tileMark(x, z, color, opacity = 0.45) {
        const m = new THREE.Mesh(new THREE.PlaneGeometry(0.96, 0.96), new THREE.MeshBasicMaterial({color, transparent: true, opacity, depthWrite: false}));
        m.rotation.x = -Math.PI / 2;
        m.position.set(x + 0.5, (this.level || 0) * FH + 0.06, z + 0.5);
        return m;
    }

    // ------------------------------------------------------------ rules (same as clean_layout on the server)

    /** Tiles taken on one layer: rugs lie under furniture (and on the door tile), never on each other. */
    occupied(exceptId, rugs = false) {
        const layerOf = (cat) => cat === 'rug' || cat === 'ground' ? 'rug' : cat === 'window' ? 'window' : 'solid';
        const layer = rugs === true ? 'rug' : rugs || 'solid';
        const doorTiles = Array.from({length: this.doorW || 1}, (_, k) => (this.world.door + k) + ',' + (this.D - 1));
        const others = this.draft.filter(d => d.id !== exceptId);
        const set = new Set(layer !== 'solid' ? [] : this.level ? [...this.world.shaftTiles(this.level, others, this.catalog.items), ...this.world.stairExits(this.level, others, this.catalog.items)] : doorTiles);
        for (const it of this.draft) {
            if (it.id === exceptId || (it.f || 0) !== this.level || layerOf(this.catalog.items[it.k].cat) !== layer) continue;
            // Windows on the same tile but on two walls (a corner) do not clash: the wall is part of the key
            for (const [x, z] of this.world.footprint(it, this.catalog.items).tiles) set.add(x + ',' + z + (layer === 'window' ? ',' + it.r : ''));
        }
        return set;
    }

    /** The outdoor area (same rule as outdoor_tile in lib/economy.php): terrace in front, yard at the side, never the path to the door. */
    outdoorTile(x, z, path = false) {
        const W = this.W, D = this.D, O = this.catalog.outdoor || 3;
        const front = x >= 0 && x < W + O && z >= D && z < D + O;
        const side = x >= W && x < W + O && z >= 0 && z < D;
        const back = x >= 0 && x < W + O && z >= -O && z < 0; // the back garden
        return (front || side || back) && (path || !(x >= this.world.door && x < this.world.door + (this.doorW || 1) && z >= D));
    }

    fits(it) {
        this.reason = null;
        const cat = this.catalog.items[it.k].cat;
        if ((cat === 'outdoor' || cat === 'ground') && this.level) return false; // the garden is on the ground
        const occ = this.occupied(it.id, cat === 'rug' || cat === 'ground' ? 'rug' : cat === 'window' ? 'window' : 'solid');
        const rect = this.world.floorRect(this.level);
        const inRect = (r, x, z) => r && x >= r.x0 && x < r.x0 + r.w && z >= 0 && z < r.d;
        const tiles = this.world.footprint(it, this.catalog.items).tiles;
        if (cat === 'window') { // along its wall, off the doorway, not over another window
            this.reason = 'window_wall';
            const on = (x, z) => inRect(rect, x, z) && [z === 0, x === rect.x0, z === rect.d - 1, x === rect.x0 + rect.w - 1][it.r];
            if (!tiles.every(([x, z]) => on(x, z) && !occ.has(x + ',' + z + ',' + it.r))) return false;
            if (!this.level && it.r === 2 && tiles.some(([x]) => x >= this.world.door && x < this.world.door + (this.doorW || 1))) return (this.reason = 'window_door'), false;
            return true;
        }
        const inside = (x, z) => cat === 'outdoor' ? this.outdoorTile(x, z) : cat === 'ground' ? this.outdoorTile(x, z, true) : inRect(rect, x, z);
        if (!tiles.every(([x, z]) => inside(x, z) && !occ.has(x + ',' + z))) return false;
        if (cat === 'stairs') { // it must lead to a floor above (a lift starts on the ground floor)
            this.reason = 'stairs_under_floor';
            if (it.k.startsWith('lift') && this.level) return (this.reason = 'lift_ground'), false;
            const up = this.world.floorRect(this.level + 1);
            if (!up) return (this.reason = 'no_floor_above'), false;
            if (!tiles.every(([x, z]) => inRect(up, x, z))) return false;
            // and nothing may stand where it comes out
            const above = new Set(this.draft.filter(d => (d.f || 0) === this.level + 1 && !['rug', 'window'].includes(this.catalog.items[d.k].cat)).flatMap(d => this.world.footprint(d, this.catalog.items).tiles.map(([x, z]) => x + ',' + z)));
            if (tiles.some(([x, z]) => above.has(x + ',' + z))) return false;
            // and the tile where they come out, past the top end, must be on the floor above and free
            if (!it.k.startsWith('lift')) {
                const exits = [...this.world.stairExits(this.level + 1, [{...it, f: this.level}], this.catalog.items)];
                this.reason = 'stairs_exit';
                if (!exits.every(k => { const [x, z] = k.split(',').map(Number); return inRect(up, x, z) && !above.has(k); })) return false;
            }
        }
        return true;
    }

    /** How many of a piece are in storage now (stored before, plus put away now, minus taken out now). */
    inStorage(k) {
        let n = this.storage[k] || 0;
        for (const id of this.stored) if (this.original.get(id)?.k === k && !this.draft.some(d => d.id === id)) n++;
        for (const it of this.draft) if (it.st && it.k === k) n--;
        if (this.placing?.it.st && !this.placing.moving && this.placing.it.k === k) n--;
        return n;
    }

    /** Everything in storage: [[key, count], …]. */
    storageList() {
        const keys = new Set([...Object.keys(this.storage), ...[...this.stored].map(id => this.original.get(id)?.k).filter(Boolean)]);
        return [...keys].map(k => [k, this.inStorage(k)]).filter(([, n]) => n > 0);
    }

    cost() {
        let spend = 0, refund = 0;
        const kept = new Set();
        for (const it of this.draft) {
            if (it.id > 0 && this.original.has(it.id)) kept.add(it.id);
            else if (!it.st) spend += this.catalog.items[it.k].price; // out of storage: already paid for
        }
        for (const [id, it] of this.original) if (!kept.has(id) && !this.stored.has(id)) refund += Math.floor(this.catalog.items[it.k].price * this.catalog.rules.sellBack);
        for (const kind of ['floor', 'wall']) if (this[kind] !== this.r[kind] && !this.r.finishes.includes(this[kind])) spend += this.catalog.items[this[kind]].price;
        return {spend, refund, net: spend - refund};
    }

    payload() {
        const take = {};
        for (const it of this.draft) if (it.st) take[it.k] = (take[it.k] || 0) + 1;
        const store = [...this.stored].filter(id => !this.draft.some(d => d.id === id));
        return {layout: this.draft.map(it => ({id: it.id > 0 ? it.id : 0, k: it.k, x: it.x, z: it.z, r: it.r, ...(it.f ? {f: it.f} : {})})), floor: this.floor, wall: this.wall, store, take};
    }

    // ------------------------------------------------------------ placing

    /** Start placing a new piece from the shop (or an existing one being moved). */
    startPlacing(key, existing = null, r = 0, fromStorage = false) {
        this.cancelPlacing();
        if (fromStorage && this.inStorage(key) < 1) return;
        this.select(null);
        const outdoor = ['outdoor', 'ground'].includes(this.catalog.items[key].cat);
        // A new piece starts where the last one was (or in the middle of the room), already turned like the last one
        const last = this.lastPos && this.catalog.items[this.lastPos.k]?.cat === this.catalog.items[key].cat ? this.lastPos : null;
        const rect = this.world.floorRect(this.level);
        const isWin = this.catalog.items[key].cat === 'window';
        const it = existing ? {...existing} : {id: this.tempId--, k: key, x: last ? last.x : outdoor ? 1 : rect.x0 + Math.floor(rect.w / 2), z: last ? last.z : outdoor ? this.D + 1 : Math.floor(rect.d / 2), r};
        if (!existing && isWin && last) it.r = last.r ?? it.r;
        if (!existing && isWin && !last) Object.assign(it, {x: rect.x0 + Math.max(0, Math.floor((rect.w - this.catalog.items[key].w) / 2)), z: 0, r: 0}); // starts on the back wall
        if (!existing && this.level) it.f = this.level;
        if (!existing && fromStorage) it.st = true; // taken out of storage: free
        // Moving a table takes along the chairs turned towards it (moving a chair moves only the chair)
        const attached = existing && this.catalog.items[key].cat === 'table' ? this.chairsOf(existing) : [];
        if (existing) {
            this.draft = this.draft.filter(d => d.id !== existing.id && !attached.includes(d));
            this.world.removeItem(existing.id);
            for (const c of attached) this.world.removeItem(c.id);
        }
        const ghost = furnitureModel(it.k);
        const arrow = useArrow(this.catalog.items[it.k]);
        if (arrow) ghost.add(arrow); // which side it is used from, while it is being placed
        ghost.traverse(o => {
            if (o.isMesh) {
                o.material = o.material.clone();
                o.material.transparent = true;
                o.material.opacity = 0.7;
                o.castShadow = false;
            }
        });
        this.world.root.add(ghost);
        this.placing = {it, ghost, moving: existing ? {...existing} : null, attached: attached.map(c => {
            const g = this.ghostOf(c.k);
            this.world.root.add(g);
            const tf = this.world.footprint(existing, this.catalog.items);
            // Where the chair sits, from the middle of the table (turned with it)
            return {orig: {...c}, it: {...c}, ghost: g, cx: c.x + 0.5 - (existing.x + tf.w / 2), cz: c.z + 0.5 - (existing.z + tf.d / 2), r0: existing.r};
        })};
        this.updateGhost();
        this.hooks.changed();
    }

    cancelPlacing() {
        if (!this.placing) return;
        const p = this.placing;
        this.world.root.remove(p.ghost);
        for (const c of p.attached || []) this.world.root.remove(c.ghost);
        this.marks.clear();
        if (p.moving) { // put the piece back where it was (with its chairs)
            this.draft.push(p.moving);
            this.world.addItem(p.moving);
            for (const c of p.attached || []) {
                this.draft.push(c.orig);
                this.world.addItem(c.orig);
            }
        }
        this.placing = null;
        this.hooks.changed();
    }

    updateGhost() {
        const p = this.placing;
        this.lastPos = {k: p.it.k, x: p.it.x, z: p.it.z, r: p.it.r};
        this.world.placeObject(p.ghost, p.it, this.catalog.items);
        p.ghost.position.y = (p.it.f || 0) * FH + 0.04;
        this.marks.clear();
        let ok = this.fits(p.it);
        // The chairs that come with a table: placed around it as they were (turned with it), all must fit too
        if (p.attached?.length) {
            const used = new Set(this.world.footprint(p.it, this.catalog.items).tiles.map(([x, z]) => x + ',' + z));
            const tf = this.world.footprint(p.it, this.catalog.items);
            for (const c of p.attached) {
                let cx = c.cx, cz = c.cz, cr = c.orig.r;
                for (let k = 0; k < ((p.it.r - c.r0) + 4) % 4; k++) [cx, cz, cr] = [cz, -cx, (cr + 1) % 4];
                Object.assign(c.it, {x: Math.round(p.it.x + tf.w / 2 + cx - 0.5), z: Math.round(p.it.z + tf.d / 2 + cz - 0.5), r: cr, f: p.it.f});
                if (!c.it.f) delete c.it.f;
                this.world.placeObject(c.ghost, c.it, this.catalog.items);
                c.ghost.position.y = (c.it.f || 0) * FH + 0.04;
                const key = c.it.x + ',' + c.it.z, fit = this.fits(c.it) && !used.has(key);
                used.add(key);
                if (!fit) ok = false;
                this.marks.add(this.tileMark(c.it.x, c.it.z, fit ? '#9ED27F' : '#E04F4F', 0.35));
            }
        }
        for (const [x, z] of this.world.footprint(p.it, this.catalog.items).tiles) this.marks.add(this.tileMark(x, z, ok ? '#6BBF59' : '#E04F4F'));
        p.ok = ok;
        p.reason = ok ? null : this.reason;
        // A dish lift: where the other floors have theirs (put it in the same place to join them)
        if (p.it.k === 'dish_lift') for (const o of this.draft) if (o.k === 'dish_lift' && (o.f || 0) !== this.level) this.marks.add(this.tileMark(o.x, o.z, '#7FC4D9', 0.45));
        // Stairs and lifts: show where the floor above is (they must stand under it)
        if (this.catalog.items[p.it.k].cat === 'stairs') {
            const up = this.world.floorRect(this.level + 1);
            if (up) for (let x = up.x0; x < up.x0 + up.w; x++) for (let z = 0; z < up.d; z++) this.marks.add(this.tileMark(x, z, '#7FC4D9', 0.18));
        }
    }

    rotate(dir = 1) {
        if (this.placing) {
            this.placing.it.r = (this.placing.it.r + dir + 4) % 4;
            this.updateGhost();
        } else if (this.selected) {
            const it = this.selected;
            const next = {...it, r: (it.r + dir + 4) % 4};
            // A table turns with its chairs: they go round it, each still facing it
            const chairs = this.catalog.items[it.k].cat === 'table' ? this.chairsOf(it) : [];
            const moved = chairs.length ? this.turnChairs(it, next, chairs) : [];
            const others = this.draft;
            this.draft = this.draft.filter(d => !chairs.includes(d) && d !== it); // the table and its chairs do not block each other
            const used = new Set(this.world.footprint(next, this.catalog.items).tiles.map(([x, z]) => x + ',' + z));
            let ok = this.fits(next);
            for (const c of moved) {
                const key = c.x + ',' + c.z;
                if (!ok || used.has(key) || !this.fits(c)) ok = false;
                used.add(key);
            }
            this.draft = others;
            if (!ok) return this.hooks.toast?.('no_room');
            it.r = next.r;
            this.world.placeObject(this.world.items.get(it.id), it, this.catalog.items);
            chairs.forEach((c, k) => {
                Object.assign(c, {x: moved[k].x, z: moved[k].z, r: moved[k].r});
                this.world.placeObject(this.world.items.get(c.id), c, this.catalog.items);
            });
            this.highlight();
        }
        this.hooks.changed();
    }

    hover(e, dragging = false) {
        if (this.dragging && !this.dragging.started) {
            if (Math.hypot(e.clientX - this.dragging.startX, e.clientY - this.dragging.startY) < 6) return;
            this.dragging.started = true;
            this.startPlacing(this.dragging.it.k, this.dragging.it);
        }
        if (!this.placing) {
            this.hoverItem(e);
            return;
        }
        const t = this.world.tileAt(e);
        if (!t) return;
        if (this.catalog.items[this.placing.it.k].cat === 'window') return this.hoverWindow(t);
        const {w, d} = this.world.footprint(this.placing.it, this.catalog.items);
        const x = Math.round(t.px - w / 2), z = Math.round(t.pz - d / 2);
        if (x === this.placing.it.x && z === this.placing.it.z) return;
        this.placing.it.x = x;
        this.placing.it.z = z;
        this.updateGhost();
    }

    /** A window follows the pointer along the nearest outer wall, turned to look out of it. */
    hoverWindow(t) {
        const it = this.placing.it, rc = this.world.floorRect(this.level);
        if (!rc) return;
        const px = Math.min(Math.max(t.px, rc.x0), rc.x0 + rc.w), pz = Math.min(Math.max(t.pz, 0), rc.d);
        const dist = [pz, px - rc.x0, rc.d - pz, rc.x0 + rc.w - px];
        const r = dist.indexOf(Math.min(...dist));
        const len = this.catalog.items[it.k].w;
        let x, z;
        if (r === 0 || r === 2) {
            x = Math.min(Math.max(Math.round(px - len / 2), rc.x0), rc.x0 + rc.w - len);
            z = r === 0 ? 0 : rc.d - 1;
        } else {
            z = Math.min(Math.max(Math.round(pz - len / 2), 0), rc.d - len);
            x = r === 1 ? rc.x0 : rc.x0 + rc.w - 1;
        }
        if (x === it.x && z === it.z && r === it.r) return;
        Object.assign(it, {x, z, r});
        this.updateGhost();
    }

    tap(e) {
        if (this.doorMove) { // the new place of the door
            const t = this.world.tileAt(e);
            if (!t || t.z !== this.D - 1 || t.x === this.world.door) return;
            if (!this.doorSpotOk(t.x)) return this.hooks.toast?.('door_spot');
            this.stopDoorMove();
            this.hooks.moveDoor?.(t.x);
            return;
        }
        if (this.placing) {
            if (e.pointerType !== 'mouse') return; // touch: drag the piece, then "Place here"
            this.hover(e);
            this.confirm();
            return;
        }
        const id = this.world.itemAt(e);
        this.select(id != null ? this.draft.find(d => d.id === id && (d.f || 0) === this.level) || null : null);
    }

    /** Puts the piece being placed where it is (if it fits). */
    confirm() {
        if (!this.placing) return;
        if (!this.placing.ok) return this.hooks.toast?.('no_room');
        this.place();
    }

    place() {
        const p = this.placing;
        const it = {...p.it};
        this.draft.push(it);
        this.world.addItem(it);
        this.world.root.remove(p.ghost);
        for (const c of p.attached || []) { // the table's chairs land around it
            const ch = {...c.it};
            this.draft.push(ch);
            this.world.addItem(ch);
            this.world.root.remove(c.ghost);
        }
        this.marks.clear();
        this.placing = null;
        this.hooks.placed?.(it);
        // Another of the same right away when "place several" is on for its category; otherwise it stays selected
        const cat = this.catalog.items[it.k].cat;
        if (!p.moving && it.st && this.repeatFor(cat) && this.inStorage(it.k) > 0) this.startPlacing(it.k, null, it.r, true);
        else if (!p.moving && !it.st && this.repeatFor(cat) && this.cost().net + this.catalog.items[it.k].price <= this.hooks.coins()) this.startPlacing(it.k, null, it.r);
        else this.select(it);
        this.hooks.changed();
    }

    /**
     * Where a table's chairs go when the table turns from `from` to `to` (a quarter turn each step, round the table's
     * middle): copies of the chairs with their new x, z and r.
     */
    turnChairs(from, to, chairs) {
        const f0 = this.world.footprint(from, this.catalog.items), f1 = this.world.footprint(to, this.catalog.items);
        const steps = ((to.r - from.r) + 4) % 4;
        return chairs.map(c => {
            let cx = c.x + 0.5 - (from.x + f0.w / 2), cz = c.z + 0.5 - (from.z + f0.d / 2), cr = c.r;
            for (let k = 0; k < steps; k++) [cx, cz, cr] = [cz, -cx, (cr + 1) % 4];
            return {...c, x: Math.round(to.x + f1.w / 2 + cx - 0.5), z: Math.round(to.z + f1.d / 2 + cz - 0.5), r: cr};
        });
    }

    /** The chairs turned towards a table (on its floor): they move with it. */
    chairsOf(table) {
        const tiles = new Set(this.world.footprint(table, this.catalog.items).tiles.map(([x, z]) => x + ',' + z));
        const D4 = [[0, 1], [1, 0], [0, -1], [-1, 0]];
        return this.draft.filter(c => this.catalog.items[c.k]?.cat === 'chair' && (c.f || 0) === (table.f || 0) && tiles.has((c.x + D4[c.r][0]) + ',' + (c.z + D4[c.r][1])));
    }

    /** A see-through copy of a piece, for placing. */
    ghostOf(key) {
        const g = furnitureModel(key);
        g.traverse(o => {
            if (o.isMesh) {
                o.material = o.material.clone();
                o.material.transparent = true;
                o.material.opacity = 0.6;
                o.castShadow = false;
            }
        });
        return g;
    }

    /** A copy of the selected piece, to be placed (same turn). */
    copySelected() {
        if (this.selected) this.startPlacing(this.selected.k, null, this.selected.r);
    }

    /** Hovering a piece (not placing): a soft highlight, so it is clear what a click would pick. */
    hoverItem(e) {
        const id = this.world.itemAt(e);
        if (id === this.hoverId) return;
        this.hoverId = id;
        this.world.canvas.style.cursor = id != null ? 'grab' : '';
        if (!this.hoverMarks) {
            this.hoverMarks = new THREE.Group();
            this.world.root.add(this.hoverMarks);
        }
        this.hoverMarks.clear();
        const it = id != null ? this.draft.find(d => d.id === id && (d.f || 0) === this.level) : null;
        if (it && it !== this.selected) for (const [x, z] of this.world.footprint(it, this.catalog.items).tiles) this.hoverMarks.add(this.tileMark(x, z, '#FFFFFF', 0.3));
    }

    /** The little label next to the piece being placed: its name, price, and whether it fits there. */
    updateLabel() {
        const p = this.placing;
        if (!p) {
            this.label && (this.label.style.display = 'none');
            return;
        }
        if (!this.label) {
            this.label = document.createElement('div');
            this.label.className = 'place-label';
            this.world.overlay.appendChild(this.label);
        }
        const pos = p.ghost.position.clone().setY(1.7);
        const s = this.world.project(pos);
        const def = this.catalog.items[p.it.k];
        const html = `<b>${this.hooks.name(p.it.k)}</b><span>${p.moving ? '' : '<i class="coin"></i>' + def.price}</span><em class="${p.ok ? 'ok' : 'no'}">${this.hooks.text(p.ok ? 'fits' : p.reason || 'no_room')}</em>`;
        if (this.label.dataset.html !== html) {
            this.label.innerHTML = html;
            this.label.dataset.html = html;
        }
        this.label.style.display = s.visible ? '' : 'none';
        this.label.style.transform = `translate(${s.x}px, ${s.y}px)`;
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

    /** Put the selected piece away in storage (not sold: it can be placed again for free). */
    storeSelected() {
        const it = this.selected;
        if (!it) return;
        if (it.id > 0 && this.original.has(it.id)) this.stored.add(it.id); // a piece bought before: into storage
        // A piece taken out of storage in this session simply goes back; a piece just bought from the shop is not bought
        this.draft = this.draft.filter(d => d.id !== it.id);
        this.world.removeItem(it.id);
        this.select(null);
        this.hooks.changed();
    }

    sellSelected() {
        if (!this.selected) return;
        if (this.selected.st) return this.storeSelected(); // out of storage in this session: it just goes back
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
