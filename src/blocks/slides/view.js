/**
 * Slides / Carousel — Interactivity API store.
 *
 * The track is a native scroll-snap container, so touch, trackpad and keyboard
 * scrolling work with no JS. This store adds arrows / dots / autoplay, and an
 * IntersectionObserver that tracks the current slide, marks off-screen slides
 * `inert` (so they are neither tabbable nor announced), and drives the dots.
 *
 * Per-instance DOM (track, slides, observer, timer) is kept in a WeakMap keyed
 * by the carousel element — never in the reactive context.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const registry = new WeakMap();

const regFor = ( el ) =>
	el ? registry.get( el.closest( '.etu-slides' ) ) : undefined;

const behaviorFor = ( ctx ) => ( ctx.reduced ? 'auto' : 'smooth' );

function scrollByDir( reg, ctx, dir ) {
	const track = reg.track;
	if ( ! track ) {
		return;
	}
	const behavior = behaviorFor( ctx );
	const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
	const atStart = track.scrollLeft <= 2;

	if ( dir > 0 && atEnd ) {
		if ( ctx.loop ) {
			track.scrollTo( { left: 0, behavior } );
		}
		return;
	}
	if ( dir < 0 && atStart ) {
		if ( ctx.loop ) {
			track.scrollTo( { left: track.scrollWidth, behavior } );
		}
		return;
	}
	track.scrollBy( { left: dir * track.clientWidth, behavior } );
}

function scrollToIndex( reg, ctx, index ) {
	const track = reg.track;
	const slide = reg.slides[ index ];
	if ( ! track || ! slide ) {
		return;
	}
	track.scrollTo( {
		left: slide.offsetLeft - track.offsetLeft,
		behavior: behaviorFor( ctx ),
	} );
}

function updateTimer( reg, ctx ) {
	if ( reg.timer ) {
		clearInterval( reg.timer );
		reg.timer = null;
	}
	if ( ctx.autoplay && ! ctx.paused && ! ctx.hovering ) {
		reg.timer = setInterval(
			() => scrollByDir( reg, ctx, 1 ),
			ctx.interval
		);
	}
}

store( 'etu/slides', {
	state: {
		get statusText() {
			const ctx = getContext();
			return String( ctx.statusTemplate )
				.replace( '%1$d', ctx.current + 1 )
				.replace( '%2$d', ctx.total );
		},
		get liveMode() {
			const ctx = getContext();
			return ctx.autoplay && ! ctx.paused ? 'off' : 'polite';
		},
		get playLabel() {
			const ctx = getContext();
			return ctx.paused ? ctx.playText : ctx.pauseText;
		},
		get isPaused() {
			return getContext().paused;
		},
		get isCurrentDot() {
			const ctx = getContext();
			return ctx.index === ctx.current;
		},
	},
	actions: {
		prev() {
			const reg = regFor( getElement().ref );
			if ( reg ) {
				scrollByDir( reg, getContext(), -1 );
			}
		},
		next() {
			const reg = regFor( getElement().ref );
			if ( reg ) {
				scrollByDir( reg, getContext(), 1 );
			}
		},
		goTo() {
			const ctx = getContext();
			const reg = regFor( getElement().ref );
			if ( reg ) {
				scrollToIndex( reg, ctx, ctx.index );
			}
		},
		togglePlay() {
			const ctx = getContext();
			ctx.paused = ! ctx.paused;
			const reg = regFor( getElement().ref );
			if ( reg ) {
				updateTimer( reg, ctx );
			}
		},
		hoverPause() {
			const ctx = getContext();
			ctx.hovering = true;
			const reg = regFor( getElement().ref );
			if ( reg ) {
				updateTimer( reg, ctx );
			}
		},
		hoverResume() {
			const ctx = getContext();
			ctx.hovering = false;
			const reg = regFor( getElement().ref );
			if ( reg ) {
				updateTimer( reg, ctx );
			}
		},
	},
	callbacks: {
		init() {
			const ctx = getContext();
			const region = getElement().ref;
			const track = region.querySelector( '.etu-slides__track' );
			if ( ! track ) {
				return;
			}
			const slides = Array.from( track.children );

			ctx.reduced = window.matchMedia(
				'(prefers-reduced-motion: reduce)'
			).matches;
			ctx.hovering = false;
			// Never auto-start under reduced motion (the visitor can still press Play).
			if ( ctx.reduced && ctx.autoplay ) {
				ctx.paused = true;
			}

			const reg = { track, slides, io: null, timer: null };
			registry.set( region, reg );

			reg.io = new IntersectionObserver(
				( entries ) => {
					entries.forEach( ( entry ) => {
						const li = entry.target;
						const visible =
							entry.isIntersecting &&
							entry.intersectionRatio >= 0.55;
						li.inert = ! visible;
						if ( visible ) {
							li.removeAttribute( 'aria-hidden' );
						} else {
							li.setAttribute( 'aria-hidden', 'true' );
						}
					} );
					const idx = slides.findIndex( ( li ) => ! li.inert );
					if ( idx >= 0 ) {
						ctx.current = idx;
					}
				},
				{ root: track, threshold: [ 0, 0.55, 1 ] }
			);
			slides.forEach( ( li ) => reg.io.observe( li ) );

			updateTimer( reg, ctx );
		},
	},
} );
