<?php
// migrate.php
try {
    // Tenta conectar diretamente para evitar problemas de $_ENV no CLI
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=sgqpro;charset=utf8mb4";
    $db = new PDO($dsn, "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Verifica se a coluna já existe antes de adicionar
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'password_plain'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE users ADD COLUMN password_plain VARCHAR(255) DEFAULT NULL");
        echo "Coluna password_plain adicionada com sucesso.\n";
    } else {
        echo "A coluna password_plain já existe.\n";
    }
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
