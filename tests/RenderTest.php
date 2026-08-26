<?php

/**
 * Testes do render.php do bloco.
 *
 * A estratégia aqui é usar do_blocks(), a mesma função que o WordPress usa
 * de verdade para transformar o post_content em HTML — ou seja, testamos
 * o bloco do jeito que ele é usado na prática, e não chamando render.php
 * diretamente por fora.
 */
class RenderTest extends WP_UnitTestCase
{
	/**
	 * Monta o comentário de bloco <!-- wp:... /--> com os atributos dados
	 * e devolve o HTML renderizado.
	 */
	private function render_slider(array $attributes): string
	{
		$content = '<!-- wp:create-block/vidal-slider-block ' . wp_json_encode($attributes) . ' /-->';

		return trim(do_blocks($content));
	}

	public function test_renders_nothing_without_images()
	{
		$output = $this->render_slider(['images' => []]);

		$this->assertSame('', $output);
	}

	public function test_ignores_attachment_id_that_does_not_exist()
	{
		$output = $this->render_slider([
			'images' => [
				['id' => 999999, 'url' => 'https://evil.example.com/x.jpg'],
			],
		]);

		$this->assertSame('', $output);
	}

	public function test_uses_real_attachment_url_and_ignores_client_supplied_url()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$url_real = wp_get_attachment_image_url($attachment_id, 'full');

		$output = $this->render_slider([
			'images' => [
				[
					'id'  => $attachment_id,
					'url' => 'https://evil.example.com/tracker.jpg',
					'alt' => 'Foto de teste',
				],
			],
		]);

		$this->assertStringContainsString(esc_url($url_real), $output);
		$this->assertStringNotContainsString('evil.example.com', $output);
		$this->assertStringContainsString('Foto de teste', $output);
	}

	public function test_invalid_layout_falls_back_to_boxed()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-2.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [['id' => $attachment_id]],
			'layout' => 'algo-malicioso',
		]);

		$this->assertStringContainsString('vidal-slider--boxed', $output);
		$this->assertStringNotContainsString('alignfull', $output);
	}

	public function test_full_layout_adds_alignfull_class()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-3.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [['id' => $attachment_id]],
			'layout' => 'full',
		]);

		$this->assertStringContainsString('vidal-slider--full', $output);
		$this->assertStringContainsString('alignfull', $output);
	}

	public function test_dots_only_appear_with_more_than_one_image()
	{
		$id_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-4.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output_uma_imagem = $this->render_slider(['images' => [['id' => $id_1]]]);
		$this->assertStringNotContainsString('vidal-slider__dots', $output_uma_imagem);

		$id_2 = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-5.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output_duas_imagens = $this->render_slider([
			'images' => [['id' => $id_1], ['id' => $id_2]],
		]);
		$this->assertStringContainsString('vidal-slider__dots', $output_duas_imagens);
	}

	public function test_dots_are_hidden_when_show_dots_is_false()
	{
		$id_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-dots-1.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);
		$id_2 = self::factory()->attachment->create_object([
			'file'           => 'imagem-dots-2.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'   => [['id' => $id_1], ['id' => $id_2]],
			'showDots' => false,
		]);

		$this->assertStringNotContainsString('vidal-slider__dots', $output);
	}

	public function test_arrows_appear_with_multiple_images_by_default()
	{
		$id_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-arrows-1.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);
		$id_2 = self::factory()->attachment->create_object([
			'file'           => 'imagem-arrows-2.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [['id' => $id_1], ['id' => $id_2]],
		]);

		$this->assertStringContainsString('vidal-slider__arrow--prev', $output);
		$this->assertStringContainsString('vidal-slider__arrow--next', $output);
	}

	public function test_arrows_are_hidden_when_show_arrows_is_false()
	{
		$id_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-arrows-3.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);
		$id_2 = self::factory()->attachment->create_object([
			'file'           => 'imagem-arrows-4.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'     => [['id' => $id_1], ['id' => $id_2]],
			'showArrows' => false,
		]);

		$this->assertStringNotContainsString('vidal-slider__arrow', $output);
	}

	public function test_arrows_do_not_appear_with_a_single_image_even_when_enabled()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-arrows-5.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'     => [['id' => $attachment_id]],
			'showArrows' => true,
		]);

		$this->assertStringNotContainsString('vidal-slider__arrow', $output);
	}

	public function test_negative_interval_is_sanitized_to_absolute_value()
	{
		$anexo_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-1.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);


		$output_uma_imagem = $this->render_slider(
			[
				'images' => [
					['id' => $anexo_1]
				],
				'interval' => -50
			]
		);
		$this->assertStringContainsString('data-interval="50"', $output_uma_imagem);
	}

	public function test_zero_interval_is_not_replaced_by_default()
	{
		$anexo_1 = self::factory()->attachment->create_object([
			'file'           => 'imagem-teste-6.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output_uma_imagem = $this->render_slider(
			[
				'images' => [
					['id' => $anexo_1]
				],
				'interval' => 0
			]
		);
		$this->assertStringContainsString('data-interval="0"', $output_uma_imagem);
	}

	public function test_slide_with_relative_link_wraps_image_in_anchor()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-1.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => '/contato']],
			],
		]);

		$this->assertStringContainsString('href="/contato"', $output);
	}

	public function test_external_link_adds_rel_noopener_noreferrer()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-2.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				[
					'id'   => $attachment_id,
					'link' => ['url' => 'https://example.com/promo'],
				],
			],
		]);

		$this->assertStringContainsString('href="https://example.com/promo"', $output);
		$this->assertStringContainsString('target="_blank"', $output);
		$this->assertStringContainsString('rel="noopener noreferrer"', $output);
	}

	public function test_internal_relative_link_does_not_add_target_or_rel_attribute()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-3.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => '/pagina']],
			],
		]);

		$this->assertStringNotContainsString('target="_blank"', $output);
		$this->assertStringNotContainsString('rel="noopener', $output);
	}

	public function test_invalid_link_scheme_renders_image_without_anchor()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-4.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => 'javascript:alert(1)']],
			],
		]);

		$this->assertStringNotContainsString('<a ', $output);
	}

	public function test_protocol_relative_link_is_rejected()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-5.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => '//evil.com']],
			],
		]);

		$this->assertStringNotContainsString('<a ', $output);
		$this->assertStringNotContainsString('evil.com', $output);
	}

	public function test_bare_domain_without_scheme_is_rejected()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-6.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => 'example.com']],
			],
		]);

		$this->assertStringNotContainsString('<a ', $output);
	}

	public function test_missing_link_field_renders_image_without_anchor()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-7.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [['id' => $attachment_id]],
		]);

		$this->assertStringNotContainsString('<a ', $output);
	}

	public function test_link_with_empty_url_is_ignored()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-9.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => '']],
			],
		]);

		$this->assertStringNotContainsString('<a ', $output);
	}

	public function test_link_url_with_surrounding_whitespace_is_trimmed()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-10.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => ' /contato ']],
			],
		]);

		$this->assertStringContainsString('href="/contato"', $output);
	}

	public function test_absolute_link_to_external_domain_uses_blank_target()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-11.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [
				[
					'id'   => $attachment_id,
					'link' => ['url' => 'https://external-site.example/page'],
				],
			],
		]);

		$this->assertStringContainsString('target="_blank"', $output);
		$this->assertStringContainsString('rel="noopener noreferrer"', $output);
	}

	public function test_absolute_link_to_same_domain_does_not_use_blank_target()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-link-12.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$same_domain_url = home_url('/pagina-interna');

		$output = $this->render_slider([
			'images' => [
				['id' => $attachment_id, 'link' => ['url' => $same_domain_url]],
			],
		]);

		$this->assertStringContainsString('href="' . esc_url($same_domain_url) . '"', $output);
		$this->assertStringNotContainsString('target="_blank"', $output);
	}

	public function test_default_height_variables_use_500px_desktop_and_250px_mobile()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-altura-1.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images' => [['id' => $attachment_id]],
		]);

		$this->assertStringContainsString('--vidal-slider-height:500px', $output);
		$this->assertStringContainsString('--vidal-slider-height-mobile:250px', $output);
	}

	public function test_custom_height_and_unit_are_applied()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-altura-2.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'            => [['id' => $attachment_id]],
			'heightDesktop'     => 80,
			'heightUnitDesktop' => 'vh',
			'heightMobile'      => 30,
			'heightUnitMobile'  => 'em',
		]);

		$this->assertStringContainsString('--vidal-slider-height:80vh', $output);
		$this->assertStringContainsString('--vidal-slider-height-mobile:30em', $output);
	}

	public function test_invalid_height_unit_falls_back_to_px()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-altura-3.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'            => [['id' => $attachment_id]],
			'heightUnitDesktop' => 'algo-malicioso',
		]);

		$this->assertStringContainsString('--vidal-slider-height:500px', $output);
	}

	public function test_negative_height_is_clamped_to_zero()
	{
		$attachment_id = self::factory()->attachment->create_object([
			'file'           => 'imagem-altura-4.jpg',
			'post_parent'    => 0,
			'post_mime_type' => 'image/jpeg',
		]);

		$output = $this->render_slider([
			'images'        => [['id' => $attachment_id]],
			'heightDesktop' => -50,
		]);

		$this->assertStringContainsString('--vidal-slider-height:0px', $output);
	}
}
