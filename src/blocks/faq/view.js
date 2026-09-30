/**
 * FAQ block — Interactivity API store.
 *
 * Open/closed state is a per-item `context.open` boolean, so the server can
 * resolve the `aria-expanded` and `hidden` bindings at render time (a JS-only
 * derived getter cannot be resolved server-side). Single-open mode is coordinated
 * through the wrapper's shared `activeId`: `toggle` sets it, and each item's
 * `syncOpen` watch mirrors `open` from it — so opening one closes the others.
 * In multiple-open mode, `toggle` simply flips the item's own `open`.
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'etu/faq', {
	actions: {
		toggle() {
			const ctx = getContext();
			if ( ctx.allowMultiple ) {
				ctx.open = ! ctx.open;
			} else {
				ctx.activeId = ctx.activeId === ctx.id ? '' : ctx.id;
			}
		},
	},
	callbacks: {
		syncOpen() {
			const ctx = getContext();
			if ( ! ctx.allowMultiple ) {
				ctx.open = ctx.activeId === ctx.id;
			}
		},
	},
} );
