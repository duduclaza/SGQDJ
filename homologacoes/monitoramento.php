<?php
require_once __DIR__ . '/layout/header.php';

$u = getUsuarioLogado();
if ($u['perfil'] !== 'compras' && $u['perfil'] !== 'admin' && $u['perfil'] !== 'super_admin') {
    echo "<div class='alert alert-danger'>Acesso restrito ao Setor de Compras.</div>";
    require_once __DIR__ . '/layout/footer.php';
    exit;
}

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
?>

<div class="mb-4">
    <h2 class="mb-0">Painel de Monitoramento (Compras)</h2>
    <p class="text-muted">Visão gerencial de todas as homologações, andamento e SLA da TI.</p>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="ph ph-list me-2"></i>Controle Geral</h6>
        <button class="btn btn-sm btn-outline-secondary" onclick="alert('Funcionalidade de exportação em desenvolvimento (Mock).')"><i class="ph ph-file-csv"></i> Exportar CSV</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Código</th>
                        <th>Equipamento</th>
                        <th>Fornecedor</th>
                        <th>Status</th>
                        <th>Fase Atual</th>
                        <th>Prazo/SLA</th>
                        <th class="text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($homologacoes as $h): ?>
                    <tr>
                        <td class="ps-3 fw-medium text-secondary"><?= $h['codigo'] ?></td>
                        <td>
                            <strong><?= $h['tipo_equipamento'] ?></strong><br>
                            <small class="text-muted"><?= $h['modelo'] ?></small>
                        </td>
                        <td><?= $h['fornecedor'] ?></td>
                        <td><span class="badge <?= getBadgeClass($h['status']) ?>"><?= getStatusLabel($h['status']) ?></span></td>
                        <td>
                            <?php if ($h['status'] === 'aguardando_chegada'): ?>
                                Aguardando Logística
                            <?php elseif ($h['status'] === 'item_recebido'): ?>
                                Pendente Início TI
                            <?php elseif ($h['status'] === 'em_homologacao'): ?>
                                TI Executando Testes
                            <?php else: ?>
                                Finalizado
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                if ($h['status'] === 'aguardando_chegada' && $h['data_prevista_chegada']) {
                                    $dias = calcularDiasRestantes($h['data_prevista_chegada']);
                                    if ($dias < 0) echo "<span class='text-danger fw-bold'><i class='ph-fill ph-warning'></i> Atrasado " . abs($dias) . " dias</span>";
                                    elseif ($dias <= $h['dias_antecedencia_notif']) echo "<span class='text-warning fw-bold'>Chega em $dias dia(s)</span>";
                                    else echo "<span class='text-muted'>$dias dias para chegar</span>";
                                } elseif ($h['status'] === 'em_homologacao' && $h['data_inicio_homologacao']) {
                                    $dias_homol = calcularDiasRestantes($h['data_inicio_homologacao']);
                                    echo "<span class='text-primary'>Em teste há " . abs($dias_homol) . " dias</span>";
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                        <td class="text-end pe-3">
                            <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="btn btn-sm btn-outline-primary" title="Abrir Detalhe"><i class="ph ph-arrows-out-simple"></i></a>
                            <?php if ($h['status'] !== 'concluida' && $h['status'] !== 'cancelada'): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Cancelar processo" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $h['id'] ?>">
                                    <i class="ph ph-x"></i>
                                </button>

                                <div class="modal fade" id="cancelModal<?= $h['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog text-start">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Cancelamento</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Deseja realmente cancelar a homologação <strong><?= $h['codigo'] ?></strong>?</p>
                                                <form method="POST">
                                                    <input type="hidden" name="cancelar_id" value="<?= $h['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Motivo do Cancelamento</label>
                                                        <textarea name="motivo_cancelamento" class="form-control" rows="2" required></textarea>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-danger">Cancelar Homologação</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
