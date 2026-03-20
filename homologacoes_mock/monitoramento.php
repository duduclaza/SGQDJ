<?php
require_once __DIR__ . '/init.php';

// Tratar a troca de usuário no mock (Global para o módulo mock)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$u = getUsuarioLogado();

$data = getMockData();
$homologacoes = $data['homologacoes'];

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'])) {
    $id = (int)$_POST['cancelar_id'];
    atualizarHomologacaoMock($id, [
        'status' => 'cancelada',
        'parecer_final' => "Cancelado por Compras. Motivo: " . $_POST['motivo_cancelamento']
    ]);
    $_SESSION['flash_message'] = ['type' => 'warning', 'text' => "Homologação cancelada."];
    header("Location: monitoramento.php");
    exit;
}

$title = "Monitorar Entregas - Homologações 2.0";
$viewFile = __DIR__ . '/views/monitoramento.php';
require_once __DIR__ . '/../views/layouts/main.php';
?>
