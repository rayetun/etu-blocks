/**
 * Google Map — Interactivity API store (privacy facade).
 *
 * The Google iframe idles at about:blank so nothing loads on page view. When the
 * visitor clicks "Load map" we point the iframe at the embed URL, hide the facade,
 * and move focus into the now-interactive map.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'etu/map', {
	actions: {
		load() {
			const ctx = getContext();
			const btn = getElement().ref;
			ctx.iframeSrc = ctx.embedUrl;
			ctx.loaded = true;
			const frame = btn.closest( '.etu-map__frame' );
			const iframe = frame && frame.querySelector( '.etu-map__iframe' );
			if ( iframe ) {
				window.requestAnimationFrame( () => {
					try {
						iframe.focus();
					} catch ( e ) {}
				} );
			}
		},
	},
} );
