<?php

/**
 * Render do bloco no front-end.
 *
 * Variáveis disponíveis automaticamente aqui:
 * $attributes (array)    - atributos do bloco
 * $content    (string)   - conteúdo salvo pelo save.js (vazio, bloco dinâmico)
 * $block      (WP_Block) - instância do bloco
 */

$images   = $attributes['images'] ?? [];
$layout   = $attributes['layout'] ?? 'boxed';
$autoplay = $attributes['autoplay'] ?? true;
$interval = $attributes['interval'] ?? 3000;

// Sem imagens, não faz sentido renderizar o slider.
if (empty($images)) {
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
		<?php foreach ($images as $index => $image) : ?>
			<div
				class="vidal-slider__slide"
				data-slide-index="<?php echo esc_attr($index); ?>">
				<img
					src="<?php echo esc_url($image['url']); ?>"
					alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" />
			</div>
		<?php endforeach; ?>
	</div>

	<?php if (count($images) > 1) : ?>
		<div class="vidal-slider__dots">
			<?php foreach ($images as $index => $image) : ?>
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
