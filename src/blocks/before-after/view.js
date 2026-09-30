/**
 * Before / After block — Interactivity API store.
 *
 * The reveal position lives in `context.position` (0–100). The range input is the
 * single source of truth: on input we update the position and recompute the two
 * derived context values the markup binds to — `afterClip` (the clip-path on the
 * "after" layer) and `dividerPos` (the divider's offset). Both are already
 * rendered server-side at the start position, so the block is correct with no JS;
 * this only makes it live. Keyboard operation comes free with the native range.
 */
import { store, getContext } from '@wordpress/interactivity';

const clamp = ( n ) => Math.max( 0, Math.min( 100, n ) );

store( 'etu/before-after', {
	actions: {
		slide( event ) {
			const ctx = getContext();
			const p = clamp( Number( event.target.value ) || 0 );
			ctx.position = p;
			ctx.beforeClip =
				ctx.orientation === 'vertical'
					? `inset(0 0 ${ 100 - p }% 0)`
					: `inset(0 ${ 100 - p }% 0 0)`;
			ctx.dividerPos = `${ p }%`;
			ctx.valueText = `${ Math.round( p ) }%`;
		},
	},
} );
