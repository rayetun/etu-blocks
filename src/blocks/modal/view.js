/**
 * Modal / Popup — Interactivity API store.
 *
 * Opening locks body scroll and moves focus into the dialog; closing restores
 * focus to whatever opened it. Tab is trapped within the dialog, Esc closes, and
 * the backdrop closes (when enabled). Per-instance DOM is kept in a WeakMap, not
 * in the reactive context.
 */
import {
	store,
	getContext,
	getElement,
	withSyncEvent,
} from '@wordpress/interactivity';

const registry = new WeakMap();

const regFor = ( el ) =>
	el ? registry.get( el.closest( '.etu-modal' ) ) : undefined;

const FOCUSABLE =
	'a[href],area[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

function focusable( dialog ) {
	return Array.from( dialog.querySelectorAll( FOCUSABLE ) ).filter(
		( el ) => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement
	);
}

// Make everything except the dialog inert while open, so AT and keyboard users
// can only reach the dialog (aria-modal alone is unreliable). We walk from the
// overlay up to <body>, marking each level's other children inert, and only undo
// the ones we set.
function setBackgroundInert( overlay, on ) {
	let node = overlay;
	while ( node && node.parentElement && node !== document.body ) {
		const parent = node.parentElement;
		Array.from( parent.children ).forEach( ( sib ) => {
			if ( sib === node ) {
				return;
			}
			if ( on ) {
				if ( ! sib.hasAttribute( 'data-etu-inert' ) && ! sib.inert ) {
					sib.setAttribute( 'data-etu-inert', '' );
					sib.inert = true;
				}
			} else if ( sib.hasAttribute( 'data-etu-inert' ) ) {
				sib.removeAttribute( 'data-etu-inert' );
				sib.inert = false;
			}
		} );
		node = parent;
	}
}

function lockScroll( reg ) {
	const sb = window.innerWidth - document.documentElement.clientWidth;
	reg.prevOverflow = document.body.style.overflow;
	reg.prevPad = document.body.style.paddingRight;
	document.body.style.overflow = 'hidden';
	if ( sb > 0 ) {
		document.body.style.paddingRight = sb + 'px';
	}
}

function unlockScroll( reg ) {
	document.body.style.overflow = reg.prevOverflow || '';
	document.body.style.paddingRight = reg.prevPad || '';
}

function onOpen( reg ) {
	lockScroll( reg );
	setBackgroundInert( reg.overlay, true );
	// Focus the first control (or the dialog) after it becomes visible.
	window.requestAnimationFrame( () => {
		const f = focusable( reg.dialog );
		( f[ 0 ] || reg.dialog ).focus();
	} );
}

function onClose( reg, ctx ) {
	unlockScroll( reg );
	// Restore the background before moving focus back to the trigger.
	setBackgroundInert( reg.overlay, false );
	if ( reg.trigger && typeof reg.trigger.focus === 'function' ) {
		reg.trigger.focus();
	}
	if ( ctx.showOnce && ctx.key ) {
		document.cookie =
			ctx.key + '=1;path=/;max-age=31536000;samesite=lax';
	}
}

store( 'etu/modal', {
	actions: {
		open() {
			const ctx = getContext();
			const reg = regFor( getElement().ref );
			if ( reg ) {
				reg.trigger = getElement().ref;
			}
			ctx.open = true;
		},
		close() {
			getContext().open = false;
		},
		backdropClick: withSyncEvent( ( event ) => {
			const ctx = getContext();
			// Only when the backdrop itself (not the dialog) was clicked.
			if ( ctx.closeOnBackdrop && event.target === getElement().ref ) {
				ctx.open = false;
			}
		} ),
		onKeydown: withSyncEvent( ( event ) => {
			const ctx = getContext();
			if ( ! ctx.open ) {
				return;
			}
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				ctx.open = false;
				return;
			}
			if ( event.key !== 'Tab' ) {
				return;
			}
			const reg = regFor( getElement().ref );
			if ( ! reg ) {
				return;
			}
			const f = focusable( reg.dialog );
			if ( ! f.length ) {
				event.preventDefault();
				reg.dialog.focus();
				return;
			}
			const first = f[ 0 ];
			const last = f[ f.length - 1 ];
			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		} ),
	},
	callbacks: {
		init() {
			const ctx = getContext();
			const root = getElement().ref;
			const reg = {
				overlay: root.querySelector( '.etu-modal__overlay' ),
				dialog: root.querySelector( '.etu-modal__dialog' ),
				trigger: null,
				wasOpen: false,
				timer: null,
			};
			registry.set( root, reg );

			if ( ctx.autoOpen ) {
				if ( ctx.trigger === 'load' ) {
					ctx.open = true;
				} else if ( ctx.trigger === 'delay' ) {
					reg.timer = window.setTimeout( () => {
						ctx.open = true;
					}, ctx.delay );
				}
			}
		},
		onToggle() {
			const ctx = getContext();
			const reg = regFor( getElement().ref );
			if ( ! reg ) {
				return;
			}
			if ( ctx.open && ! reg.wasOpen ) {
				reg.wasOpen = true;
				onOpen( reg );
			} else if ( ! ctx.open && reg.wasOpen ) {
				reg.wasOpen = false;
				onClose( reg, ctx );
			}
		},
	},
} );
