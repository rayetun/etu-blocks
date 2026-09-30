/**
 * Post Grid — Interactivity API store.
 *
 * - Category filter: each card watches the active category and toggles its own
 *   `visible` (so it works with server-side rendering — cards default visible).
 * - Carousel: scroll-snap track with looping prev/next arrows and page dots that
 *   match the responsive slides-per-view (rebuilt on resize).
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const registry = new WeakMap();

const behavior = () =>
	window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
		? 'auto'
		: 'smooth';

function scrollPage( buttonEl, dir ) {
	const root = buttonEl.closest( '.etu-post-grid' );
	const reg = root && registry.get( root );
	if ( ! reg ) {
		return;
	}
	const track = reg.track;
	const beh = behavior();
	const atEnd =
		track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
	const atStart = track.scrollLeft <= 4;

	if ( dir > 0 && atEnd ) {
		track.scrollTo( { left: 0, behavior: beh } );
	} else if ( dir < 0 && atStart ) {
		track.scrollTo( { left: track.scrollWidth, behavior: beh } );
	} else {
		track.scrollBy( { left: dir * track.clientWidth, behavior: beh } );
	}
}

function setupCarousel( root ) {
	const track = root.querySelector( '.etu-post-grid__track' );
	if ( ! track ) {
		return;
	}
	const cards = Array.from( track.children );
	const dotsWrap = root.querySelector( '.etu-post-grid__dots' );
	const reg = { track, cards, dots: [] };
	registry.set( root, reg );

	const perView = () => {
		const v = parseInt(
			getComputedStyle( root ).getPropertyValue( '--etu-pg-per' ),
			10
		);
		return v > 0 ? v : 1;
	};

	const updateActive = () => {
		if ( ! reg.dots.length ) {
			return;
		}
		const page = Math.round( track.scrollLeft / track.clientWidth );
		reg.dots.forEach( ( d, i ) => {
			d.classList.toggle( 'is-active', i === page );
			if ( i === page ) {
				d.setAttribute( 'aria-current', 'true' );
			} else {
				d.removeAttribute( 'aria-current' );
			}
		} );
	};

	const buildDots = () => {
		if ( ! dotsWrap ) {
			return;
		}
		const pv = perView();
		const pages = Math.max( 1, Math.ceil( cards.length / pv ) );
		const label = dotsWrap.getAttribute( 'data-page-label' ) || 'Page';
		dotsWrap.textContent = '';
		reg.dots = [];
		for ( let i = 0; i < pages; i++ ) {
			const b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'etu-post-grid__dot';
			b.setAttribute( 'aria-label', label + ' ' + ( i + 1 ) );
			b.addEventListener( 'click', () => {
				const target = cards[ i * perView() ];
				if ( target ) {
					track.scrollTo( {
						left: target.offsetLeft - track.offsetLeft,
						behavior: behavior(),
					} );
				}
			} );
			dotsWrap.appendChild( b );
			reg.dots.push( b );
		}
		updateActive();
	};

	buildDots();
	track.addEventListener(
		'scroll',
		() => window.requestAnimationFrame( updateActive ),
		{ passive: true }
	);
	window.addEventListener( 'resize', buildDots );
}

store( 'etu/post-grid', {
	state: {
		get filterActive() {
			const ctx = getContext();
			return ctx.slug === ctx.activeCat;
		},
		get statusText() {
			const ctx = getContext();
			return ctx.activeLabel
				? String( ctx.showingTemplate || '%s' ).replace(
						'%s',
						ctx.activeLabel
				  )
				: '';
		},
	},
	actions: {
		setFilter() {
			const ctx = getContext();
			ctx.activeCat = ctx.slug;
			ctx.activeLabel = ctx.label;
		},
		prev() {
			scrollPage( getElement().ref, -1 );
		},
		next() {
			scrollPage( getElement().ref, 1 );
		},
	},
	callbacks: {
		filterCard() {
			const ctx = getContext();
			ctx.visible =
				! ctx.activeCat ||
				( Array.isArray( ctx.cats ) && ctx.cats.includes( ctx.activeCat ) );
		},
		init() {
			setupCarousel( getElement().ref );
		},
	},
} );
