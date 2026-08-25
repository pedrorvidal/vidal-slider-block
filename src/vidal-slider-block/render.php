<?php

/**
 * Render do bloco no front-end.
 *
 * Variáveis disponíveis automaticamente aqui:
 * $attributes (array)    - atributos do bloco
 * $content    (string)   - conteúdo salvo pelo save.js (vazio, bloco dinâmico)
 * $block      (WP_Block) - instância do bloco
 */

$images      = is_array($attributes['images'] ?? null) ? $attributes['images'] : [];
$layout      = in_array($attributes['layout'] ?? 'boxed', ['boxed', 'full'], true) ? $attributes['layout'] : 'boxed';
$autoplay    = (bool) ($attributes['autoplay'] ?? true);
$interval    = absint($attributes['interval'] ?? 3000);
$show_dots   = (bool) ($attributes['showDots'] ?? true);
$show_arrows = (bool) ($attributes['showArrows'] ?? true);

// Os atributos do bloco vêm do post_content. O WordPress valida o TIPO
// deles contra o block.json (WP_Block_Type::prepare_attributes_for_render()),
// mas não a semântica — uma string continua "válida" mesmo sendo uma URL
// maliciosa ou um link mal formado — e se um único item de um array falhar
// a validação, o atributo inteiro é descartado, não só o item. Por isso
// revalidamos tudo aqui, item a item, em vez de confiar no que foi salvo.
$slides = [];
foreach ($images as $image) {
	if (! is_array($image) || empty($image['id'])) {
		continue;
	}

	$url = wp_get_attachment_image_url(absint($image['id']), 'full');

	if (! $url) {
		continue;
	}

	$slide = [
		'url'         => $url,
		'alt'         => is_string($image['alt'] ?? null) ? $image['alt'] : '',
		'link_url'    => '',
		'link_target' => '_self',
	];

	$link     = is_array($image['link'] ?? null) ? $image['link'] : [];
	$link_url = is_string($link['url'] ?? null) ? trim($link['url']) : '';

	// Link relativo (uma única "/") ou absoluto (http/https). Rejeita
	// propositalmente URLs protocol-relative ("//evil.com"), que apontam
	// pra outro domínio e são um vetor clássico de open-redirect.
	if ('' !== $link_url && preg_match('#^(/(?!/)|https?://)#i', $link_url)) {
		$slide['link_url'] = $link_url;

		// O target não é uma escolha do usuário: link pra fora do domínio do
		// site sempre abre em nova aba, ninguém deveria sair do site sem
		// perceber. Link relativo nunca tem host (é sempre interno); link
		// absoluto só é considerado externo se o host for diferente do
		// domínio atual.
		$link_host = wp_parse_url($link_url, PHP_URL_HOST);
		$site_host = wp_parse_url(home_url(), PHP_URL_HOST);
		$is_external = $link_host && 0 !== strcasecmp($link_host, (string) $site_host);

		$slide['link_target'] = $is_external ? '_blank' : '_self';
	}

	$slides[] = $slide;
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
				<?php if ('' !== $image['link_url']) : ?>
					<a
						class="vidal-slider__slide-link"
						href="<?php echo esc_url($image['link_url']); ?>"
						<?php if ('_blank' === $image['link_target']) : ?>
						target="_blank"
						rel="noopener noreferrer"
						<?php endif; ?>>
						<img
							src="<?php echo esc_url($image['url']); ?>"
							alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" />
					</a>
				<?php else : ?>
					<img
						src="<?php echo esc_url($image['url']); ?>"
						alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" />
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ($show_arrows && count($slides) > 1) : ?>
		<button
			class="vidal-slider__arrow vidal-slider__arrow--prev"
			aria-label="<?php echo esc_attr__('Slide anterior', 'vidal-slider-block'); ?>">
			<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polyline points="15 18 9 12 15 6"></polyline>
			</svg>
		</button>
		<button
			class="vidal-slider__arrow vidal-slider__arrow--next"
			aria-label="<?php echo esc_attr__('Próximo slide', 'vidal-slider-block'); ?>">
			<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polyline points="9 18 15 12 9 6"></polyline>
			</svg>
		</button>
	<?php endif; ?>

	<?php if ($show_dots && count($slides) > 1) : ?>
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
