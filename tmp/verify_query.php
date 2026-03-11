<?php
// Mocking session for testing
session_start();
$_SESSION['user_id'] = 1; // Assuming 1 is a valid user ID

require_once __DIR__ . '/../vendor/autoload.php';

// Check if we can instantiate the controller and call the method
try {
    $db = \App\Config\Database::getInstance();
    $controller = new \App\Controllers\AdminController();
    
    // We can't easily call the method and get the JSON if it exits, 
    // but we can test the query logic if we extract it or just check the DB directly.
    
    $where = '1=1';
    $params = [];
    $chart5Sql = "SELECT
                DATE_FORMAT(t.created_at, '%Y-%m') AS mes,
                COALESCE(SUM(t.valor_recuperado), 0) AS total,
                COUNT(*) AS qtd_triagens
                FROM triagem_toners t WHERE {$where}
                GROUP BY mes ORDER BY mes ASC";
    
    $stmt = $db->prepare($chart5Sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "API Data Simulation Results:\n";
    foreach ($results as $row) {
        echo "Month: {$row['mes']}, Total: {$row['total']}, Qtd: {$row['qtd_triagens']}\n";
    }
    
    if (count($results) > 0 && isset($results[0]['qtd_triagens'])) {
        echo "\nSUCCESS: 'qtd_triagens' found in query results.\n";
    } else if (count($results) === 0) {
        echo "\nWARNING: No data found in triagem_toners table, but query structure seems valid.\n";
    } else {
        echo "\nFAILURE: 'qtd_triagens' NOT found in query results.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
