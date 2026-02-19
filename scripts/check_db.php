<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
}
catch (Exception $e) {
// Ignore if already loaded or not found in some envs
}

try {
    $db = Database::getInstance();

    // Check columns
    $stmt = $db->query("DESCRIBE toners_defeitos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== Estrutura da Tabela toners_defeitos ===\n";
    foreach ($columns as $col) {
        echo str_pad($col['Field'], 25) . " | " . $col['Type'] . "\n";
    }

// Check Session (Simulated)
// we can't check session here easily as it's CLI.


}
catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
