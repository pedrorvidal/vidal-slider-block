<?php

/**
 * Plugin Name:       Vidal Slider Block
 * Description:       Custom slider block
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Pedro Vidal
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vidal-slider-block
 *
 * @package CreateBlock
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Carrega as traduções (.mo/.json) do plugin a partir de /languages.
 *
 * Diferente de plugins hospedados no WordPress.org (que têm as traduções
 * carregadas automaticamente desde o WP 4.6), um plugin distribuído fora
 * do .org precisa chamar load_plugin_textdomain() explicitamente — sem
 * isso, os textos em português ficam fixos mesmo que existam arquivos de
 * tradução. Ganchado em "init" (mesmo hook do registro do bloco, logo
 * abaixo) para não disparar o aviso "textdomain carregado cedo demais"
 * que o WordPress emite quando uma string é traduzida antes desse hook.
 */
function vidal_slider_block_load_textdomain()
{
	load_plugin_textdomain('vidal-slider-block', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'vidal_slider_block_load_textdomain');

/**
 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
 * based on the registered block metadata. Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_vidal_slider_block_block_init()
{
	wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
}
add_action('init', 'create_block_vidal_slider_block_block_init');
