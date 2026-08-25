<?php
/**
 * Bootstrap do PHPUnit.
 *
 * Carrega a suíte de testes do WordPress (instalada via composer, pacote
 * wp-phpunit/wp-phpunit) e, dentro dela, o nosso próprio plugin — do mesmo
 * jeito que o WordPress carregaria um plugin de verdade.
 */

$_tests_dir = getenv('WP_TESTS_DIR');

if (! $_tests_dir) {
	$_tests_dir = dirname(__DIR__) . '/vendor/wp-phpunit/wp-phpunit';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Carrega o plugin sob teste no momento certo do ciclo de carregamento do
 * WordPress (antes do `init`, que é quando o bloco se registra).
 */
function _manually_load_plugin()
{
	require dirname(__DIR__) . '/vidal-slider-block.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';
