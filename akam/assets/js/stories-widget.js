( function () {
	'use strict';

	var viewer = null;
	var touchBound = false;
	var state = {
		root: null,
		data: null,
		groupIndex: 0,
		slideIndex: 0,
		timer: null,
		paused: false,
		holdPaused: false,
		muted: true,
		progressStart: 0,
		elapsed: 0,
		isClosing: false,
		icons: {},
		videoCleanup: null,
		boxId: 0
	};

	var CLOSE_MS = 320;
	var HOLD_MS = 200;
	var SWIPE_PX = 56;
	var TAP_EDGE = 0.34;
	var SEEN_STORAGE_KEY = 'webmz_stories_seen';

	var ICON_FALLBACKS = {
		play: '&#9654;',
		pause: '&#10074;&#10074;',
		mute: '&#9834;',
		unmute: '&#9835;',
		nav_prev: '&#8249;',
		nav_next: '&#8250;',
		close: '&times;'
	};

	var touchTracker = {
		pointerId: null,
		startX: 0,
		startY: 0,
		startTime: 0,
		holdTimer: null,
		isHolding: false,
		moved: false
	};

	function isRtl() {
		return document.documentElement.getAttribute( 'dir' ) === 'rtl';
	}

	function preventMediaDrag( element ) {
		if ( ! element || element.dataset.webmzStoriesNoDrag ) {
			return;
		}

		element.dataset.webmzStoriesNoDrag = '1';
		element.addEventListener(
			'dragstart',
			function ( event ) {
				var tag = event.target && event.target.tagName ? event.target.tagName.toLowerCase() : '';

				if ( tag === 'img' || tag === 'video' ) {
					event.preventDefault();
				}
			}
		);
	}

	function getSeenMap() {
		try {
			return JSON.parse( localStorage.getItem( SEEN_STORAGE_KEY ) || '{}' );
		} catch ( error ) {
			return {};
		}
	}

	function saveSeenMap( map ) {
		try {
			localStorage.setItem( SEEN_STORAGE_KEY, JSON.stringify( map ) );
		} catch ( error ) {}
	}

	function getSeenGroups( boxId ) {
		var map = getSeenMap();
		var key = String( boxId );
		return Array.isArray( map[ key ] ) ? map[ key ].map( String ) : [];
	}

	function markGroupSeen( boxId, groupId ) {
		if ( ! boxId || ! groupId ) {
			return;
		}

		var map = getSeenMap();
		var key = String( boxId );
		var id  = String( groupId );

		if ( ! Array.isArray( map[ key ] ) ) {
			map[ key ] = [];
		}

		if ( map[ key ].indexOf( id ) !== -1 ) {
			return;
		}

		map[ key ].push( id );
		saveSeenMap( map );

		if ( state.root ) {
			applySeenClasses( state.root, boxId );
		}
	}

	function applySeenClasses( root, boxId ) {
		if ( ! root || ! boxId ) {
			return;
		}

		var seen = getSeenGroups( boxId );
		root.querySelectorAll( '[data-story-group-id]' ).forEach( function ( item ) {
			var groupId = item.getAttribute( 'data-story-group-id' );
			item.classList.toggle( 'is-seen', seen.indexOf( String( groupId ) ) !== -1 );
		} );
	}

	function markCurrentGroupSeen() {
		var group = getCurrentGroup();
		if ( group && state.boxId ) {
			markGroupSeen( state.boxId, group.id );
		}
	}

	function navigateFromHorizontalSwipe( deltaX ) {
		if ( isRtl() ) {
			if ( deltaX > 0 ) {
				nextSlide();
			} else {
				prevSlide();
			}
			return;
		}

		if ( deltaX > 0 ) {
			prevSlide();
		} else {
			nextSlide();
		}
	}

	function navigateFromTapZone( ratio ) {
		var rtl = isRtl();

		if ( ratio < TAP_EDGE ) {
			if ( rtl ) {
				nextSlide();
			} else {
				prevSlide();
			}
			return;
		}

		if ( ratio > 1 - TAP_EDGE ) {
			if ( rtl ) {
				prevSlide();
			} else {
				nextSlide();
			}
		}
	}

	function ensureViewer() {
		if ( viewer ) {
			return viewer;
		}

		viewer = document.createElement( 'div' );
		viewer.className = 'webmz-stories-viewer';
		viewer.innerHTML =
			'<div class="webmz-stories-viewer__backdrop" aria-hidden="true">' +
				'<div class="webmz-stories-viewer__backdrop-bg" data-webmz-story-backdrop-bg></div>' +
				'<div class="webmz-stories-viewer__backdrop-tint"></div>' +
			'</div>' +
			'<button type="button" class="webmz-stories-viewer__close" data-webmz-story-close data-webmz-story-icon-btn="close" aria-label="بستن"></button>' +
			'<div class="webmz-stories-viewer__shell">' +
				'<button type="button" class="webmz-stories-viewer__nav" data-webmz-story-prev data-webmz-story-icon-btn="nav_prev" aria-label="قبلی"></button>' +
				'<div class="webmz-stories-viewer__stage" data-webmz-story-stage>' +
					'<div class="webmz-stories-viewer__progress" data-webmz-story-progress></div>' +
					'<div class="webmz-stories-viewer__topbar">' +
						'<div class="webmz-stories-viewer__title" data-webmz-story-title></div>' +
						'<div class="webmz-stories-viewer__controls">' +
							'<button type="button" data-webmz-story-pause data-webmz-story-icon-btn="pause" aria-label="توقف"></button>' +
							'<button type="button" data-webmz-story-mute data-webmz-story-icon-btn="mute" aria-label="صدا" hidden></button>' +
						'</div>' +
					'</div>' +
					'<div class="webmz-stories-viewer__media" data-webmz-story-media></div>' +
					'<div class="webmz-stories-viewer__cta" data-webmz-story-cta hidden></div>' +
				'</div>' +
				'<div class="webmz-stories-viewer__preview" data-webmz-story-preview hidden></div>' +
				'<button type="button" class="webmz-stories-viewer__nav" data-webmz-story-next data-webmz-story-icon-btn="nav_next" aria-label="بعدی"></button>' +
			'</div>';

		document.body.appendChild( viewer );
		preventMediaDrag( viewer );

		viewer.addEventListener( 'click', function ( event ) {
			var target = event.target.closest( 'button' );
			if ( ! target || ! viewer.contains( target ) ) {
				return;
			}

			if ( target.matches( '[data-webmz-story-close]' ) ) {
				event.preventDefault();
				closeViewer();
				return;
			}

			if ( target.matches( '[data-webmz-story-prev]' ) ) {
				event.preventDefault();
				prevSlide();
				return;
			}

			if ( target.matches( '[data-webmz-story-next]' ) ) {
				event.preventDefault();
				nextSlide();
				return;
			}

			if ( target.matches( '[data-webmz-story-pause]' ) ) {
				event.preventDefault();
				togglePause( false );
				return;
			}

			if ( target.matches( '[data-webmz-story-mute]' ) ) {
				event.preventDefault();
				toggleMute();
			}
		} );

		viewer.addEventListener( 'click', function ( event ) {
			var target = event.target;

			if ( target && target.closest && target.closest( '.webmz-stories-viewer__backdrop' ) ) {
				closeViewer();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( ! viewer.classList.contains( 'is-open' ) || state.isClosing ) {
				return;
			}

			if ( event.key === 'Escape' ) {
				closeViewer();
			} else if ( event.key === 'ArrowLeft' ) {
				nextSlide();
			} else if ( event.key === 'ArrowRight' ) {
				prevSlide();
			} else if ( event.key === ' ' ) {
				event.preventDefault();
				togglePause( false );
			}
		} );

		return viewer;
	}

	function bindTouchNavigation() {
		if ( touchBound ) {
			return;
		}

		var stage = viewer.querySelector( '[data-webmz-story-stage]' );
		if ( ! stage ) {
			return;
		}

		touchBound = true;

		stage.addEventListener( 'pointerdown', onPointerDown );
		stage.addEventListener( 'pointermove', onPointerMove );
		stage.addEventListener( 'pointerup', onPointerUp );
		stage.addEventListener( 'pointercancel', onPointerUp );
		stage.addEventListener( 'pointerleave', onPointerLeave );
	}

	function isInteractiveTarget( target ) {
		return !! target.closest( '[data-webmz-story-cta] a, .webmz-stories-viewer__controls button, .webmz-stories-viewer__topbar' );
	}

	function onPointerDown( event ) {
		if ( ! viewer.classList.contains( 'is-open' ) || state.isClosing || isInteractiveTarget( event.target ) ) {
			return;
		}

		if ( event.pointerType === 'mouse' && event.button !== 0 ) {
			return;
		}

		touchTracker.pointerId = event.pointerId;
		touchTracker.startX = event.clientX;
		touchTracker.startY = event.clientY;
		touchTracker.startTime = Date.now();
		touchTracker.moved = false;
		touchTracker.isHolding = false;

		if ( event.currentTarget.setPointerCapture ) {
			try {
				event.currentTarget.setPointerCapture( event.pointerId );
			} catch ( error ) {}
		}

		clearTimeout( touchTracker.holdTimer );
		touchTracker.holdTimer = window.setTimeout( function () {
			if ( touchTracker.moved ) {
				return;
			}
			touchTracker.isHolding = true;
			holdPause();
		}, HOLD_MS );
	}

	function onPointerMove( event ) {
		if ( touchTracker.pointerId !== event.pointerId ) {
			return;
		}

		if ( Math.abs( event.clientX - touchTracker.startX ) > 8 || Math.abs( event.clientY - touchTracker.startY ) > 8 ) {
			touchTracker.moved = true;
			clearTimeout( touchTracker.holdTimer );
		}
	}

	function onPointerLeave( event ) {
		if ( touchTracker.isHolding ) {
			onPointerUp( event );
		}
	}

	function onPointerUp( event ) {
		if ( touchTracker.pointerId !== event.pointerId ) {
			return;
		}

		clearTimeout( touchTracker.holdTimer );

		if ( touchTracker.isHolding ) {
			touchTracker.isHolding = false;
			holdResume();
			resetTouchTracker();
			return;
		}

		var elapsed = Date.now() - touchTracker.startTime;
		var deltaX = event.clientX - touchTracker.startX;
		var deltaY = event.clientY - touchTracker.startY;

		if ( Math.abs( deltaX ) > SWIPE_PX && Math.abs( deltaX ) > Math.abs( deltaY ) ) {
			navigateFromHorizontalSwipe( deltaX );
			resetTouchTracker();
			return;
		}

		if ( touchTracker.moved || elapsed > HOLD_MS + 40 ) {
			resetTouchTracker();
			return;
		}

		var rect = event.currentTarget.getBoundingClientRect();
		var ratio = ( event.clientX - rect.left ) / rect.width;

		navigateFromTapZone( ratio );

		resetTouchTracker();
	}

	function resetTouchTracker() {
		touchTracker.pointerId = null;
		touchTracker.holdTimer = null;
		touchTracker.isHolding = false;
		touchTracker.moved = false;
	}

	function clearTimer() {
		if ( state.timer ) {
			window.cancelAnimationFrame( state.timer );
			state.timer = null;
		}
		if ( state.videoCleanup ) {
			state.videoCleanup();
			state.videoCleanup = null;
		}
	}

	function getCurrentGroup() {
		return state.data && state.data.groups ? state.data.groups[ state.groupIndex ] : null;
	}

	function getCurrentSlide() {
		var group = getCurrentGroup();
		return group && group.slides ? group.slides[ state.slideIndex ] : null;
	}

	function getIcons() {
		return ( state.data && state.data.settings && state.data.settings.icons ) || {};
	}

	function setButtonIcon( button, key ) {
		if ( ! button ) {
			return;
		}

		var icons = getIcons();
		var svg = icons[ key ] || '';
		button.innerHTML = '';

		if ( svg && svg.toLowerCase().indexOf( '<svg' ) !== -1 ) {
			button.innerHTML = svg;
			var svgEl = button.querySelector( 'svg' );
			if ( svgEl ) {
				svgEl.setAttribute( 'aria-hidden', 'true' );
				svgEl.setAttribute( 'focusable', 'false' );
				if ( ! svgEl.getAttribute( 'width' ) && ! svgEl.getAttribute( 'height' ) ) {
					svgEl.setAttribute( 'width', '18' );
					svgEl.setAttribute( 'height', '18' );
				}
			}
			return;
		}

		button.innerHTML = ICON_FALLBACKS[ key ] || '';
	}

	function refreshControlIcons() {
		var pauseBtn = viewer.querySelector( '[data-webmz-story-pause]' );
		var muteBtn = viewer.querySelector( '[data-webmz-story-mute]' );
		setButtonIcon( viewer.querySelector( '[data-webmz-story-close]' ), 'close' );
		setButtonIcon( viewer.querySelector( '[data-webmz-story-prev]' ), 'nav_prev' );
		setButtonIcon( viewer.querySelector( '[data-webmz-story-next]' ), 'nav_next' );
		setButtonIcon( pauseBtn, state.paused ? 'play' : 'pause' );
		setButtonIcon( muteBtn, state.muted ? 'mute' : 'unmute' );
	}

	function getDurationMs( slide, media ) {
		if ( slide && slide.sync_video_duration && media && media.tagName === 'VIDEO' && media.duration && ! isNaN( media.duration ) ) {
			return Math.max( 1000, Math.round( media.duration * 1000 ) );
		}

		var duration = slide && slide.duration ? parseInt( slide.duration, 10 ) : 3;
		return Math.max( 1, duration ) * 1000;
	}

	function renderProgress() {
		var group = getCurrentGroup();
		var container = viewer.querySelector( '[data-webmz-story-progress]' );
		if ( ! container || ! group ) {
			return;
		}

		container.innerHTML = '';
		group.slides.forEach( function ( _, index ) {
			var bar = document.createElement( 'span' );
			var fill = document.createElement( 'i' );
			if ( index < state.slideIndex ) {
				fill.style.width = '100%';
			}
			bar.appendChild( fill );
			container.appendChild( bar );
		} );
	}

	function updateProgress( percent ) {
		var bars = viewer.querySelectorAll( '[data-webmz-story-progress] span i' );
		if ( ! bars[ state.slideIndex ] ) {
			return;
		}
		bars[ state.slideIndex ].style.width = Math.min( 100, Math.max( 0, percent ) ) + '%';
	}

	function getSlideBackdropUrl( slide, group ) {
		if ( slide && slide.media_type === 'image' && slide.media_url ) {
			return slide.media_url;
		}

		if ( group && group.thumb ) {
			return group.thumb;
		}

		return slide && slide.media_url ? slide.media_url : '';
	}

	function updateBackdrop( slide, group ) {
		var bg = viewer.querySelector( '[data-webmz-story-backdrop-bg]' );

		if ( ! bg ) {
			return;
		}

		var url = getSlideBackdropUrl( slide, group );

		if ( url ) {
			bg.style.backgroundImage = 'url("' + String( url ).replace( /"/g, '\\"' ) + '")';
		} else {
			bg.style.backgroundImage = '';
		}
	}

	function buildSlideMedia( slide, group, settings ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'webmz-stories-viewer__slide';

		var bg = document.createElement( 'div' );
		bg.className = 'webmz-stories-viewer__media-bg';
		bg.style.backgroundImage = 'url("' + slide.media_url + '")';
		wrap.appendChild( bg );

		var media;
		if ( slide.media_type === 'video' ) {
			media = document.createElement( 'video' );
			media.src = slide.media_url;
			media.playsInline = true;
			media.preload = 'metadata';
			media.muted = settings.mute_videos !== 'no';
			media.controls = false;
		} else {
			media = document.createElement( 'img' );
			media.src = slide.media_url;
			media.alt = group.title || '';
			media.draggable = false;
		}

		wrap.appendChild( media );
		return { media: media, wrap: wrap };
	}

	function renderSlide( direction ) {
		clearTimer();

		var group = getCurrentGroup();
		var slide = getCurrentSlide();
		var mediaWrap = viewer.querySelector( '[data-webmz-story-media]' );
		var title = viewer.querySelector( '[data-webmz-story-title]' );
		var cta = viewer.querySelector( '[data-webmz-story-cta]' );
		var preview = viewer.querySelector( '[data-webmz-story-preview]' );
		var muteBtn = viewer.querySelector( '[data-webmz-story-mute]' );
		var settings = state.data.settings || {};

		if ( ! group || ! slide || ! mediaWrap ) {
			closeViewer();
			return;
		}

		title.textContent = group.title || '';
		updateBackdrop( slide, group );
		renderProgress();
		refreshControlIcons();

		if ( muteBtn ) {
			muteBtn.hidden = slide.media_type !== 'video';
		}

		state.muted = settings.mute_videos !== 'no';
		if ( slide.media_type === 'video' ) {
			setButtonIcon( muteBtn, state.muted ? 'mute' : 'unmute' );
		}

		mediaWrap.classList.toggle( 'is-cover', settings.full_size_media === 'yes' );

		var built = buildSlideMedia( slide, group, settings );

		if ( direction ) {
			mediaWrap.classList.remove( 'is-slide-next', 'is-slide-prev', 'is-slide-enter' );
			built.wrap.classList.add( direction === 'next' ? 'is-slide-next' : 'is-slide-prev' );
			mediaWrap.innerHTML = '';
			mediaWrap.appendChild( built.wrap );
			window.requestAnimationFrame( function () {
				built.wrap.classList.add( 'is-slide-enter' );
			} );
		} else {
			mediaWrap.classList.remove( 'is-slide-next', 'is-slide-prev', 'is-slide-enter' );
			mediaWrap.innerHTML = '';
			mediaWrap.appendChild( built.wrap );
		}

		if ( settings.swipe_up_button !== 'no' && slide.button_title && slide.button_link ) {
			cta.hidden = false;
			cta.innerHTML = '<a href="' + slide.button_link + '"' + ( slide.button_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '' ) + '>' + slide.button_title + '</a>';
		} else {
			cta.hidden = true;
			cta.innerHTML = '';
		}

		var nextGroup = state.data.groups[ state.groupIndex + 1 ];
		if ( nextGroup && nextGroup.thumb ) {
			preview.hidden = false;
			var previewImg = document.createElement( 'img' );
			previewImg.src = nextGroup.thumb;
			previewImg.alt = '';
			previewImg.draggable = false;
			preview.innerHTML = '';
			preview.appendChild( previewImg );
		} else {
			preview.hidden = true;
			preview.innerHTML = '';
		}

		if ( ! state.holdPaused ) {
			state.paused = false;
			viewer.classList.remove( 'is-paused', 'is-holding' );
		}

		state.progressStart = performance.now();
		state.elapsed = 0;
		refreshControlIcons();
		startTimer( built.media, slide );
	}

	function startTimer( media, slide ) {
		slide = slide || getCurrentSlide();

		function begin() {
			if ( slide && slide.sync_video_duration && media && media.tagName === 'VIDEO' ) {
				startVideoProgress( media );
				media.play().catch( function () {} );
				return;
			}

			var duration = getDurationMs( slide, media );

			function tick( now ) {
				if ( state.paused ) {
					state.timer = window.requestAnimationFrame( tick );
					return;
				}

				var elapsed = state.elapsed + ( now - state.progressStart );
				updateProgress( ( elapsed / duration ) * 100 );

				if ( elapsed >= duration ) {
					nextSlide();
					return;
				}

				state.timer = window.requestAnimationFrame( tick );
			}

			if ( media && media.tagName === 'VIDEO' ) {
				media.play().catch( function () {} );
			}

			state.timer = window.requestAnimationFrame( tick );
		}

		if ( media && media.tagName === 'VIDEO' ) {
			if ( media.readyState >= 1 ) {
				begin();
			} else {
				media.addEventListener( 'loadedmetadata', begin, { once: true } );
			}
			return;
		}

		begin();
	}

	function startVideoProgress( media ) {
		function onTimeUpdate() {
			if ( state.paused || ! media.duration ) {
				return;
			}
			updateProgress( ( media.currentTime / media.duration ) * 100 );
		}

		function onEnded() {
			if ( ! state.paused ) {
				nextSlide();
			}
		}

		media.addEventListener( 'timeupdate', onTimeUpdate );
		media.addEventListener( 'ended', onEnded );

		state.videoCleanup = function () {
			media.removeEventListener( 'timeupdate', onTimeUpdate );
			media.removeEventListener( 'ended', onEnded );
		};
	}

	function setPaused( paused, fromHold ) {
		state.paused = paused;
		state.holdPaused = !! fromHold && paused;

		if ( paused ) {
			state.elapsed += performance.now() - state.progressStart;
			viewer.classList.add( 'is-paused' );
			if ( fromHold ) {
				viewer.classList.add( 'is-holding' );
			}
		} else {
			state.holdPaused = false;
			state.progressStart = performance.now();
			viewer.classList.remove( 'is-paused', 'is-holding' );
		}

		var video = viewer.querySelector( '[data-webmz-story-media] video' );
		if ( video ) {
			if ( paused ) {
				video.pause();
			} else {
				video.play().catch( function () {} );
			}
		}

		refreshControlIcons();
	}

	function holdPause() {
		if ( ! state.paused ) {
			setPaused( true, true );
		}
	}

	function holdResume() {
		if ( state.holdPaused || state.paused ) {
			setPaused( false, false );
		}
	}

	function togglePause( fromHold ) {
		setPaused( ! state.paused, fromHold );
	}

	function toggleMute() {
		var video = viewer.querySelector( '[data-webmz-story-media] video' );
		if ( ! video ) {
			return;
		}

		video.muted = ! video.muted;
		state.muted = video.muted;
		refreshControlIcons();
	}

	function nextSlide() {
		var group = getCurrentGroup();
		if ( ! group ) {
			closeViewer();
			return;
		}

		if ( state.slideIndex < group.slides.length - 1 ) {
			state.slideIndex += 1;
			renderSlide( 'next' );
			return;
		}

		markCurrentGroupSeen();

		if ( state.groupIndex < state.data.groups.length - 1 ) {
			state.groupIndex += 1;
			state.slideIndex = 0;
			renderSlide( 'next' );
			return;
		}

		closeViewer();
	}

	function prevSlide() {
		if ( state.slideIndex > 0 ) {
			state.slideIndex -= 1;
			renderSlide( 'prev' );
			return;
		}

		if ( state.groupIndex > 0 ) {
			state.groupIndex -= 1;
			var prevGroup = state.data.groups[ state.groupIndex ];
			state.slideIndex = Math.max( 0, ( prevGroup.slides || [] ).length - 1 );
			renderSlide( 'prev' );
		}
	}

	function openViewer( root, groupIndex, triggerEl ) {
		var json = root.querySelector( '.webmz-stories__data' );
		if ( ! json ) {
			return;
		}

		try {
			state.data = JSON.parse( json.textContent || '{}' );
		} catch ( error ) {
			return;
		}

		if ( ! state.data.groups || ! state.data.groups.length ) {
			return;
		}

		var targetGroup = state.data.groups[ groupIndex ];
		if ( ! targetGroup || ! targetGroup.slides || ! targetGroup.slides.length ) {
			return;
		}

		state.root = root;
		state.boxId = parseInt( root.getAttribute( 'data-box-id' ) || '0', 10 );
		state.groupIndex = groupIndex;
		state.slideIndex = 0;
		state.isClosing = false;
		state.holdPaused = false;

		ensureViewer();
		bindTouchNavigation();

		if ( triggerEl ) {
			var ring = triggerEl.querySelector( '.webmz-stories__ring' ) || triggerEl;
			var ringRect = ring.getBoundingClientRect();
			viewer.style.setProperty( '--webmz-story-origin-x', ( ringRect.left + ringRect.width / 2 ) + 'px' );
			viewer.style.setProperty( '--webmz-story-origin-y', ( ringRect.top + ringRect.height / 2 ) + 'px' );
		}

		viewer.classList.remove( 'is-closing', 'is-open-active', 'is-holding' );
		viewer.classList.add( 'is-open', 'is-opening' );
		document.documentElement.classList.add( 'webmz-stories-open' );

		renderSlide( null );

		window.requestAnimationFrame( function () {
			window.requestAnimationFrame( function () {
				viewer.classList.add( 'is-open-active' );
				viewer.classList.remove( 'is-opening' );
			} );
		} );
	}

	function closeViewer() {
		if ( ! viewer || state.isClosing ) {
			return;
		}

		var group = getCurrentGroup();
		if ( group && state.boxId && state.slideIndex >= ( group.slides || [] ).length - 1 ) {
			markGroupSeen( state.boxId, group.id );
		}

		state.isClosing = true;
		clearTimer();
		viewer.classList.remove( 'is-open-active' );
		viewer.classList.add( 'is-closing' );

		window.setTimeout( function () {
			if ( ! viewer ) {
				return;
			}
			viewer.classList.remove( 'is-open', 'is-closing', 'is-opening', 'is-paused', 'is-holding' );
			var mediaWrap = viewer.querySelector( '[data-webmz-story-media]' );
			if ( mediaWrap ) {
				mediaWrap.innerHTML = '';
			}
			var backdropBg = viewer.querySelector( '[data-webmz-story-backdrop-bg]' );
			if ( backdropBg ) {
				backdropBg.style.backgroundImage = '';
			}
			state.isClosing = false;
			state.holdPaused = false;
			resetTouchTracker();
		}, CLOSE_MS );

		document.documentElement.classList.remove( 'webmz-stories-open' );
	}

	function bindRootClick( root ) {
		root.addEventListener( 'click', function ( event ) {
			var item = event.target.closest( '.webmz-stories__item' );
			if ( ! item || ! root.contains( item ) || item.classList.contains( 'webmz-stories__item--skeleton' ) || item.disabled ) {
				return;
			}

			if ( root.classList.contains( 'webmz-stories--loading' ) ) {
				return;
			}

			event.preventDefault();
			openViewer( root, parseInt( item.getAttribute( 'data-story-group' ) || '0', 10 ), item );
		} );
	}

	function initTrackSwiper( root ) {
		if ( typeof window.Swiper === 'undefined' ) {
			return;
		}

		var swiperEl = root.querySelector( '.webmz-stories__swiper' );

		if ( ! swiperEl ) {
			return;
		}

		if ( swiperEl.swiper ) {
			swiperEl.swiper.update();
			return;
		}

		new window.Swiper( swiperEl, {
			slidesPerView: 'auto',
			spaceBetween: 14,
			freeMode: {
				enabled: true,
				momentum: true,
				momentumRatio: 0.45,
				momentumBounce: true,
			},
			grabCursor: true,
			rtl: isRtl(),
			watchOverflow: true,
			observer: true,
			observeParents: true,
			resistanceRatio: 0.75,
			wrapperClass: 'webmz-stories__track',
			slideClass: 'webmz-stories__item',
			touchStartPreventDefault: false,
		} );
	}

	function loadStoryBox( root ) {
		var boxId = root.getAttribute( 'data-box-id' );
		var config = window.webmzStories || {};

		if ( ! boxId || ! config.ajaxUrl ) {
			root.classList.remove( 'webmz-stories--loading' );
			return;
		}

		var body = new URLSearchParams();
		body.append( 'action', 'webmz_load_story_box' );
		body.append( 'nonce', config.nonce || '' );
		body.append( 'box_id', boxId );

		fetch( config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString()
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( response ) {
				if ( ! response || ! response.success || ! response.data ) {
					root.classList.remove( 'webmz-stories--loading' );
					root.classList.add( 'webmz-stories--empty' );
					root.innerHTML = '';
					return;
				}

				var track = root.querySelector( '.webmz-stories__track' );
				if ( track ) {
					track.innerHTML = response.data.track || '';
				}

				var oldData = root.querySelector( '.webmz-stories__data' );
				if ( oldData ) {
					oldData.remove();
				}

				var script = document.createElement( 'script' );
				script.type = 'application/json';
				script.className = 'webmz-stories__data';
				script.textContent = JSON.stringify( response.data.payload || {} );
				root.appendChild( script );

				root.classList.remove( 'webmz-stories--loading' );
				initTrackSwiper( root );
				applySeenClasses( root, boxId );
			} )
			.catch( function () {
				root.classList.remove( 'webmz-stories--loading' );
				root.classList.add( 'webmz-stories--empty' );
				root.innerHTML = '';
			} );
	}

	function initRoot( root ) {
		if ( root.dataset.webmzStoriesReady ) {
			initTrackSwiper( root );
			return;
		}
		root.dataset.webmzStoriesReady = '1';

		bindRootClick( root );
		preventMediaDrag( root );
		initTrackSwiper( root );

		if ( root.classList.contains( 'webmz-stories--loading' ) && root.getAttribute( 'data-box-id' ) ) {
			loadStoryBox( root );
			return;
		}

		var boxId = root.getAttribute( 'data-box-id' );
		if ( boxId ) {
			applySeenClasses( root, boxId );
		}
	}

	function initAll() {
		document.querySelectorAll( '[data-webmz-stories]' ).forEach( initRoot );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	var elementorHookBound = false;

	function bindElementorHook() {
		if ( elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks ) {
			return;
		}

		elementorHookBound = true;

		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/webmz-stories.default', function ( $scope ) {
			var root = $scope[ 0 ].querySelector( '[data-webmz-stories]' ) || $scope[ 0 ];
			if ( root ) {
				initRoot( root );
			}
		} );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', bindElementorHook );

		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			bindElementorHook();
		}
	}
}() );
