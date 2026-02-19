<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
}
catch (Exception $e) {
    die("Erro ao carregar .env: " . $e->getMessage());
}

try {
    $db = Database::getInstance();

    echo "Iniciando migração...\n";

    // Ler SQL
    $sql = file_get_contents(__DIR__ . '/../database/toners_defeitos_add_devolutiva.sql');

    // Executar
    $db->exec($sql);

    echo "Migração executada com sucesso!\n";


}
catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
