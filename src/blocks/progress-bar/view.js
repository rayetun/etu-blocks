/**
 * Progress Bar block — Interactivity API store.
 *
 * The fill width is already rendered server-side (context.width). When the bar
 * scrolls into view we animate it from zero with an ease-out curve. Under
 * prefers-reduced-motion we leave the final width in place.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'etu/progress-bar', {
	callbacks: {
		start() {
			const ctx = getContext();
			const { ref } = getElement();
			if ( ! ref ) {
				return;
			}

			const reduce =
				window.matchMedia &&
				window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			if ( reduce ) {
				return;
			}

			const target = Number( ctx.target ) || 0;
			const duration = 1400;

			const observer = new window.IntersectionObserver(
				( entries, obs ) => {
					entries.forEach( ( entry ) => {
						if ( ! entry.isIntersecting ) {
							return;
						}
						obs.disconnect();

						const startTime = performance.now();
						ctx.width = '0%';

						const tick = ( now ) => {
							const progress = Math.min(
								1,
								( now - startTime ) / duration
							);
							const eased = 1 - Math.pow( 1 - progress, 3 );
							ctx.width = ( target * eased ).toFixed( 1 ) + '%';
							if ( progress < 1 ) {
								window.requestAnimationFrame( tick );
							} else {
								ctx.width = target + '%';
							}
						};

						window.requestAnimationFrame( tick );
					} );
				},
				{ threshold: 0.4 }
			);

			observer.observe( ref );
		},
	},
} );
