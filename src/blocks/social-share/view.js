/**
 * Social Share — Interactivity API store.
 *
 * Copy link writes the page URL to the clipboard (with a fallback for non-secure
 * contexts) and announces the result through a polite live region. Native share
 * uses the Web Share API and only reveals itself when the browser supports it.
 */
import { store, getContext } from '@wordpress/interactivity';

function copyText( text ) {
	if ( navigator.clipboard && window.isSecureContext ) {
		return navigator.clipboard.writeText( text );
	}
	// Fallback for http / older browsers.
	const area = document.createElement( 'textarea' );
	area.value = text;
	area.setAttribute( 'readonly', '' );
	area.style.position = 'fixed';
	area.style.opacity = '0';
	document.body.appendChild( area );
	area.select();
	try {
		document.execCommand( 'copy' );
	} catch ( e ) {
		// Ignore — nothing more we can do.
	}
	document.body.removeChild( area );
	return Promise.resolve();
}

store( 'etu/social-share', {
	state: {
		get statusText() {
			const ctx = getContext();
			return ctx.copied ? ctx.copiedText : '';
		},
	},
	actions: {
		*copy() {
			const ctx = getContext();
			yield copyText( ctx.url );
			ctx.copied = true;
			if ( ctx.timer ) {
				clearTimeout( ctx.timer );
			}
			ctx.timer = setTimeout( () => {
				ctx.copied = false;
			}, 2500 );
		},
		nativeShare() {
			const ctx = getContext();
			if ( navigator.share ) {
				navigator.share( { title: ctx.title, url: ctx.url } ).catch( () => {} );
			}
		},
	},
	callbacks: {
		init() {
			const ctx = getContext();
			ctx.canShare =
				typeof navigator !== 'undefined' &&
				typeof navigator.share === 'function';
		},
	},
} );
