/**
 * Akam 3D background (Elementor widget "پس‌زمینه سه‌بعدی").
 *
 * Source file; the theme loads the bundled build assets/js/three-background.min.js
 * (see scripts/build-three.sh). Only the Three.js modules imported here end up in it.
 *
 * Presets: network (connected particles), waves (dot ocean), orbs (floating glossy
 * shapes), globe (dotted rotating sphere).
 *
 * Performance: renders only while visible on screen and the tab is active, caps the
 * pixel ratio, uses fewer particles on small screens, and draws a single still frame
 * for visitors who prefer reduced motion.
 */
import {
	AdditiveBlending,
	AmbientLight,
	BufferAttribute,
	BufferGeometry,
	CanvasTexture,
	Color,
	DirectionalLight,
	Group,
	IcosahedronGeometry,
	LineBasicMaterial,
	LineSegments,
	Mesh,
	MeshStandardMaterial,
	PerspectiveCamera,
	PointLight,
	Points,
	PointsMaterial,
	Scene,
	SRGBColorSpace,
	TorusKnotGeometry,
	WebGLRenderer,
} from 'three';

const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const instances = new WeakMap();

function cssColor(name, fallback) {
	const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
	return value || fallback;
}

function toColor(value, fallback) {
	const color = new Color();
	try {
		color.setStyle(value || fallback);
	} catch (error) {
		color.setStyle(fallback);
	}
	return color;
}

/** Soft round sprite so points render as glowing dots instead of squares. */
function dotTexture() {
	const size = 64;
	const canvas = document.createElement('canvas');
	canvas.width = size;
	canvas.height = size;
	const ctx = canvas.getContext('2d');
	const gradient = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
	gradient.addColorStop(0, 'rgba(255,255,255,1)');
	gradient.addColorStop(0.35, 'rgba(255,255,255,0.8)');
	gradient.addColorStop(1, 'rgba(255,255,255,0)');
	ctx.fillStyle = gradient;
	ctx.fillRect(0, 0, size, size);
	const texture = new CanvasTexture(canvas);
	texture.colorSpace = SRGBColorSpace;
	return texture;
}

/* ------------------------------------------------------------------ presets */

function createNetwork(ctx) {
	const { scene, colors, density, small } = ctx;
	const count = Math.round((small ? 35 : 60) + density * (small ? 0.6 : 1.4));
	const bounds = { x: 26, y: 15, z: 8 };
	const positions = new Float32Array(count * 3);
	const velocities = new Float32Array(count * 3);
	const pointColors = new Float32Array(count * 3);

	for (let i = 0; i < count; i++) {
		positions[i * 3] = (Math.random() - 0.5) * bounds.x * 2;
		positions[i * 3 + 1] = (Math.random() - 0.5) * bounds.y * 2;
		positions[i * 3 + 2] = (Math.random() - 0.5) * bounds.z * 2;
		velocities[i * 3] = (Math.random() - 0.5) * 0.04;
		velocities[i * 3 + 1] = (Math.random() - 0.5) * 0.04;
		velocities[i * 3 + 2] = (Math.random() - 0.5) * 0.02;
		const mixed = colors[0].clone().lerp(colors[1], Math.random());
		pointColors.set([mixed.r, mixed.g, mixed.b], i * 3);
	}

	const pointGeometry = new BufferGeometry();
	pointGeometry.setAttribute('position', new BufferAttribute(positions, 3));
	pointGeometry.setAttribute('color', new BufferAttribute(pointColors, 3));
	const points = new Points(pointGeometry, new PointsMaterial({
		size: 0.55,
		map: ctx.dot,
		vertexColors: true,
		transparent: true,
		depthWrite: false,
		opacity: ctx.opacity,
	}));

	const maxSegments = (count * (count - 1)) / 2;
	const linePositions = new Float32Array(maxSegments * 6);
	const lineColors = new Float32Array(maxSegments * 6);
	const lineGeometry = new BufferGeometry();
	lineGeometry.setAttribute('position', new BufferAttribute(linePositions, 3));
	lineGeometry.setAttribute('color', new BufferAttribute(lineColors, 3));
	const lines = new LineSegments(lineGeometry, new LineBasicMaterial({
		vertexColors: true,
		transparent: true,
		depthWrite: false,
		blending: AdditiveBlending,
		opacity: 0.55 * ctx.opacity,
	}));

	scene.add(points, lines);
	ctx.camera.position.set(0, 0, 32);

	const maxDistance = 7;

	return (dt) => {
		for (let i = 0; i < count; i++) {
			for (let axis = 0; axis < 3; axis++) {
				const index = i * 3 + axis;
				const limit = axis === 0 ? bounds.x : axis === 1 ? bounds.y : bounds.z;
				positions[index] += velocities[index] * dt * ctx.speed;
				if (positions[index] > limit || positions[index] < -limit) {
					velocities[index] *= -1;
				}
			}
		}
		pointGeometry.attributes.position.needsUpdate = true;

		let segment = 0;
		for (let i = 0; i < count; i++) {
			for (let j = i + 1; j < count; j++) {
				const dx = positions[i * 3] - positions[j * 3];
				const dy = positions[i * 3 + 1] - positions[j * 3 + 1];
				const dz = positions[i * 3 + 2] - positions[j * 3 + 2];
				const distance = Math.sqrt(dx * dx + dy * dy + dz * dz);
				if (distance > maxDistance) {
					continue;
				}
				// Fade lines out with distance (additive blending: darker = more transparent).
				const strength = 1 - distance / maxDistance;
				linePositions.set([positions[i * 3], positions[i * 3 + 1], positions[i * 3 + 2], positions[j * 3], positions[j * 3 + 1], positions[j * 3 + 2]], segment * 6);
				lineColors.set([
					pointColors[i * 3] * strength, pointColors[i * 3 + 1] * strength, pointColors[i * 3 + 2] * strength,
					pointColors[j * 3] * strength, pointColors[j * 3 + 1] * strength, pointColors[j * 3 + 2] * strength,
				], segment * 6);
				segment++;
			}
		}
		lineGeometry.setDrawRange(0, segment * 2);
		lineGeometry.attributes.position.needsUpdate = true;
		lineGeometry.attributes.color.needsUpdate = true;
	};
}

function createWaves(ctx) {
	const { scene, colors, density, small } = ctx;
	const cols = Math.round((small ? 40 : 70) + density * (small ? 0.2 : 0.5));
	const rows = Math.round(cols * 0.5);
	const spacing = 1;
	const count = cols * rows;
	const positions = new Float32Array(count * 3);
	const pointColors = new Float32Array(count * 3);

	for (let x = 0; x < cols; x++) {
		for (let z = 0; z < rows; z++) {
			const i = x * rows + z;
			positions[i * 3] = (x - cols / 2) * spacing;
			positions[i * 3 + 2] = (z - rows / 2) * spacing;
			const mixed = colors[0].clone().lerp(colors[1], x / cols);
			pointColors.set([mixed.r, mixed.g, mixed.b], i * 3);
		}
	}

	const geometry = new BufferGeometry();
	geometry.setAttribute('position', new BufferAttribute(positions, 3));
	geometry.setAttribute('color', new BufferAttribute(pointColors, 3));
	scene.add(new Points(geometry, new PointsMaterial({
		size: 0.42,
		map: ctx.dot,
		vertexColors: true,
		transparent: true,
		depthWrite: false,
		opacity: ctx.opacity,
	})));

	ctx.camera.position.set(0, 11, 26);
	ctx.camera.lookAt(0, -2, 0);

	let time = 0;
	return (dt) => {
		time += dt * 0.02 * ctx.speed;
		for (let x = 0; x < cols; x++) {
			for (let z = 0; z < rows; z++) {
				const i = x * rows + z;
				positions[i * 3 + 1] = Math.sin(x * 0.22 + time) * 1.4 + Math.cos(z * 0.3 + time * 0.8) * 1.1;
			}
		}
		geometry.attributes.position.needsUpdate = true;
	};
}

function createOrbs(ctx) {
	const { scene, colors, density, small } = ctx;
	const count = Math.max(4, Math.round((small ? 4 : 5) + density / 20));
	const group = new Group();
	const items = [];

	scene.add(new AmbientLight(0xffffff, 1.1));
	const key = new DirectionalLight(0xffffff, 2.2);
	key.position.set(8, 12, 14);
	scene.add(key);
	const rim = new PointLight(colors[1].getHex(), 60, 60);
	rim.position.set(-12, -6, 8);
	scene.add(rim);

	const sphere = new IcosahedronGeometry(1, 6);
	const knot = new TorusKnotGeometry(0.8, 0.26, 160, 24);

	// Shapes sit around the edges (the middle is usually text) and keep apart from each other.
	// Sizes are relative to what the camera actually sees for this section's aspect ratio.
	// A narrow lens from further away keeps spheres round near the edges of wide sections.
	ctx.camera.fov = 26;
	ctx.camera.updateProjectionMatrix();
	const distance = 64;
	const visibleHeight = 2 * Math.tan((ctx.camera.fov * Math.PI) / 360) * distance;
	const visibleWidth = visibleHeight * ctx.camera.aspect;
	const placed = [];
	function findSpot(scale) {
		let best = null;
		for (let attempt = 0; attempt < 80; attempt++) {
			const x = (Math.random() - 0.5) * visibleWidth * 0.9;
			const y = (Math.random() - 0.5) * visibleHeight * 0.85;
			if (Math.abs(x) < visibleWidth * 0.3 && Math.abs(y) < visibleHeight * 0.22) {
				continue;
			}
			const clear = placed.every((other) => Math.hypot(other.x - x, other.y - y) > (other.scale + scale) * 1.3);
			best = { x, y };
			if (clear) {
				break;
			}
		}
		return best || { x: 14, y: 6 };
	}

	for (let i = 0; i < count; i++) {
		const color = colors[0].clone().lerp(colors[1], i / (count - 1));
		const mesh = new Mesh(i === 1 ? knot : sphere, new MeshStandardMaterial({
			color,
			roughness: 0.22,
			metalness: 0.15,
			transparent: ctx.opacity < 1,
			opacity: ctx.opacity,
		}));
		const scale = (1 + Math.random() * 1.4) * Math.min(1.3, Math.max(0.7, visibleWidth / 50));
		const spot = findSpot(scale);
		placed.push({ x: spot.x, y: spot.y, scale });
		mesh.scale.setScalar(scale);
		mesh.position.set(spot.x, spot.y, (Math.random() - 0.5) * 6 - 4);
		mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0);
		items.push({ mesh, baseY: mesh.position.y, phase: Math.random() * Math.PI * 2, spin: 0.002 + Math.random() * 0.004 });
		group.add(mesh);
	}

	scene.add(group);
	ctx.camera.position.set(0, 0, 60);

	let time = 0;
	return (dt) => {
		time += dt * 0.016 * ctx.speed;
		items.forEach((item) => {
			item.mesh.position.y = item.baseY + Math.sin(time + item.phase) * 1.2;
			item.mesh.rotation.x += item.spin * dt * ctx.speed;
			item.mesh.rotation.y += item.spin * 1.3 * dt * ctx.speed;
		});
	};
}

function createGlobe(ctx) {
	const { scene, colors, density, small } = ctx;
	const count = Math.round((small ? 700 : 1100) + density * (small ? 8 : 18));
	const radius = 11;
	const positions = new Float32Array(count * 3);
	const pointColors = new Float32Array(count * 3);
	const golden = Math.PI * (3 - Math.sqrt(5));

	// Fibonacci sphere: evenly spread dots.
	for (let i = 0; i < count; i++) {
		const y = 1 - (i / (count - 1)) * 2;
		const r = Math.sqrt(1 - y * y);
		const theta = golden * i;
		positions.set([Math.cos(theta) * r * radius, y * radius, Math.sin(theta) * r * radius], i * 3);
		const mixed = colors[0].clone().lerp(colors[1], (y + 1) / 2);
		pointColors.set([mixed.r, mixed.g, mixed.b], i * 3);
	}

	const geometry = new BufferGeometry();
	geometry.setAttribute('position', new BufferAttribute(positions, 3));
	geometry.setAttribute('color', new BufferAttribute(pointColors, 3));
	const globe = new Points(geometry, new PointsMaterial({
		size: 0.46,
		map: ctx.dot,
		vertexColors: true,
		transparent: true,
		depthWrite: false,
		opacity: ctx.opacity,
	}));

	const ringCount = 220;
	const ringPositions = new Float32Array(ringCount * 3);
	for (let i = 0; i < ringCount; i++) {
		const angle = (i / ringCount) * Math.PI * 2;
		ringPositions.set([Math.cos(angle) * radius * 1.45, 0, Math.sin(angle) * radius * 1.45], i * 3);
	}
	const ringGeometry = new BufferGeometry();
	ringGeometry.setAttribute('position', new BufferAttribute(ringPositions, 3));
	const ring = new Points(ringGeometry, new PointsMaterial({
		size: 0.38,
		map: ctx.dot,
		color: colors[1],
		transparent: true,
		depthWrite: false,
		opacity: 0.7 * ctx.opacity,
	}));
	ring.rotation.x = 1.2;
	ring.rotation.z = 0.35;

	const group = new Group();
	group.add(globe, ring);
	group.rotation.z = 0.25;
	scene.add(group);
	ctx.camera.position.set(0, 0, 34);

	return (dt) => {
		globe.rotation.y += 0.0025 * dt * ctx.speed;
		ring.rotation.y -= 0.0015 * dt * ctx.speed;
	};
}

const PRESETS = { network: createNetwork, waves: createWaves, orbs: createOrbs, globe: createGlobe };

/* ------------------------------------------------------------------- runtime */

function webglAvailable() {
	try {
		const canvas = document.createElement('canvas');
		return !!(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
	} catch (error) {
		return false;
	}
}

function init(element) {
	if (!element || instances.has(element) || !webglAvailable()) {
		return;
	}

	let config = {};
	try {
		config = JSON.parse(element.getAttribute('data-webmz-3d') || '{}');
	} catch (error) {
		config = {};
	}

	const factory = PRESETS[config.preset] || PRESETS.network;
	const small = window.innerWidth < 768;
	const renderer = new WebGLRenderer({ antialias: !small, alpha: true, powerPreference: 'low-power' });
	renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, small ? 1.5 : 1.75));
	renderer.outputColorSpace = SRGBColorSpace;
	renderer.domElement.className = 'webmz-3d__canvas';
	renderer.domElement.setAttribute('aria-hidden', 'true');
	element.appendChild(renderer.domElement);

	const scene = new Scene();
	const camera = new PerspectiveCamera(50, 1, 0.1, 200);
	const primary = cssColor('--webmz-color-primary', '#0878f9');
	const ctx = {
		scene,
		camera,
		small,
		dot: dotTexture(),
		colors: [toColor(config.color1, primary), toColor(config.color2, '#8b5cf6')],
		density: Math.max(0, Math.min(100, Number(config.density) || 50)),
		speed: Math.max(0, Math.min(3, Number(config.speed ?? 1))),
		opacity: Math.max(0.05, Math.min(1, Number(config.opacity) || 1)),
	};
	camera.aspect = (element.clientWidth || 1) / (element.clientHeight || 1);
	camera.updateProjectionMatrix();
	const update = factory(ctx);
	const baseCamera = camera.position.clone();
	const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
	let visible = false;
	let frame = 0;
	let last = 0;

	function resize() {
		const width = element.clientWidth || 1;
		const height = element.clientHeight || 1;
		renderer.setSize(width, height, false);
		camera.aspect = width / height;
		camera.updateProjectionMatrix();
	}

	function render(now) {
		frame = 0;
		// dt is in 60fps frames so presets animate at the same speed on any refresh rate.
		const dt = last ? Math.min((now - last) / 16.67, 3) : 1;
		last = now;

		pointer.x += (pointer.tx - pointer.x) * 0.05;
		pointer.y += (pointer.ty - pointer.y) * 0.05;
		camera.position.x = baseCamera.x + pointer.x * 3;
		camera.position.y = baseCamera.y + pointer.y * 2;
		camera.lookAt(0, config.preset === 'waves' ? -2 : 0, 0);

		update(dt);
		renderer.render(scene, camera);

		if (visible && !document.hidden) {
			frame = window.requestAnimationFrame(render);
		}
	}

	function start() {
		if (!frame && !reducedMotion) {
			last = 0;
			frame = window.requestAnimationFrame(render);
		}
	}

	function stop() {
		if (frame) {
			window.cancelAnimationFrame(frame);
			frame = 0;
		}
	}

	resize();
	if (reducedMotion) {
		// One still frame; no animation loop.
		update(1);
		renderer.render(scene, camera);
	}

	const resizeObserver = new ResizeObserver(() => {
		resize();
		if (reducedMotion || !visible) {
			renderer.render(scene, camera);
		}
	});
	resizeObserver.observe(element);

	const visibilityObserver = new IntersectionObserver((entries) => {
		visible = entries[0].isIntersecting;
		if (visible) {
			start();
		} else {
			stop();
		}
	}, { rootMargin: '100px' });
	visibilityObserver.observe(element);

	document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
			stop();
		} else if (visible) {
			start();
		}
	});

	if (config.mouse !== false && !reducedMotion) {
		window.addEventListener('pointermove', (event) => {
			const rect = element.getBoundingClientRect();
			pointer.tx = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
			pointer.ty = -((event.clientY - rect.top) / rect.height - 0.5) * 2;
		}, { passive: true });
	}

	element.classList.add('is-ready');
	instances.set(element, { renderer, resizeObserver, visibilityObserver });
}

/** In "fill section" mode the widget sits behind the rest of its container. */
function prepareHost(element) {
	const widget = element.closest('.elementor-widget');
	if (!widget || !widget.classList.contains('webmz-3d-fill-yes')) {
		return;
	}
	const host = widget.parentElement && widget.parentElement.classList.contains('e-con-inner')
		? widget.parentElement.parentElement
		: widget.parentElement;
	if (!host) {
		return;
	}
	host.classList.add('webmz-3d-host');
	if (getComputedStyle(host).position === 'static') {
		host.style.position = 'relative';
	}
}

function initAll(scope) {
	(scope || document).querySelectorAll('[data-webmz-3d]').forEach((element) => {
		prepareHost(element);
		init(element);
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => initAll(document));
} else {
	initAll(document);
}

if (window.jQuery) {
	window.jQuery(window).on('elementor/frontend/init', () => {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-three-background.default', ($scope) => initAll($scope[0]));
		}
	});
}
