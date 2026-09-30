/**
 * Countdown — Interactivity API store.
 *
 * On hydration we resolve the target instant: a fixed date uses the absolute epoch
 * from context; an evergreen countdown reads (or seeds) a per-visitor target in
 * localStorage. A 1s interval updates the day/hour/minute/second context values the
 * digits bind to. On reaching zero we flip `completed` (hiding the timer, showing
 * the message) and announce it once via the polite live region, then stop the timer.
 * The interval id and target live in a WeakMap, out of the reactive context.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const registry = new WeakMap();

const pad = ( n ) => String( n ).padStart( 2, '0' );

const split = ( ms ) => {
	let s = Math.max( 0, Math.floor( ms / 1000 ) );
	const d = Math.floor( s / 86400 );
	s -= d * 86400;
	const h = Math.floor( s / 3600 );
	s -= h * 3600;
	const m = Math.floor( s / 60 );
	s -= m * 60;
	return { d: pad( d ), h: pad( h ), m: pad( m ), s: pad( s ) };
};

store( 'etu/countdown', {
	callbacks: {
		start() {
			const ctx = getContext();
			const root = getElement().ref;

			let target;
			if ( ctx.mode === 'evergreen' ) {
				const key = ctx.storageKey;
				let stored = null;
				try {
					stored = window.localStorage.getItem( key );
				} catch ( e ) {}
				target = stored ? parseInt( stored, 10 ) : NaN;
				if ( ! target || Number.isNaN( target ) ) {
					target = Date.now() + ctx.durationMs;
					try {
						window.localStorage.setItem( key, String( target ) );
					} catch ( e ) {}
				}
			} else {
				target = ctx.targetMs;
			}

			const reg = { target, timer: null };
			registry.set( root, reg );

			const tick = () => {
				const remaining = reg.target - Date.now();
				if ( remaining <= 0 ) {
					ctx.cDays = '00';
					ctx.cHours = '00';
					ctx.cMinutes = '00';
					ctx.cSeconds = '00';
					if ( ! ctx.completed ) {
						ctx.completed = true;
						ctx.liveText =
							root.getAttribute( 'data-completed-text' ) || '';
					}
					if ( reg.timer ) {
						window.clearInterval( reg.timer );
						reg.timer = null;
					}
					return;
				}
				const p = split( remaining );
				ctx.cDays = p.d;
				ctx.cHours = p.h;
				ctx.cMinutes = p.m;
				ctx.cSeconds = p.s;
			};

			tick();
			if ( ! ctx.completed ) {
				reg.timer = window.setInterval( tick, 1000 );
			}
		},
	},
} );
