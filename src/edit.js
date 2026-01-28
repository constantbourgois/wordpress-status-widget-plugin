/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { useState, useEffect, useCallback } from '@wordpress/element';

/**
 * Editor component for the OpenStatus Badge block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to set block attributes.
 * @return {JSX.Element} Block editor component.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { theme, size, variant } = attributes;
	const blockProps = useBlockProps();

	const [ badgeSvg, setBadgeSvg ] = useState( '' );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ error, setError ] = useState( false );

	// Get settings from localized script data.
	const settings = window.openstatusBadgeSettings || {};
	const { slug, settingsUrl } = settings;

	/**
	 * Build the badge URL with current attributes.
	 */
	const buildBadgeUrl = useCallback( () => {
		if ( ! slug ) {
			return null;
		}

		const params = new URLSearchParams();

		if ( theme && theme !== 'light' ) {
			params.append( 'theme', theme );
		}

		if ( size && size !== 'sm' ) {
			params.append( 'size', size );
		}

		if ( variant ) {
			params.append( 'variant', variant );
		}

		const queryString = params.toString();
		const baseUrl = `https://${ slug }.openstatus.dev/badge`;

		return queryString ? `${ baseUrl }?${ queryString }` : baseUrl;
	}, [ slug, theme, size, variant ] );

	/**
	 * Fetch the badge SVG.
	 */
	useEffect( () => {
		const url = buildBadgeUrl();

		if ( ! url ) {
			return;
		}

		setIsLoading( true );
		setError( false );

		fetch( url, {
			headers: {
				Accept: 'image/svg+xml',
			},
		} )
			.then( ( response ) => {
				if ( ! response.ok ) {
					throw new Error( 'Failed to fetch badge' );
				}
				return response.text();
			} )
			.then( ( svg ) => {
				setBadgeSvg( svg );
				setIsLoading( false );
			} )
			.catch( () => {
				setError( true );
				setIsLoading( false );
			} );
	}, [ buildBadgeUrl ] );

	// If no slug is configured, show placeholder.
	if ( ! slug ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="chart-line"
					label={ __( 'OpenStatus Badge', 'openstatus-badge' ) }
					instructions={ __(
						'Configure your OpenStatus status page slug to display the badge.',
						'openstatus-badge'
					) }
				>
					<a href={ settingsUrl } className="components-button is-primary">
						{ __( 'Go to Settings', 'openstatus-badge' ) }
					</a>
				</Placeholder>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Badge Settings', 'openstatus-badge' ) }
				>
					<SelectControl
						label={ __( 'Theme', 'openstatus-badge' ) }
						value={ theme }
						options={ [
							{ label: __( 'Light', 'openstatus-badge' ), value: 'light' },
							{ label: __( 'Dark', 'openstatus-badge' ), value: 'dark' },
						] }
						onChange={ ( value ) =>
							setAttributes( { theme: value } )
						}
					/>
					<SelectControl
						label={ __( 'Size', 'openstatus-badge' ) }
						value={ size }
						options={ [
							{ label: __( 'Small', 'openstatus-badge' ), value: 'sm' },
							{ label: __( 'Medium', 'openstatus-badge' ), value: 'md' },
							{ label: __( 'Large', 'openstatus-badge' ), value: 'lg' },
							{ label: __( 'Extra Large', 'openstatus-badge' ), value: 'xl' },
						] }
						onChange={ ( value ) =>
							setAttributes( { size: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Outline variant', 'openstatus-badge' ) }
						checked={ variant === 'outline' }
						onChange={ ( checked ) =>
							setAttributes( { variant: checked ? 'outline' : '' } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ isLoading && (
					<div className="openstatus-badge-loading">
						<Spinner />
					</div>
				) }
				{ error && ! isLoading && (
					<div className="openstatus-badge-error">
						{ __( 'Status unavailable', 'openstatus-badge' ) }
					</div>
				) }
				{ badgeSvg && ! isLoading && ! error && (
					<div
						className="openstatus-badge-preview"
						dangerouslySetInnerHTML={ { __html: badgeSvg } }
					/>
				) }
			</div>
		</>
	);
}
