<?php

/**
 * Render do bloco no front-end.
 *
 * Variáveis disponíveis automaticamente aqui:
 * $attributes (array)    - atributos do bloco
 * $content    (string)   - conteúdo salvo pelo save.js (vazio, bloco dinâmico)
 * $block      (WP_Block) - instância do bloco
 */

$images   = is_array($attributes['images'] ?? null) ? $attributes['images'] : [];
$layout   = in_array($attributes['layout'] ?? 'boxed', ['boxed', 'full'], true) ? $attributes['layout'] : 'boxed';
$autoplay = (bool) ($attributes['autoplay'] ?? true);
$interval = absint($attributes['interval'] ?? 3000);

// Os atributos do bloco vêm do post_content e não passam pela validação de
// schema do block.json no servidor — nunca confiamos na URL "em cache" que o
// editor salvou. Resolvemos a URL de verdade a partir do ID do anexo, então
// só imagens que realmente existem na biblioteca de mídia são renderizadas.
$slides = [];
foreach ($images as $image) {
	if (! is_array($image) || empty($image['id'])) {
		continue;
	}

	$url = wp_get_attachment_image_url(absint($image['id']), 'full');

	if (! $url) {
		continue;
	}

	$slides[] = [
		'url' => $url,
		'alt' => is_string($image['alt'] ?? null) ? $image['alt'] : '',
	];
}

// Sem imagens, não faz sentido renderizar o slider.
if (empty($slides)) {
	return;
}

$wrapper_classes = 'vidal-slider vidal-slider--' . esc_attr($layout);

// A tema (FSE) restringe a largura de qualquer bloco filho de um grupo
// "is-layout-constrained" a menos que ele tenha a classe alignfull do core,
// então precisamos dela para o breakout de largura total funcionar.
if ('full' === $layout) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class'              => $wrapper_classes,
		'data-autoplay'      => $autoplay ? 'true' : 'false',
		'data-interval'      => esc_attr($interval),
	]
);
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="vidal-slider__track">
		<?php foreach ($slides as $index => $image) : ?>
			<div
				class="vidal-slider__slide"
				data-slide-index="<?php echo esc_attr($index); ?>">
				<img
					src="<?php echo esc_url($image['url']); ?>"
					alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" />
			</div>
		<?php endforeach; ?>
	</div>

	<?php if (count($slides) > 1) : ?>
		<div class="vidal-slider__dots">
			<?php foreach ($slides as $index => $image) : ?>
				<button
					class="vidal-slider__dot<?php echo 0 === $index ? ' is-active' : ''; ?>"
					data-slide-index="<?php echo esc_attr($index); ?>"
					aria-label="<?php echo esc_attr(sprintf(
									/* translators: %d: slide number */
									__('Ir para o slide %d', 'vidal-slider-block'),
									$index + 1
								)); ?>">
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
