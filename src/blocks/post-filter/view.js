/**
 * Post Filter — Interactivity API store.
 *
 * Enhances the server-rendered filter bar: search (debounced), taxonomy dropdowns
 * and reset re-query via the REST route and swap the results in place; "Load more"
 * appends the next page. The results region reports aria-busy while fetching and a
 * polite live region announces the new count. With JS off, the GET form and the
 * real next-page link keep everything working.
 */
import {
	store,
	getContext,
	getElement,
	withSyncEvent,
} from '@wordpress/interactivity';

const timers = new WeakMap();

const rootOf = ( el ) => ( el ? el.closest( '.etu-post-filter' ) : null );

function buildUrl( ctx, page ) {
	const url = new URL( ctx.restUrl );
	const p = url.searchParams;
	p.set( 'postType', ctx.cfg.postType );
	p.set( 'perPage', String( ctx.cfg.perPage ) );
	p.set( 'orderBy', ctx.cfg.orderBy );
	p.set( 'order', ctx.cfg.order );
	p.set( 'page', String( page ) );
	if ( ctx.filters.s ) {
		p.set( 's', ctx.filters.s );
	}
	( ctx.cfg.taxonomies || [] ).forEach( ( tax ) => {
		const slug = ctx.filters.terms[ tax ];
		if ( slug ) {
			p.set( `tax[${ tax }]`, slug );
		}
	} );
	const card = ctx.cfg.card || {};
	Object.keys( card ).forEach( ( k ) => {
		const v = card[ k ];
		p.set( `card[${ k }]`, typeof v === 'boolean' ? ( v ? '1' : '0' ) : String( v ) );
	} );
	return url.toString();
}

async function run( root, ctx, page, append ) {
	if ( ! root ) {
		return;
	}
	ctx.loading = true;
	const list = root.querySelector( '.etu-post-filter__list' );
	const before = list ? list.children.length : 0;
	try {
		const res = await window.fetch( buildUrl( ctx, page ), {
			headers: { 'X-WP-Nonce': ctx.nonce },
		} );
		const data = await res.json();
		if ( list ) {
			if ( append ) {
				list.insertAdjacentHTML( 'beforeend', data.html );
			} else {
				list.innerHTML = data.html;
			}
		}
		ctx.page = data.page;
		ctx.pages = data.pages;
		ctx.total = data.total;
		ctx.hasMore = data.page < data.pages;
		ctx.statusText = ( ctx.statusTemplate || '%s' ).replace(
			'%s',
			Number( data.total ).toLocaleString()
		);

		// Move focus to the first newly appended card so keyboard users land there.
		if ( append && list && list.children[ before ] ) {
			const link =
				list.children[ before ].querySelector(
					'.etu-post-filter__title a'
				) || list.children[ before ];
			link.focus();
		}
	} catch ( e ) {
		// Leave the current results in place on failure.
	} finally {
		ctx.loading = false;
	}
}

store( 'etu/post-filter', {
	actions: {
		search() {
			const ctx = getContext();
			const input = getElement().ref;
			const root = rootOf( input );
			ctx.filters.s = input.value;
			const existing = timers.get( root );
			if ( existing ) {
				window.clearTimeout( existing );
			}
			timers.set(
				root,
				window.setTimeout( () => run( root, ctx, 1, false ), 350 )
			);
		},

		filterTax() {
			const ctx = getContext();
			const select = getElement().ref;
			const tax = select.getAttribute( 'data-tax' );
			ctx.filters.terms[ tax ] = select.value;
			run( rootOf( select ), ctx, 1, false );
		},

		reset: withSyncEvent( ( event ) => {
			event.preventDefault();
			const ctx = getContext();
			const root = rootOf( getElement().ref );
			ctx.filters.s = '';
			ctx.filters.terms = {};
			if ( root ) {
				const search = root.querySelector( '.etu-post-filter__search' );
				if ( search ) {
					search.value = '';
				}
				root
					.querySelectorAll( '.etu-post-filter__select' )
					.forEach( ( s ) => {
						s.value = '';
					} );
			}
			run( root, ctx, 1, false );
		} ),

		submit: withSyncEvent( ( event ) => {
			event.preventDefault();
			const ctx = getContext();
			run( rootOf( getElement().ref ), ctx, 1, false );
		} ),

		loadMore: withSyncEvent( ( event ) => {
			event.preventDefault();
			const ctx = getContext();
			run( rootOf( getElement().ref ), ctx, ctx.page + 1, true );
		} ),
	},
} );
