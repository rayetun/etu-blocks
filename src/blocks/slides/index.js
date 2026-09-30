/**
 * Slides / Carousel block registration (parent).
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';
import Edit from './edit';

import './style.scss';

registerBlockType( metadata.name, {
	icon: { src: metadata.icon, foreground: '#635bff' },
	edit: Edit,
	// Dynamic parent: render.php wraps each inner slide. Inner blocks are
	// persisted via InnerBlocks.Content and re-rendered server-side per slide.
	save: () => <InnerBlocks.Content />,
} );
