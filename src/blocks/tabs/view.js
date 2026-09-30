/**
 * Tabs block — Interactivity API store (ARIA Tabs pattern).
 *
 * Selection is coordinated by the wrapper's shared `activeId`. Each tab/panel
 * mirrors its own `context.selected` from it via `syncSelected` (which also keeps
 * the tab's roving `tabindex` in step), so `aria-selected`, `tabindex`, and
 * `hidden` stay correct — and are server-resolvable at render time. `select`
 * handles clicks; `onKeydown` implements Left/Right/Home/End with automatic
 * activation, moving focus to the newly selected tab.
 */
import { store, getContext, withSyncEvent } from '@wordpress/interactivity';

store( 'etu/tabs', {
	actions: {
		select() {
			const ctx = getContext();
			ctx.activeId = ctx.id;
		},
		onKeydown: withSyncEvent( ( event ) => {
			const ctx = getContext();
			const ids = ctx.tabIds;
			const count = ids.length;
			const current = ids.indexOf( ctx.activeId );
			let target = null;

			switch ( event.key ) {
				case 'ArrowRight':
					target = ids[ ( current + 1 ) % count ];
					break;
				case 'ArrowLeft':
					target = ids[ ( current - 1 + count ) % count ];
					break;
				case 'Home':
					target = ids[ 0 ];
					break;
				case 'End':
					target = ids[ count - 1 ];
					break;
				default:
					return;
			}

			event.preventDefault();
			ctx.activeId = target;

			const button = document.getElementById( `${ ctx.baseId }-tab-${ target }` );
			if ( button ) {
				button.focus();
			}
		} ),
	},
	callbacks: {
		syncSelected() {
			const ctx = getContext();
			const selected = ctx.activeId === ctx.id;
			ctx.selected = selected;
			if ( ctx.tabindex !== undefined ) {
				ctx.tabindex = selected ? 0 : -1;
			}
		},
	},
} );
