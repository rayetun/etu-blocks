/**
 * Modal / Popup block registration.
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';
import Edit from './edit';

import './style.scss';

registerBlockType( metadata.name, {
	icon: { src: metadata.icon, foreground: '#635bff' },
	edit: Edit,
	// Dynamic block: render.php wraps the saved inner blocks in the dialog markup.
	save: () => <InnerBlocks.Content />,
} );
