<?php
require_once __DIR__ . '/init.php';

// Tratar a troca de usuário no mock (Global para o módulo mock)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$u = getUsuarioLogado();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_homologacao'])) {
    $responsaveis = isset($_POST['responsaveis']) ? $_POST['responsaveis'] : [];
    
    $novoRegistro = [
        'titulo' => $_POST['titulo'],
        'tipo_equipamento' => $_POST['tipo_equipamento'],
        'descricao' => $_POST['descricao'],
        'fornecedor' => $_POST['fornecedor'],
        'modelo' => $_POST['modelo'],
        'numero_serie' => $_POST['numero_serie'],
        'responsaveis' => array_map('intval', $responsaveis),
        'data_prevista_chegada' => $_POST['data_prevista_chegada'] ?: null,
        'dias_antecedencia_notif' => (int)$_POST['dias_antecedencia_notif'],
    ];
    
    $id = criarHomologacaoMock($novoRegistro);
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Homologação criada com sucesso! (ID: $id)"];
    header("Location: detalhe_homologacao.php?id=$id");
    exit;
}

$data = getMockData();
$responsaveis = array_filter($data['usuarios'], fn($u) => $u['perfil'] === 'responsavel');

$title = "Nova Homologação - Homologações 2.0";
$viewFile = __DIR__ . '/views/nova_homologacao.php';
require_once __DIR__ . '/../views/layouts/main.php';
?>
