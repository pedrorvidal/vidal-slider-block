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
}
