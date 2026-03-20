<?php
require_once __DIR__ . '/../mock_data.php';
require_once __DIR__ . '/../helpers.php';

// Tratar a troca de usuário no mock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$u = getUsuarioLogado();
$perfisSidebar = [
    'compras' => [
        ['url' => 'index.php', 'label' => 'Dashboard', 'icon' => 'ph-chart-pie-slice'],
        ['url' => 'nova_homologacao.php', 'label' => 'Nova Homologação', 'icon' => 'ph-plus-circle'],
        ['url' => 'monitoramento.php', 'label' => 'Monitoramento', 'icon' => 'ph-magnifying-glass'],
    ],
    'logistica' => [
        ['url' => 'index.php', 'label' => 'Dashboard Geral', 'icon' => 'ph-chart-pie-slice'],
        ['url' => 'logistica.php', 'label' => 'Painel Logística', 'icon' => 'ph-truck'],
    ],
    'responsavel' => [
        ['url' => 'index.php', 'label' => 'Dashboard Geral', 'icon' => 'ph-chart-pie-slice'],
        ['url' => 'minha_fila.php', 'label' => 'Minhas Homologações', 'icon' => 'ph-list-checks'],
    ]
];

$linksAtuais = $perfisSidebar[$u['perfil']] ?? [];
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homologações 2.0</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; display: flex; min-height: 100vh; }
        .sidebar { min-width: 250px; max-width: 250px; background-color: #1e293b; color: #fff; display: flex; flex-direction: column; }
        .sidebar a { color: #cbd5e1; text-decoration: none; padding: 12px 20px; display: block; transition: 0.2s; border-radius: 6px; margin: 4px 10px; }
        .sidebar a:hover, .sidebar a.active { background-color: #334155; color: #fff; }
        .sidebar a i { margin-right: 10px; font-size: 1.2rem; vertical-align: text-bottom; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; background-color: #f1f5f9; }
        .user-selector { background: #0f172a; padding: 15px; border-bottom: 1px solid #334155; }
        .timeline { position: relative; padding-left: 30px; margin-top: 20px; }
        .timeline::before { content: ''; position: absolute; left: 11px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
        .timeline-item { position: relative; margin-bottom: 25px; }
        .timeline-item::before { content: ''; position: absolute; left: -26px; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: #3b82f6; border: 3px solid #fff; box-shadow: 0 0 0 2px #3b82f6; }
        .timeline-item.pending::before { background: #fff; border: 3px solid #cbd5e1; box-shadow: none; }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="user-selector">
        <span class="d-block mb-2 text-sm text-uppercase fw-bold text-secondary text-xs">Simular Logado Como:</span>
        <form method="POST" action="">
            <select name="usuario_logado_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($_SESSION['mock_usuarios'] as $mockUser): ?>
                    <option value="<?= $mockUser['id'] ?>" <?= $mockUser['id'] == $u['id'] ? 'selected' : '' ?>>
                        <?= $mockUser['nome'] ?> [<?= ucfirst($mockUser['perfil']) ?>]
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="trocar_usuario" value="1">
        </form>
    </div>
    
    <div class="p-3">
        <h5 class="text-white mb-4 d-flex align-items-center">
            <i class="ph-fill ph-check-square-offset me-2"></i> Homologações 2.0
        </h5>
        
        <p class="text-secondary small text-uppercase fw-bold ps-2 mb-2">Menu Principal</p>
        <?php foreach ($linksAtuais as $link): ?>
            <a href="<?= $link['url'] ?>" class="<?= $currentPage === $link['url'] ? 'active' : '' ?>">
                <i class="ph <?= $link['icon'] ?>"></i> <?= $link['label'] ?>
            </a>
        <?php endforeach; ?>
        
        <hr class="border-secondary my-4">
        <a href="/inicio" class="text-danger">
            <i class="ph ph-sign-out"></i> Voltar ao SGQ
        </a>
    </div>
</nav>

<main class="main-content">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= $_SESSION['flash_message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
