/**
 * Etu Blocks — admin dashboard: search, type filter, and enable/disable toggles.
 */
( function () {
	const root = document.querySelector( '.etu-admin' );
	if ( ! root ) {
		return;
	}

	const search = root.querySelector( '.etu-blocks-search' );
	const filters = Array.prototype.slice.call(
		root.querySelectorAll( '.etu-blocks-filter' )
	);
	const cards = Array.prototype.slice.call(
		root.querySelectorAll( '.etu-blocks-card' )
	);
	const noResults = root.querySelector( '.etu-blocks-noresults' );

	/* ---- Colour theme (manual light / dark, persisted) ---- */
	const themeToggle = root.querySelector( '.etu-admin__theme-toggle' );
	const THEME_KEY = 'etuAdminTheme';

	function systemDark() {
		return (
			window.matchMedia &&
			window.matchMedia( '(prefers-color-scheme: dark)' ).matches
		);
	}
	function activeTheme() {
		return (
			root.getAttribute( 'data-theme' ) ||
			( systemDark() ? 'dark' : 'light' )
		);
	}
	function paintToggle() {
		const icon = root.querySelector( '.etu-admin__theme-icon' );
		if ( icon ) {
			icon.textContent = activeTheme() === 'dark' ? '☀' : '☾';
		}
	}
	try {
		const saved = window.localStorage.getItem( THEME_KEY );
		if ( saved === 'dark' || saved === 'light' ) {
			root.setAttribute( 'data-theme', saved );
		}
	} catch ( e ) {}
	paintToggle();
	if ( themeToggle ) {
		themeToggle.addEventListener( 'click', function () {
			const next = activeTheme() === 'dark' ? 'light' : 'dark';
			root.setAttribute( 'data-theme', next );
			try {
				window.localStorage.setItem( THEME_KEY, next );
			} catch ( e ) {}
			paintToggle();
		} );
	}

	let group = 'all';
	let term = '';

	function apply() {
		let visible = 0;
		cards.forEach( function ( card ) {
			const matchGroup =
				group === 'all' || card.getAttribute( 'data-group' ) === group;
			const matchTerm =
				! term ||
				( card.getAttribute( 'data-search' ) || '' ).indexOf( term ) !==
					-1;
			const show = matchGroup && matchTerm;
			card.hidden = ! show;
			if ( show ) {
				visible++;
			}
		} );
		if ( noResults ) {
			noResults.hidden = visible !== 0;
		}
	}

	if ( search ) {
		search.addEventListener( 'input', function () {
			term = search.value.trim().toLowerCase();
			apply();
		} );
	}

	filters.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			filters.forEach( function ( b ) {
				b.classList.remove( 'is-active' );
				b.setAttribute( 'aria-pressed', 'false' );
			} );
			btn.classList.add( 'is-active' );
			btn.setAttribute( 'aria-pressed', 'true' );
			group = btn.getAttribute( 'data-group' );
			apply();
		} );
	} );

	const admin = window.etublocksAdmin || {};

	root.querySelectorAll( '.etu-blocks-toggle' ).forEach( function ( input ) {
		input.addEventListener( 'change', function () {
			const block = input.getAttribute( 'data-block' );
			const enabled = input.checked;
			const card = input.closest( '.etu-blocks-card' );
			if ( card ) {
				card.classList.toggle( 'is-disabled', ! enabled );
			}

			const revert = function () {
				input.checked = ! enabled;
				if ( card ) {
					card.classList.toggle( 'is-disabled', enabled );
				}
			};

			if ( ! admin.ajaxUrl ) {
				return;
			}

			const body = new URLSearchParams();
			body.set( 'action', 'etublocks_toggle' );
			body.set( 'nonce', admin.nonce || '' );
			body.set( 'block', block );
			body.set( 'enabled', enabled ? '1' : '0' );

			input.disabled = true;
			window
				.fetch( admin.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: body.toString(),
				} )
				.then( function ( r ) {
					return r.json();
				} )
				.then( function ( res ) {
					if ( ! res || ! res.success ) {
						revert();
					}
				} )
				.catch( revert )
				.finally( function () {
					input.disabled = false;
				} );
		} );
	} );
} )();
