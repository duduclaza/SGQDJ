<?php
require __DIR__ . '/vendor/autoload.php';
$db = \App\Config\Database::getInstance();
$stmt = $db->query('DESCRIBE toners_defeitos');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
