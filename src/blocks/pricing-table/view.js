/**
 * Pricing Table — Interactivity API store (billing toggle only).
 *
 * Switches every plan's price between monthly and yearly. Prices are server-
 * rendered (monthly visible by default), so they still work with JS disabled.
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'etu/pricing', {
	actions: {
		setMonthly() {
			getContext().yearly = false;
		},
		setYearly() {
			getContext().yearly = true;
		},
	},
} );
