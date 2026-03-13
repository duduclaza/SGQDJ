<?php
require_once __DIR__ . '/src/Config/Database.php';
try {
    $db = \App\Config\Database::getInstance();
    $db->exec("ALTER TABLE users ADD COLUMN password_plain VARCHAR(255) DEFAULT NULL");
    echo "Success\n";
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
