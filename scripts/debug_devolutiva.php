<?php
// Simula ambiente
$_SESSION['user_setor'] = 'Qualidade';

$_SESSION['user_role'] = 'admin';

$_SESSION['user_id'] = 1;

$_POST['devolutiva_descricao'] = 'Teste via Script Debug';

// Mock File
$_FILES['devolutiva_foto1'] = ['error' => 4, 'tmp_name' => ''];
$_FILES['devolutiva_foto2'] = ['error' => 4, 'tmp_name' => ''];
$_FILES['devolutiva_foto3'] = ['error' => 4, 'tmp_name' => ''];

require __DIR__ . '/../vendor/autoload.php';

// Load Env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
}
catch (Exception $e) {
    echo "Erro .env: " . $e->getMessage() . "\n";
}

try {
    // Instantiate Controller
    $controller = new \App\Controllers\TonersController();
    echo "Controller Instanciado.\n";

    // Check Methods
    if (!method_exists($controller, 'storeDevolutiva')) {
        die("ERRO: O método storeDevolutiva NÃO existe na classe!\n");
    }

    // 1. Create Dummy Record
    $db = \App\Config\Database::getInstance();
    $sql = "INSERT INTO toners_defeitos (descricao, modelo_toner, cliente_nome, quantidade, registrado_por, numero_pedido, cliente_id, created_at) VALUES ('TESTE DEBUG', 'TESTE', 'TESTE', 1, 1, '12345', 1, NOW())";
    $db->exec($sql);
    $fakeId = $db->lastInsertId();
    echo "Registro Fake Criado ID: $fakeId\n";

    $_POST['defeito_id'] = $fakeId;

    // Call Method
    ob_start();
    $controller->storeDevolutiva();
    $output = ob_get_clean();

    echo "Output JSON:\n" . $output . "\n";

    // Cleanup
    $db->exec("DELETE FROM toners_defeitos WHERE id = $fakeId");
    echo "Registro Fake Excluído.\n";


}
catch (Throwable $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
