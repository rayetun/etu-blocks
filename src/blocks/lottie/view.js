/**
 * Lottie — Interactivity API store.
 *
 * The lottie-web player (light SVG build) is bundled here — no CDN request. On
 * hydration we create the animation (paused) and start it according to the trigger:
 * on load, when it scrolls into view (IntersectionObserver), on hover/focus, or on
 * click. prefers-reduced-motion suppresses the automatic triggers and shows a still
 * first frame; hover/click stay available since they're user-initiated. The player
 * instance lives in a WeakMap, out of the reactive context.
 *
 * Handles both plain .json Lottie exports and .lottie (dotLottie) archives —
 * dotLottie files are zip archives, so we fetch, unzip in the browser with fflate,
 * pull the first animation JSON out and hand it to lottie-web as animationData.
 */
import lottie from 'lottie-web/build/player/lottie_light';
import { unzipSync, strFromU8 } from 'fflate';
import { store, getContext, getElement } from '@wordpress/interactivity';

const registry = new WeakMap();

const prefersReduced = () =>
	window.matchMedia &&
	window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

const regOf = ( el ) => {
	const root = el ? el.closest( '.etu-lottie' ) : null;
	return root ? registry.get( root ) : undefined;
};

const isDotLottie = ( url ) => /\.lottie(\?|#|$)/i.test( url || '' );

/**
 * Fetch a .lottie archive and return the first animation JSON inside it.
 *
 * @param {string} url The .lottie URL.
 * @return {Promise<Object>} The parsed animation JSON.
 */
async function loadDotLottie( url ) {
	const res = await window.fetch( url );
	if ( ! res.ok ) {
		throw new Error( 'fetch failed' );
	}
	const buf = new Uint8Array( await res.arrayBuffer() );
	const entries = unzipSync( buf );
	// dotLottie spec: animations/{id}.json — pick the first one.
	const key = Object.keys( entries ).find( ( name ) =>
		/^animations\/.+\.json$/i.test( name )
	);
	if ( ! key ) {
		throw new Error( 'no animation in .lottie archive' );
	}
	return JSON.parse( strFromU8( entries[ key ] ) );
}

function build( canvas, ctx ) {
	if ( isDotLottie( ctx.src ) ) {
		return loadDotLottie( ctx.src ).then( ( data ) =>
			lottie.loadAnimation( {
				container: canvas,
				renderer: 'svg',
				loop: !! ctx.loop,
				autoplay: false,
				animationData: data,
			} )
		);
	}
	return Promise.resolve(
		lottie.loadAnimation( {
			container: canvas,
			renderer: 'svg',
			loop: !! ctx.loop,
			autoplay: false,
			path: ctx.src,
		} )
	);
}

store( 'etu/lottie', {
	callbacks: {
		init() {
			const ctx = getContext();
			const root = getElement().ref;
			const canvas = root.querySelector( '.etu-lottie__canvas' );
			if ( ! canvas || ! ctx.src || registry.has( root ) ) {
				return;
			}

			const reg = { anim: null };
			registry.set( root, reg );
			const reduced = prefersReduced();

			build( canvas, ctx )
				.then( ( anim ) => {
					reg.anim = anim;
					anim.setSpeed( ctx.speed || 1 );

					if ( ctx.trigger === 'autoplay' ) {
						if ( ! reduced ) {
							anim.play();
						}
						return;
					}

					if ( ctx.trigger === 'viewport' ) {
						if ( reduced ) {
							return;
						}
						const observer = new window.IntersectionObserver(
							( entries, obs ) => {
								entries.forEach( ( entry ) => {
									if ( entry.isIntersecting ) {
										anim.play();
										obs.disconnect();
									}
								} );
							},
							{ threshold: 0.35 }
						);
						observer.observe( canvas );
					}
					// hover / click: wait for the user's action.
				} )
				.catch( () => {
					// Leave the canvas empty on failure; author guidance shows in the editor.
				} );
		},
	},
	actions: {
		playFromStart() {
			const reg = regOf( getElement().ref );
			if ( reg && reg.anim ) {
				reg.anim.goToAndPlay( 0, true );
			}
		},
		pause() {
			const reg = regOf( getElement().ref );
			if ( reg && reg.anim ) {
				reg.anim.pause();
			}
		},
		toggle() {
			const reg = regOf( getElement().ref );
			if ( ! reg || ! reg.anim ) {
				return;
			}
			if ( reg.anim.isPaused ) {
				reg.anim.play();
			} else {
				reg.anim.pause();
			}
		},
	},
} );
