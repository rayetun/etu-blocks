/**
 * Table of Contents — Interactivity API store.
 *
 * - Smooth scroll to a heading on click, then move focus to it (so keyboard and
 *   screen-reader users land on the section), respecting reduced motion.
 * - Scrollspy: highlight the link for the section currently at the top.
 * - Collapse toggle.
 * Links are plain in-page anchors, so everything degrades to a working jump-list
 * with JS disabled.
 */
import {
	store,
	getContext,
	getElement,
	withSyncEvent,
} from '@wordpress/interactivity';

const registry = new WeakMap();

// Matches the CSS sticky offset (top: 24px) plus a small gap below the TOC.
const STICKY_TOP = 24;
const STICKY_GAP = 16;

const stickyOffset = ( root ) =>
	root && root.classList.contains( 'etu-toc--sticky' )
		? STICKY_TOP + root.offsetHeight + STICKY_GAP
		: 0;

store( 'etu/toc', {
	state: {
		get expanded() {
			return ! getContext().collapsed;
		},
		get isActive() {
			const ctx = getContext();
			return !! ctx.id && ctx.id === ctx.activeId;
		},
		get currentMark() {
			const ctx = getContext();
			return ctx.id && ctx.id === ctx.activeId ? 'location' : false;
		},
	},
	actions: {
		toggle() {
			const ctx = getContext();
			ctx.collapsed = ! ctx.collapsed;
		},
		scrollTo: withSyncEvent( ( event ) => {
			const link = getElement().ref;
			const href = link.getAttribute( 'href' ) || '';
			if ( href.charAt( 0 ) !== '#' ) {
				return;
			}
			const id = decodeURIComponent( href.slice( 1 ) );
			const target = document.getElementById( id );
			if ( ! target ) {
				return;
			}
			event.preventDefault();
			const reduce = window.matchMedia(
				'(prefers-reduced-motion: reduce)'
			).matches;
			// Land the heading below the sticky TOC, not behind it.
			const offset = stickyOffset( link.closest( '.etu-toc' ) );
			const y =
				target.getBoundingClientRect().top + window.scrollY - offset;
			window.scrollTo( {
				top: y < 0 ? 0 : y,
				behavior: reduce ? 'auto' : 'smooth',
			} );
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + id );
			}
			// Move focus to the section for keyboard / screen-reader users.
			if ( target.getAttribute( 'tabindex' ) === null ) {
				target.setAttribute( 'tabindex', '-1' );
			}
			target.focus( { preventScroll: true } );
		} ),
	},
	callbacks: {
		init() {
			const ctx = getContext();
			const root = getElement().ref;
			const targets = Array.from(
				root.querySelectorAll( '.etu-toc__link' )
			)
				.map( ( l ) => {
					const h = l.getAttribute( 'href' ) || '';
					return h.charAt( 0 ) === '#'
						? document.getElementById(
								decodeURIComponent( h.slice( 1 ) )
						  )
						: null;
				} )
				.filter( Boolean );

			if ( ! targets.length ) {
				return;
			}

			// Sticky: give headings a scroll-margin so native anchor jumps (and
			// no-JS / non-smooth) also land below the pinned TOC. Kept in sync
			// with the TOC height on resize.
			if ( ctx.sticky ) {
				const applyMargin = () => {
					const off = stickyOffset( root ) + 'px';
					targets.forEach( ( t ) => {
						t.style.scrollMarginTop = off;
					} );
				};
				applyMargin();
				window.addEventListener( 'resize', applyMargin );
			}

			if ( ! ctx.spy ) {
				return;
			}

			const recompute = () => {
				let active = targets[ 0 ];
				for ( const t of targets ) {
					if ( t.getBoundingClientRect().top - 100 <= 1 ) {
						active = t;
					} else {
						break;
					}
				}
				if ( active && active.id ) {
					ctx.activeId = active.id;
				}
			};

			const io = new IntersectionObserver( recompute, {
				rootMargin: '0px 0px -66% 0px',
				threshold: [ 0, 1 ],
			} );
			targets.forEach( ( t ) => io.observe( t ) );
			registry.set( root, { io } );
			recompute();
		},
	},
} );
