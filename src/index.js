/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';
import './editor.scss';

/**
 * Register the OpenStatus Badge block.
 */
registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null, // Server-side rendered
} );
