/**
 * Video Popup — Interactivity API store.
 *
 * The dialog behaviour mirrors the Modal block (scroll lock, background inert,
 * focus trap, Esc, backdrop close, focus restore). The video is a facade: for
 * YouTube/Vimeo the iframe has no src until `open`, when we point it at the embed
 * URL; closing clears the src so playback stops and nothing keeps running. Self-
 * hosted <video> uses preload="none" and is play()/pause()'d directly. Per-instance
 * DOM lives in a WeakMap, out of the reactive context.
 */
import {
	store,
	getContext,
	getElement,
	withSyncEvent,
} from '@wordpress/interactivity';

const registry = new WeakMap();

const regFor = ( el ) =>
	el ? registry.get( el.closest( '.etu-video-popup' ) ) : undefined;

const FOCUSABLE =
	'a[href],area[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),iframe,video,[tabindex]:not([tabindex="-1"])';

function focusable( dialog ) {
	return Array.from( dialog.querySelectorAll( FOCUSABLE ) ).filter(
		( el ) =>
			el.offsetWidth > 0 ||
			el.offsetHeight > 0 ||
			el === document.activeElement
	);
}

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

function onOpen( reg, ctx ) {
	lockScroll( reg );
	setBackgroundInert( reg.backdrop, true );
	if ( reg.video && ctx.autoplay ) {
		try {
			const p = reg.video.play();
			if ( p && typeof p.catch === 'function' ) {
				p.catch( () => {} );
			}
		} catch ( e ) {}
	}
	window.requestAnimationFrame( () => {
		( reg.close || reg.dialog ).focus();
	} );
}

function onClose( reg ) {
	unlockScroll( reg );
	setBackgroundInert( reg.backdrop, false );
	if ( reg.video ) {
		reg.video.pause();
	}
	if ( reg.trigger && typeof reg.trigger.focus === 'function' ) {
		reg.trigger.focus();
	}
}

store( 'etu/video-popup', {
	actions: {
		open() {
			const ctx = getContext();
			const reg = regFor( getElement().ref );
			if ( reg ) {
				reg.trigger = getElement().ref;
			}
			if ( 'self' !== ctx.type ) {
				ctx.iframeSrc = ctx.embedUrl;
			}
			ctx.open = true;
		},
		close() {
			const ctx = getContext();
			ctx.open = false;
			if ( 'self' !== ctx.type ) {
				ctx.iframeSrc = 'about:blank';
			}
		},
		backdropClick: withSyncEvent( ( event ) => {
			if ( event.target === getElement().ref ) {
				const ctx = getContext();
				ctx.open = false;
				if ( 'self' !== ctx.type ) {
					ctx.iframeSrc = '';
				}
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
				if ( 'self' !== ctx.type ) {
					ctx.iframeSrc = '';
				}
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
			const root = getElement().ref;
			const reg = {
				backdrop: root.querySelector( '.etu-video-popup__backdrop' ),
				dialog: root.querySelector( '.etu-video-popup__dialog' ),
				close: root.querySelector( '.etu-video-popup__close' ),
				video: root.querySelector( '.etu-video-popup__video' ),
				trigger: null,
				wasOpen: false,
			};
			registry.set( root, reg );
		},
		onToggle() {
			const ctx = getContext();
			const reg = regFor( getElement().ref );
			if ( ! reg ) {
				return;
			}
			if ( ctx.open && ! reg.wasOpen ) {
				reg.wasOpen = true;
				onOpen( reg, ctx );
			} else if ( ! ctx.open && reg.wasOpen ) {
				reg.wasOpen = false;
				onClose( reg );
			}
		},
	},
} );
