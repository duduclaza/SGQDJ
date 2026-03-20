<?php
// Garantir que a sessão foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carregar o Autoloader do Composer para que classes como PermissionService funcionem
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis de ambiente (necessário para conexão de banco que o PermissionService pode usar)
if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    try {
        $dotenv->load();
    } catch (Exception $e) {
        // ignora se não conseguir
    }
}

require_once __DIR__ . '/mock_data.php';
require_once __DIR__ . '/helpers.php';
