<?php
require_once __DIR__ . '/layout/header.php';

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

// Alertas de Notificação (SLA / Chegada Iminente)
$alertas = [];
foreach ($homologacoes as $h) {
    if ($h['status'] === 'aguardando_chegada' && $h['data_prevista_chegada']) {
        $dias = calcularDiasRestantes($h['data_prevista_chegada']);
        if ($dias !== null && $dias <= $h['dias_antecedencia_notif']) {
            $alertas[] = "Atenção: A homologação <strong>{$h['codigo']}</strong> ({$h['modelo']}) tem chegada prevista para daqui a {$dias} dias!";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard Geral</h2>
    <?php if ($u['perfil'] === 'compras'): ?>
        <a href="nova_homologacao.php" class="btn btn-primary shadow-sm"><i class="ph ph-plus"></i> Nova Homologação</a>
    <?php endif; ?>
</div>

<!-- Alertas -->
<?php foreach ($alertas as $alerta): ?>
    <div class="alert alert-warning shadow-sm border-warning ps-4" role="alert">
        <i class="ph-fill ph-warning-circle text-warning fs-5 me-2" style="vertical-align: text-bottom;"></i> <?= $alerta ?>
    </div>
<?php endforeach; ?>

<!-- Resumo em Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-5ths col-sm-6 col-12">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold">Total</h6>
                <h3 class="mb-0"><?= $totais['total'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-5ths col-sm-6 col-12">
        <div class="card border-0 shadow-sm h-100 bg-white border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold">Aguardando Chegada</h6>
                <h3 class="mb-0 text-warning"><?= $totais['aguardando'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-5ths col-sm-6 col-12">
        <div class="card border-0 shadow-sm h-100 bg-white border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold">Em Andamento</h6>
                <h3 class="mb-0 text-primary"><?= $totais['em_andamento'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-5ths col-sm-6 col-12">
        <div class="card border-0 shadow-sm h-100 bg-white border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold">Concluídas</h6>
                <h3 class="mb-0 text-success"><?= $totais['concluidas'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-5ths col-sm-6 col-12">
        <div class="card border-0 shadow-sm h-100 bg-white border-start border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold">Canceladas</h6>
                <h3 class="mb-0 text-danger"><?= $totais['canceladas'] ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Tabela -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="ph ph-list-dashes me-2"></i>Lista de Homologações</h5>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos os Status</option>
                <option value="aguardando_chegada" <?= $filtroStatus == 'aguardando_chegada' ? 'selected' : '' ?>>Aguardando Chegada</option>
                <option value="item_recebido" <?= $filtroStatus == 'item_recebido' ? 'selected' : '' ?>>Item Recebido</option>
                <option value="em_homologacao" <?= $filtroStatus == 'em_homologacao' ? 'selected' : '' ?>>Em Homologação</option>
                <option value="concluida" <?= $filtroStatus == 'concluida' ? 'selected' : '' ?>>Concluída</option>
                <option value="cancelada" <?= $filtroStatus == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
            <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos os Tipos</option>
                <option value="Impressora" <?= $filtroTipo == 'Impressora' ? 'selected' : '' ?>>Impressora</option>
                <option value="Notebook" <?= $filtroTipo == 'Notebook' ? 'selected' : '' ?>>Notebook</option>
                <option value="Suprimento de Impressora" <?= $filtroTipo == 'Suprimento de Impressora' ? 'selected' : '' ?>>Suprimento</option>
                <option value="Peça de Impressora" <?= $filtroTipo == 'Peça de Impressora' ? 'selected' : '' ?>>Peça</option>
            </select>
            <a href="index.php" class="btn btn-sm btn-light">Limpar</a>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Equipamento/Título</th>
                        <th>Status</th>
                        <th>Prev. Chegada</th>
                        <th>Responsáveis</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Ainda não há homologações cadastradas.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($lista as $h): ?>
                    <tr>
                        <td class="ps-4 fw-medium text-secondary"><?= $h['codigo'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ph <?= getIconForTipo($h['tipo_equipamento']) ?> fs-4 text-secondary"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block"><?= $h['titulo'] ?></strong>
                                    <small class="text-muted"><?= $h['fornecedor'] ?> | <?= $h['modelo'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge <?= getBadgeClass($h['status']) ?> px-2 py-1 rounded-pill"><?= getStatusLabel($h['status']) ?></span></td>
                        <td><?= $h['data_prevista_chegada'] ? date('d/m/Y', strtotime($h['data_prevista_chegada'])) : '-' ?></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($h['responsaveis'] as $resp_id): ?>
                                    <span class="badge bg-secondary opacity-75"><?= getUserById($resp_id)['nome'] ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Visualizar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Utilities for 5 columns */
.col-md-5ths { position: relative; width: 100%; padding-right: calc(var(--bs-gutter-x) * .5); padding-left: calc(var(--bs-gutter-x) * .5); }
@media (min-width: 768px) { .col-md-5ths { flex: 0 0 auto; width: 20%; } }
</style>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
