<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/mock_data.php';
require_once __DIR__ . '/helpers.php';

// Tratar a troca de usuário no mock (Global para o módulo mock)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$data = getMockData();
$homologacoes = $data['homologacoes'];
$u = getUsuarioLogado();

// Contadores para os Cards
$totais = [
    'total' => count($homologacoes),
    'aguardando' => count(array_filter($homologacoes, fn($h) => $h['status'] === 'aguardando_chegada')),
    'em_andamento' => count(array_filter($homologacoes, fn($h) => in_array($h['status'], ['item_recebido', 'em_homologacao']))),
    'concluidas' => count(array_filter($homologacoes, fn($h) => $h['status'] === 'concluida')),
    'canceladas' => count(array_filter($homologacoes, fn($h) => $h['status'] === 'cancelada')),
];

// Filtros
$filtroStatus = $_GET['status'] ?? '';
$filtroTipo = $_GET['tipo'] ?? '';

$lista = array_filter($homologacoes, function($h) use ($filtroStatus, $filtroTipo) {
    if ($filtroStatus && $h['status'] !== $filtroStatus) return false;
    if ($filtroTipo && $h['tipo_equipamento'] !== $filtroTipo) return false;
    return true;
});

// Alertas de Notificação
$alertas = [];
foreach ($homologacoes as $h) {
    if ($h['status'] === 'aguardando_chegada' && $h['data_prevista_chegada']) {
        $dias = calcularDiasRestantes($h['data_prevista_chegada']);
        if ($dias !== null && $dias <= $h['dias_antecedencia_notif']) {
            $alertas[] = "Atenção: A homologação <strong>{$h['codigo']}</strong> ({$h['modelo']}) tem chegada prevista para daqui a {$dias} dias!";
        }
    }
}

// Carregar o Layout SGQ
$title = "Homologações 2.0 - Dashboard";
$viewFile = __DIR__ . '/views/index.php';
require_once __DIR__ . '/../views/layouts/main.php';
?>
