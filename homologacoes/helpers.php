<?php
require_once __DIR__ . '/mock_data.php';

// Busca Usuário pelo ID
function getUserById($id) {
    $usuarios = $_SESSION['mock_usuarios'];
    foreach ($usuarios as $u) {
        if ($u['id'] == $id) return $u;
    }
    return null;
}

// Busca Homologação pelo ID
function getHomologacaoById($id) {
    $homologacoes = $_SESSION['mock_homologacoes'];
    foreach ($homologacoes as $h) {
        if ($h['id'] == $id) return $h;
    }
    return null;
}

function getUsuarioLogado() {
    return getUserById($_SESSION['usuario_logado_id']);
}

// Labels formatadas para status
function getStatusLabel($status) {
    $labels = [
        'aguardando_chegada' => 'Aguardando Chegada',
        'item_recebido'      => 'Item Recebido',
        'em_homologacao'     => 'Em Homologação',
        'concluida'          => 'Concluída',
        'cancelada'          => 'Cancelada'
    ];
    return $labels[$status] ?? 'Desconhecido';
}

// Cor/Badge para Status
function getBadgeClass($status) {
    $classes = [
        'aguardando_chegada' => 'text-bg-warning',
        'item_recebido'      => 'text-bg-info text-white',
        'em_homologacao'     => 'text-bg-primary',
        'concluida'          => 'text-bg-success',
        'cancelada'          => 'text-bg-danger'
    ];
    return $classes[$status] ?? 'text-bg-secondary';
}

// Dias restantes
function calcularDiasRestantes($data_prevista) {
    if (!$data_prevista) return null;
    $hoje = new DateTime(date('Y-m-d'));
    $prevista = new DateTime($data_prevista);
    $intervalo = $hoje->diff($prevista);
    return (int)$intervalo->format('%R%a'); // retorna negativo se atrasado
}

// Obter Ícone Baseado no Tipo
function getIconForTipo($tipo) {
    switch ($tipo) {
        case 'Impressora': return 'ph-printer';
        case 'Notebook': return 'ph-laptop';
        case 'Suprimento de Impressora': return 'ph-drop';
        case 'Peça de Impressora': return 'ph-gear';
        default: return 'ph-box';
    }
}
?>
