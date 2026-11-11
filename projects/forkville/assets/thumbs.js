/*
 * Forkville — shop thumbnails: each item's real 3D model, rendered once into a small picture (cached).
 */
import * as THREE from '../vendor/three.module.min.js';
import {furnitureModel, personModel} from './models.js';

let renderer = null, scene = null, camera = null;
const cache = new Map();

function setup() {
    const canvas = document.createElement('canvas');
    renderer = new THREE.WebGLRenderer({canvas, antialias: true, alpha: true, preserveDrawingBuffer: true});
    renderer.setSize(128, 128, false);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    scene = new THREE.Scene();
    scene.add(new THREE.HemisphereLight('#FFFFFF', '#B8A58C', 1.6));
    const sun = new THREE.DirectionalLight('#FFF1DC', 1.8);
    sun.position.set(3, 5, 4);
    scene.add(sun);
    camera = new THREE.PerspectiveCamera(30, 1, 0.1, 50);
}

/** A data URL with the item's model seen from three quarters above. */
export function thumbFor(key) {
    if (cache.has(key)) return cache.get(key);
    try {
        if (!renderer) setup();
        const model = furnitureModel(key);
        scene.add(model);
        const box = new THREE.Box3().setFromObject(model);
        const size = box.getSize(new THREE.Vector3());
        const center = box.getCenter(new THREE.Vector3());
        const r = Math.max(size.x, size.y * 1.1, size.z) * 0.95 + 0.25;
        camera.position.set(center.x + r * 1.6, center.y + r * 1.25, center.z + r * 1.9);
        camera.lookAt(center);
        renderer.render(scene, camera);
        const url = renderer.domElement.toDataURL('image/png');
        scene.remove(model);
        cache.set(key, url);
        return url;
    } catch (e) {
        return '';
    }
}

/** A portrait of a worker with a given look (not cached: it changes while choosing). */
export function portrait(role, color, seed, look) {
    try {
        if (!renderer) setup();
        const model = personModel(role, color, seed, look);
        model.rotation.y = 0.35;
        scene.add(model);
        camera.position.set(0.55, 1.35, 2.2);
        camera.lookAt(0, 0.85, 0);
        renderer.render(scene, camera);
        const url = renderer.domElement.toDataURL('image/png');
        scene.remove(model);
        return url;
    } catch (e) {
        return '';
    }
}
