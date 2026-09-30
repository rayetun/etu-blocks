/**
 * Counter block — Interactivity API store.
 *
 * The final value is already rendered server-side (bound to context.displayValue).
 * When the block scrolls into view we animate the value from zero to the target
 * with an ease-out curve, updating context.displayValue so the bound text
 * reflects each frame. Under prefers-reduced-motion we leave the final value in
 * place — no animation.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const format = ( value, separator ) => {
	const rounded = Math.round( value );
	return separator ? rounded.toLocaleString() : String( rounded );
};

store( 'etu/counter', {
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
				return; // Final value is already shown.
			}

			const target = Number( ctx.target ) || 0;
			const duration = Number( ctx.duration ) || 2000;
			const separator = !! ctx.separator;

			const observer = new window.IntersectionObserver(
				( entries, obs ) => {
					entries.forEach( ( entry ) => {
						if ( ! entry.isIntersecting ) {
							return;
						}
						obs.disconnect();

						const startTime = performance.now();
						ctx.displayValue = format( 0, separator );

						const tick = ( now ) => {
							const progress = Math.min(
								1,
								( now - startTime ) / duration
							);
							const eased = 1 - Math.pow( 1 - progress, 3 );
							ctx.displayValue = format( target * eased, separator );
							if ( progress < 1 ) {
								window.requestAnimationFrame( tick );
							} else {
								ctx.displayValue = format( target, separator );
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
