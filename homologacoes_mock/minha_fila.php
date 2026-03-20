<?php
require_once __DIR__ . '/init.php';

// Tratar troca de usuario no mock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$u = getUsuarioLogado();

$data = getMockData();
$homologacoes = $data['homologacoes'];

// Filtrar as homologações onde o usuário logado consta na array 'responsaveis'
$minha_fila = array_filter($homologacoes, function($h) use ($u) {
    if (!in_array($u['id'], $h['responsaveis'])) return false;
    return in_array($h['status'], ['item_recebido', 'em_homologacao']);
});

$historico = array_filter($homologacoes, function($h) use ($u) {
    return in_array($u['id'], $h['responsaveis']) && in_array($h['status'], ['concluida', 'cancelada']);
});

$title = "Minha Fila de TI - Homologações 2.0";
$viewFile = __DIR__ . '/views/minha_fila.php';
require_once __DIR__ . '/../views/layouts/main.php';
?>
