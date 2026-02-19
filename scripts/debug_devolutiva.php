<?php
// Simula ambiente
$_SESSION['user_setor'] = 'Qualidade'; // Permissão OK
$_SESSION['user_role'] = 'admin'; // Permissão OK
$_SESSION['user_id'] = 1;

$_POST['defeito_id'] = 1; // ID existente (espero)
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
    $ref = new ReflectionClass($controller);
    $methods = [];
    foreach ($ref->getMethods() as $m) {
        $methods[] = $m->name;
    }
    echo "Métodos Encontrados: " . implode(', ', $methods) . "\n";

    if (!method_exists($controller, 'storeDevolutiva')) {
        die("ERRO: O método storeDevolutiva NÃO existe na classe!\n");
    }

    // Call Method
    ob_start();
    $controller->storeDevolutiva();
    $output = ob_get_clean();

    echo "Output JSON:\n" . $output . "\n";


}
catch (Throwable $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
