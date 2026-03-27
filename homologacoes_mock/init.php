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

// --- TRATAMENTO GLOBAL DE AÇÕES MOCK ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Troca de Usuário (Simulação)
    if (isset($_POST['trocar_usuario'])) {
        $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // 2. Cancelar ou Excluir Definitivamente
    if (isset($_POST['acao']) && $_POST['acao'] === 'cancelar_homologacao') {
        $id = (int)$_POST['id'];
        $excluir = isset($_POST['excluir_definitivo']) && $_POST['excluir_definitivo'] === '1';
        
        if ($excluir) {
            excluirHomologacaoMock($id);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Homologação excluída permanentemente."];
        } else {
            atualizarHomologacaoMock($id, ['status' => 'cancelada']);
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => "Homologação cancelada."];
        }
        
        // Redireciona para evitar re-submissão
        $redirect = basename($_SERVER['PHP_SELF']);
        header("Location: $redirect");
        exit;
    }
}
