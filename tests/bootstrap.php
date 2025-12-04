<?php
/**
 * Bootstrap para testes PHPUnit
 * Este arquivo é executado antes de todos os testes
 */

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente de teste
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Define constantes de teste
define('TESTING', true);
define('TEST_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(__DIR__));

// Desabilita output de erros durante testes
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Funções helper para testes
if (!function_exists('test_asset')) {
    function test_asset($path) {
        return TEST_ROOT . '/assets/' . ltrim($path, '/');
    }
}

echo "PHPUnit Bootstrap: Environment configured for testing\n";
