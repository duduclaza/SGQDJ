<?php
require_once __DIR__ . '/layout/header.php';

$u = getUsuarioLogado();
if ($u['perfil'] !== 'responsavel' && $u['perfil'] !== 'admin' && $u['perfil'] !== 'super_admin') {
    echo "<div class='alert alert-danger'>Acesso restrito ao Setor Técnico/TI.</div>";
    require_once __DIR__ . '/layout/footer.php';
    exit;
}

$data = getMockData();
$homologacoes = $data['homologacoes'];

// Filtrar as homologações onde o usuário logado consta na array 'responsaveis'
$minha_fila = array_filter($homologacoes, function($h) use ($u) {
    if (!in_array($u['id'], $h['responsaveis'])) return false;
    // Mostrar as ativas dele (item recebido aguardando início, em_homologacao)
    return in_array($h['status'], ['item_recebido', 'em_homologacao']);
});
?>

<div class="mb-4">
    <h2 class="mb-0">Painel do Responsável Técnico</h2>
    <p class="text-muted">Acompanhe suas tarefas de teste e certificação técnica de hardware.</p>
</div>

<div class="row g-4 mt-2">
    <?php if (empty($minha_fila)): ?>
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center border-success p-4">
                <i class="ph-fill ph-check-circle fs-1 me-4 text-success"></i>
                <div>
                    <h4 class="alert-heading fw-bold mb-1">Fila Vazia!</h4>
                    <p class="mb-0">Você não possui homologações pendentes no momento. Ótimo trabalho!</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php foreach ($minha_fila as $h): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 bg-white <?= $h['status'] === 'em_homologacao' ? 'border-primary border-4 border-top' : 'border-info border-4 border-top' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge <?= getBadgeClass($h['status']) ?>"><?= getStatusLabel($h['status']) ?></span>
                    <strong class="text-secondary"><?= $h['codigo'] ?></strong>
                </div>
                
                <h5 class="card-title fw-bold text-dark mt-2 mb-1"><?= $h['titulo'] ?></h5>
                <small class="d-block text-muted mb-4"><i class="ph <?= getIconForTipo($h['tipo_equipamento']) ?>"></i> <?= $h['tipo_equipamento'] ?> | <?= $h['modelo'] ?></small>
                
                <?php if ($h['status'] === 'em_homologacao'): ?>
                    <?php 
                        $total_items = count($data['checklists'][$h['tipo_equipamento']] ?? []);
                        $respondidos = count(array_filter($h['checklist_respostas'] ?? [], fn($r) => $r !== null));
                        $perc = $total_items > 0 ? round(($respondidos / $total_items) * 100) : 0;
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Progresso do Checklist</span>
                            <span><?= $respondidos ?>/<?= $total_items ?> (<?= $perc ?>%)</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $perc ?>%;" aria-valuenow="<?= $perc ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="btn btn-primary w-100 shadow-sm fw-medium"><i class="ph ph-list-checks"></i> Continuar Testes</a>
                <?php else: ?>
                    <div class="alert alert-light border small text-center mb-4 text-muted">
                        <i class="ph ph-package d-block fs-3 mb-2 text-info"></i>
                        O item já chegou no estoque. Dirija-se à logística para retirar o equipamento e iniciar a esteira.
                    </div>
                    <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="btn btn-info w-100 shadow-sm text-white fw-medium"><i class="ph ph-play-circle"></i> Iniciar Homologação</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<h4 class="mt-5 mb-3 text-secondary border-bottom pb-2">Histórico de Conclusões Recentes</h4>
<?php 
// Opcional: Mostrar os últimos que ele ajudou
$historico = array_filter($homologacoes, function($h) use ($u) {
    return in_array($u['id'], $h['responsaveis']) && $h['status'] === 'concluida';
});
?>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-4">Código</th>
                <th>Equipamento</th>
                <th>Data Fim</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historico)): ?>
                <tr><td colspan="4" class="text-center py-3 text-muted">Nenhum histórico encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($historico as $hist): ?>
            <tr>
                <td class="ps-4 fw-medium"><?= $hist['codigo'] ?></td>
                <td><?= $hist['titulo'] ?></td>
                <td><?= date('d/m/Y', strtotime($hist['data_fim_homologacao'])) ?></td>
                <td>
                    <span class="badge <?= $hist['resultado'] === 'aprovado' ? 'bg-success' : 'bg-danger' ?>"><?= strtoupper($hist['resultado']) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
