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
	Button,
	Placeholder,
} from '@wordpress/components';
import { image as imageIcon } from '@wordpress/icons';

import './editor.scss';

// Link relativo (uma única "/", não "//..." — isso é protocol-relative,
// um vetor de open-redirect) ou absoluto (http/https). A mesma regra é
// reaplicada em render.php, que é quem de fato decide o que é renderizado —
// esta validação aqui é só para a experiência de edição.
const LINK_URL_PATTERN = /^(\/(?!\/)|https?:\/\/)/i;

function isValidLinkUrl( url ) {
	return LINK_URL_PATTERN.test( url );
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { images, layout, interval, autoplay } = attributes;
	const blockProps = useBlockProps();

	function onSelectImages( newImages ) {
		// Preserva o link já configurado em cada imagem ao reabrir a seleção
		// da biblioteca de mídia — sem isso, editar a seleção apagaria todos
		// os links salvos.
		const previousById = new Map(
			images.map( ( image ) => [ image.id, image ] )
		);
		const formattedImages = newImages.map( ( image ) => {
			const previous = previousById.get( image.id );
			return {
				id: image.id,
				url: image.url,
				alt: image.alt || '',
				link: previous?.link,
			};
		} );
		setAttributes( { images: formattedImages } );
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

	const hasInvalidLink = images.some( ( image ) => {
		const url = ( image.link?.url ?? '' ).trim();
		return url !== '' && ! isValidLinkUrl( url );
	} );

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
										placeholder="/pagina ou https://..."
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
