/**
 * Block slug from a block name: "etu/info-box" → "info-box".
 *
 * @param {string} name Block name.
 * @return {string} Slug.
 */
export function getBlockSlug( name ) {
	return String( name ).replace( /^etu\//, '' );
}
