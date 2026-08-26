import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	RangeControl,
	TextControl,
	RadioControl,
	Button,
	Placeholder,
} from '@wordpress/components';
import { image as imageIcon } from '@wordpress/icons';

import {
	isValidLinkUrl,
	mergeSelectedImages,
	hasAnyInvalidLink,
} from './slider-images';

import './editor.scss';

const HEIGHT_UNIT_OPTIONS = [
	{ label: __( 'Pixels (px)', 'vidal-slider-block' ), value: 'px' },
	{ label: __( 'Viewport height (vh)', 'vidal-slider-block' ), value: 'vh' },
	{ label: __( 'Em (em)', 'vidal-slider-block' ), value: 'em' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		images,
		layout,
		interval,
		autoplay,
		showDots,
		showArrows,
		heightDesktop,
		heightUnitDesktop,
		heightMobile,
		heightUnitMobile,
	} = attributes;
	const blockProps = useBlockProps();

	function onSelectImages( newImages ) {
		setAttributes( {
			images: mergeSelectedImages( images, newImages ),
		} );
	}

	function removeImage( idToRemove ) {
		const newImages = images.filter( ( image ) => image.id !== idToRemove );
		setAttributes( { images: newImages } );
	}

	function updateSlideLinkUrl( imageId, url ) {
		const newImages = images.map( ( image ) =>
			image.id === imageId ? { ...image, link: { url } } : image
		);
		setAttributes( { images: newImages } );
	}

	const hasInvalidLink = hasAnyInvalidLink( images );

	const { lockPostSaving, unlockPostSaving } = useDispatch( editorStore );
	const lockName = `vidal-slider-block-invalid-link-${ clientId }`;

	useEffect( () => {
		if ( ! lockPostSaving || ! unlockPostSaving ) {
			return;
		}
		if ( hasInvalidLink ) {
			lockPostSaving( lockName );
		} else {
			unlockPostSaving( lockName );
		}
		return () => unlockPostSaving( lockName );
	}, [ hasInvalidLink, lockName, lockPostSaving, unlockPostSaving ] );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Configurações do Slider',
						'vidal-slider-block'
					) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __( 'Full width', 'vidal-slider-block' ) }
						help={
							layout === 'full'
								? __(
										'O slider vai ocupar toda a largura da tela.',
										'vidal-slider-block'
								  )
								: __(
										'O slider vai respeitar a largura do conteúdo.',
										'vidal-slider-block'
								  )
						}
						checked={ layout === 'full' }
						onChange={ ( value ) =>
							setAttributes( {
								layout: value ? 'full' : 'boxed',
							} )
						}
					/>
					<ToggleControl
						label={ __( 'Autoplay', 'vidal-slider-block' ) }
						help={
							autoplay
								? __(
										'O slider avança automaticamente.',
										'vidal-slider-block'
								  )
								: __(
										'O slider só avança por interação do usuário.',
										'vidal-slider-block'
								  )
						}
						checked={ autoplay }
						onChange={ ( value ) =>
							setAttributes( { autoplay: value } )
						}
					/>
					<RangeControl
						label={ __(
							'Intervalo entre slides (segundos)',
							'vidal-slider-block'
						) }
						value={ interval / 1000 }
						onChange={ ( value ) =>
							setAttributes( { interval: value * 1000 } )
						}
						min={ 1 }
						max={ 10 }
						step={ 0.5 }
					/>
					<ToggleControl
						label={ __(
							'Mostrar bolinhas de navegação',
							'vidal-slider-block'
						) }
						checked={ showDots }
						onChange={ ( value ) =>
							setAttributes( { showDots: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'Mostrar setas de navegação',
							'vidal-slider-block'
						) }
						checked={ showArrows }
						onChange={ ( value ) =>
							setAttributes( { showArrows: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Altura do Slider', 'vidal-slider-block' ) }
					initialOpen={ false }
				>
					<TextControl
						type="number"
						label={ __( 'Altura', 'vidal-slider-block' ) }
						value={ heightDesktop }
						min={ 0 }
						onChange={ ( value ) =>
							setAttributes( {
								heightDesktop:
									value === '' ? 0 : Number( value ),
							} )
						}
					/>
					<RadioControl
						label={ __( 'Unidade', 'vidal-slider-block' ) }
						selected={ heightUnitDesktop }
						options={ HEIGHT_UNIT_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { heightUnitDesktop: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __(
						'Altura do Slider (Mobile)',
						'vidal-slider-block'
					) }
					initialOpen={ false }
				>
					<TextControl
						type="number"
						label={ __( 'Altura', 'vidal-slider-block' ) }
						value={ heightMobile }
						min={ 0 }
						onChange={ ( value ) =>
							setAttributes( {
								heightMobile:
									value === '' ? 0 : Number( value ),
							} )
						}
					/>
					<RadioControl
						label={ __( 'Unidade', 'vidal-slider-block' ) }
						selected={ heightUnitMobile }
						options={ HEIGHT_UNIT_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { heightUnitMobile: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ images.length === 0 ? (
					<Placeholder
						icon={ imageIcon }
						label={ __( 'Vidal Slider', 'vidal-slider-block' ) }
						instructions={ __(
							'Adicione uma ou mais imagens para montar o slider.',
							'vidal-slider-block'
						) }
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ onSelectImages }
								allowedTypes={ [ 'image' ] }
								multiple
								gallery
								render={ ( { open } ) => (
									<Button variant="primary" onClick={ open }>
										{ __(
											'Adicionar imagens',
											'vidal-slider-block'
										) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					</Placeholder>
				) : (
					<div className="vidal-slider-block__grid">
						{ images.map( ( image ) => {
							const linkUrl = image.link?.url ?? '';
							const trimmedUrl = linkUrl.trim();
							const hasUrl = trimmedUrl !== '';
							const isInvalid =
								hasUrl && ! isValidLinkUrl( trimmedUrl );

							return (
								<div
									className="vidal-slider-block__grid-item"
									key={ image.id }
								>
									<div className="vidal-slider-block__grid-item-media">
										<img
											src={ image.url }
											alt={ image.alt }
										/>
										<Button
											className="vidal-slider-block__remove-btn"
											isDestructive
											size="small"
											onClick={ () =>
												removeImage( image.id )
											}
										>
											{ __(
												'Remover',
												'vidal-slider-block'
											) }
										</Button>
									</div>
									<TextControl
										label={ __(
											'Link (opcional)',
											'vidal-slider-block'
										) }
										placeholder={ __(
											'/pagina ou https://…',
											'vidal-slider-block'
										) }
										value={ linkUrl }
										className={
											isInvalid
												? 'vidal-slider-block__link-input has-error'
												: 'vidal-slider-block__link-input'
										}
										help={
											isInvalid
												? __(
														'Use um link relativo (iniciando com /) ou absoluto (http:// ou https://).',
														'vidal-slider-block'
												  )
												: undefined
										}
										onChange={ ( value ) =>
											updateSlideLinkUrl(
												image.id,
												value
											)
										}
									/>
								</div>
							);
						} ) }

						<MediaUploadCheck>
							<MediaUpload
								onSelect={ onSelectImages }
								allowedTypes={ [ 'image' ] }
								multiple
								gallery
								value={ images.map( ( image ) => image.id ) }
								render={ ( { open } ) => (
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __(
											'Editar imagens',
											'vidal-slider-block'
										) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					</div>
				) }
			</div>
		</>
	);
}
