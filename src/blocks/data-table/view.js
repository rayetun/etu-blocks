/**
 * Data Table — Interactivity API store.
 *
 * Sorting and search are progressive enhancements over the server-rendered table.
 * Both work directly on the existing <tr> nodes (kept out of the reactive context,
 * like the carousel): sort reorders the rows and updates aria-sort on the headers
 * (the standard signal assistive tech announces); search toggles each row's hidden
 * flag from its data-search haystack and reports the count in a polite live region.
 */
import { store, getElement } from '@wordpress/interactivity';

const cellText = ( row, index ) => {
	const cell = row.cells[ index ];
	return cell ? cell.textContent.trim() : '';
};

const toNumber = ( value ) => {
	const n = parseFloat( String( value ).replace( /[^0-9.\-]/g, '' ) );
	return Number.isNaN( n ) ? 0 : n;
};

store( 'etu/data-table', {
	actions: {
		sort() {
			const btn = getElement().ref;
			const table = btn.closest( 'table' );
			const th = btn.closest( 'th' );
			if ( ! table || ! th ) {
				return;
			}
			const index = Number( th.getAttribute( 'data-col-index' ) );
			const type = th.getAttribute( 'data-col-type' ) || 'text';
			const dir =
				th.getAttribute( 'aria-sort' ) === 'ascending'
					? 'descending'
					: 'ascending';

			table
				.querySelectorAll( 'th[aria-sort]' )
				.forEach( ( h ) => h.setAttribute( 'aria-sort', 'none' ) );
			th.setAttribute( 'aria-sort', dir );

			const tbody = table.tBodies[ 0 ];
			if ( ! tbody ) {
				return;
			}
			const factor = dir === 'ascending' ? 1 : -1;
			const rows = Array.from( tbody.rows );
			rows.sort( ( a, b ) => {
				const av = cellText( a, index );
				const bv = cellText( b, index );
				if ( type === 'number' ) {
					return ( toNumber( av ) - toNumber( bv ) ) * factor;
				}
				return (
					av.localeCompare( bv, undefined, {
						numeric: true,
						sensitivity: 'base',
					} ) * factor
				);
			} );
			rows.forEach( ( r ) => tbody.appendChild( r ) );
		},

		search() {
			const input = getElement().ref;
			const root = input.closest( '.etu-data-table' );
			if ( ! root ) {
				return;
			}
			const term = input.value.trim().toLowerCase();
			const rows = root.querySelectorAll( 'tbody tr' );
			let shown = 0;
			rows.forEach( ( row ) => {
				const match =
					! term ||
					( row.getAttribute( 'data-search' ) || '' ).indexOf(
						term
					) !== -1;
				row.hidden = ! match;
				if ( match ) {
					shown++;
				}
			} );
			const status = root.querySelector( '.etu-data-table__status' );
			if ( status ) {
				const tpl = status.getAttribute( 'data-tpl' ) || '';
				status.textContent = term
					? tpl
							.replace( '%1$s', String( shown ) )
							.replace( '%2$s', String( rows.length ) )
					: '';
			}
		},
	},
} );
