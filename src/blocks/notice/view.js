/**
 * Notice block — Interactivity API store.
 *
 * Dismiss hides the notice (context.dismissed → bound `hidden`) and, for the
 * fixed Top/Bottom bars, releases the page offset. With "remember" on, dismissal
 * is stored in a cookie; the server reads that cookie and skips rendering the
 * notice on the next load, so there is never a flash-of-visible-then-hidden.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const offsetProp = ( display ) =>
	display === 'top'
		? 'paddingTop'
		: display === 'bottom'
		? 'paddingBottom'
		: null;

store( 'etu/notice', {
	actions: {
		dismiss() {
			const ctx = getContext();
			ctx.dismissed = true;
			if ( ctx.remember && ctx.key ) {
				// One year; the server skips rendering while this is set.
				document.cookie =
					ctx.key + '=1;path=/;max-age=31536000;samesite=lax';
			}
			const prop = offsetProp( ctx.display );
			if ( prop ) {
				document.body.style[ prop ] = '';
			}
		},
	},
	callbacks: {
		init() {
			const ctx = getContext();
			const { ref } = getElement();

			// Push the page so the fixed bar doesn't overlap content.
			const prop = offsetProp( ctx.display );
			if ( prop && ! ctx.dismissed && ref ) {
				document.body.style[ prop ] = ref.offsetHeight + 'px';
			}
		},
	},
} );
