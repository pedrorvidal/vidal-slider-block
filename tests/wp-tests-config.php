<?php
/**
 * Configuração usada pela suíte de testes do WordPress (pacote wp-phpunit).
 *
 * Aponta para a instalação real do WordPress deste projeto (a mesma que
 * roda no ddev) e para um banco de dados SEPARADO, usado só pelos testes —
 * a cada execução, o WordPress recria as tabelas desse banco do zero.
 */

// Raiz do WordPress (pasta "web/"), 4 níveis acima deste arquivo:
// tests/ -> vidal-slider-block/ -> plugins/ -> wp-content/ -> web/.
define('ABSPATH', dirname(__DIR__, 4) . '/');

// Mesmas credenciais do ddev, mas em um banco dedicado aos testes
// (veja o README para o comando que cria esse banco).
define('DB_NAME', getenv('WP_TESTS_DB_NAME') ?: 'db_test');
define('DB_USER', getenv('WP_TESTS_DB_USER') ?: 'db');
define('DB_PASSWORD', getenv('WP_TESTS_DB_PASSWORD') ?: 'db');
define('DB_HOST', getenv('WP_TESTS_DB_HOST') ?: 'db');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$table_prefix = 'wptests_';

define('WP_TESTS_DOMAIN', 'plugins-blocos.ddev.site');
define('WP_TESTS_EMAIL', 'admin@example.com');
define('WP_TESTS_TITLE', 'Test Blog');

define('WP_PHP_BINARY', 'php');
