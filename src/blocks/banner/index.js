/**
 * Banner (Hero) block registration.
 */
import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';

import './style.scss';

registerBlockType( metadata.name, {
	icon: { src: metadata.icon, foreground: '#635bff' },
	edit: Edit,
	// Dynamic block: markup is produced by render.php on the server.
	save: () => null,
} );
