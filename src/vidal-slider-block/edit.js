import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	PanelBody,
	ToggleControl,
	RangeControl,
	Button,
	Placeholder,
} from "@wordpress/components";
import { image as imageIcon } from "@wordpress/icons";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { images, layout, interval, autoplay } = attributes;
	const blockProps = useBlockProps();

	function onSelectImages(newImages) {
		const formattedImages = newImages.map((image) => ({
			id: image.id,
			url: image.url,
			alt: image.alt || "",
		}));
		setAttributes({ images: formattedImages });
	}

	function removeImage(idToRemove) {
		const newImages = images.filter((image) => image.id !== idToRemove);
		setAttributes({ images: newImages });
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Configurações do Slider", "vidal-slider-block")}
					initialOpen={true}
				>
					<ToggleControl
						label={__("Full width", "vidal-slider-block")}
						help={
							layout === "full"
								? __(
										"O slider vai ocupar toda a largura da tela.",
										"vidal-slider-block",
								  )
								: __(
										"O slider vai respeitar a largura do conteúdo.",
										"vidal-slider-block",
								  )
						}
						checked={layout === "full"}
						onChange={(value) =>
							setAttributes({ layout: value ? "full" : "boxed" })
						}
					/>
					<ToggleControl
						label={__("Autoplay", "vidal-slider-block")}
						help={
							autoplay
								? __("O slider avança automaticamente.", "vidal-slider-block")
								: __(
										"O slider só avança por interação do usuário.",
										"vidal-slider-block",
								  )
						}
						checked={autoplay}
						onChange={(value) => setAttributes({ autoplay: value })}
					/>
					<RangeControl
						label={__(
							"Intervalo entre slides (segundos)",
							"vidal-slider-block",
						)}
						value={interval / 1000}
						onChange={(value) => setAttributes({ interval: value * 1000 })}
						min={1}
						max={10}
						step={0.5}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{images.length === 0 ? (
					<Placeholder
						icon={imageIcon}
						label={__("Vidal Slider", "vidal-slider-block")}
						instructions={__(
							"Adicione uma ou mais imagens para montar o slider.",
							"vidal-slider-block",
						)}
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImages}
								allowedTypes={["image"]}
								multiple
								gallery
								render={({ open }) => (
									<Button variant="primary" onClick={open}>
										{__("Adicionar imagens", "vidal-slider-block")}
									</Button>
								)}
							/>
						</MediaUploadCheck>
					</Placeholder>
				) : (
					<div className="vidal-slider-block__grid">
						{images.map((image, index) => (
							<div className="vidal-slider-block__grid-item" key={image.id}>
								<img src={image.url} alt={image.alt} />
								<Button
									className="vidal-slider-block__remove-btn"
									isDestructive
									size="small"
									onClick={() => removeImage(image.id)}
								>
									{__("Remover", "vidal-slider-block")}
								</Button>
							</div>
						))}

						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImages}
								allowedTypes={["image"]}
								multiple
								gallery
								value={images.map((image) => image.id)}
								render={({ open }) => (
									<Button variant="secondary" onClick={open}>
										{__("Editar imagens", "vidal-slider-block")}
									</Button>
								)}
							/>
						</MediaUploadCheck>
					</div>
				)}
			</div>
		</>
	);
}
