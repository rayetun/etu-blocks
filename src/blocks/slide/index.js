/**
 * Slide block registration (child of etu/slides).
 *
 * Static: its inner blocks are serialized into the parent, which re-renders each
 * slide server-side inside an accessible slide group.
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

import metadata from './block.json';

registerBlockType( metadata.name, {
	icon: { src: metadata.icon, foreground: '#635bff' },
	edit: () => {
		const blockProps = useBlockProps( {
			className: 'etu-slide etu-slide--editor',
		} );
		const innerProps = useInnerBlocksProps( blockProps, {
			template: [
				[
					'core/paragraph',
					{ placeholder: __( 'Slide content…', 'etu-blocks' ) },
				],
			],
			templateLock: false,
		} );
		return <div { ...innerProps } />;
	},
	save: () => {
		const blockProps = useBlockProps.save( { className: 'etu-slide' } );
		const innerProps = useInnerBlocksProps.save( blockProps );
		return <div { ...innerProps } />;
	},
} );
