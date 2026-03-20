<?php
require_once __DIR__ . '/layout/header.php';

$u = getUsuarioLogado();
if ($u['perfil'] !== 'logistica' && $u['perfil'] !== 'admin' && $u['perfil'] !== 'super_admin') {
    echo "<div class='alert alert-danger'>Acesso restrito ao Setor de Logística.</div>";
    require_once __DIR__ . '/layout/footer.php';
    exit;
}

$data = getMockData();
$homologacoes = $data['homologacoes'];

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_recebimento_id'])) {
    $id = (int)$_POST['confirmar_recebimento_id'];
    atualizarHomologacaoMock($id, [
        'status' => 'item_recebido',
        'data_recebimento' => $_POST['data_recebimento'],
        'recebido_por' => $u['id']
    ]);
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Recebimento confirmado!"];
    header("Location: logistica.php");
    exit;
}

// Filtrar itens apenas Aguardando ou Recém recebidos
$aguardando = array_filter($homologacoes, fn($h) => $h['status'] === 'aguardando_chegada');
$recebidos = array_filter($homologacoes, fn($h) => $h['status'] === 'item_recebido');
?>

<div class="mb-4">
    <h2 class="mb-0">Painel de Recepção Logística</h2>
    <p class="text-muted">Acompanhe as remessas chegando para homologação e acuse o recebimento físico.</p>
</div>

<h4 class="mt-4 mb-3 text-warning"><i class="ph-fill ph-truck me-2"></i>Aguardando Recebimento Fiscal/Físico</h4>

<div class="row g-4">
    <?php if (empty($aguardando)): ?>
        <div class="col-12"><div class="alert alert-light border">Nenhum equipamento aguardando chegada no momento.</div></div>
    <?php endif; ?>
    <?php foreach ($aguardando as $h): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 border-top border-warning border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-warning text-dark"><i class="ph ph-clock"></i> Aguardando Chegada</span>
                    <strong class="text-secondary"><?= $h['codigo'] ?></strong>
                </div>
                <h5 class="card-title fw-bold text-dark mt-3 truncate"><?= $h['titulo'] ?></h5>
                <p class="card-text text-muted mb-4 small">
                    <strong>Fornecedor:</strong> <?= $h['fornecedor'] ?><br>
                    <strong>Previsão de Chegada:</strong> <?= $h['data_prevista_chegada'] ? date('d/m/Y', strtotime($h['data_prevista_chegada'])) : 'Não estipulada' ?>
                </p>
                
                <button type="button" class="btn btn-warning w-100 fw-bold shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#receiveModal<?= $h['id'] ?>">
                    <i class="ph ph-package"></i> Confirmar Chegada
                </button>
            </div>
        </div>
        
        <!-- Modal Recebimento -->
        <div class="modal fade" id="receiveModal<?= $h['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark fw-bold"><i class="ph ph-package"></i> Receber Equipamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Você está confirmando o recebimento de: <strong><?= $h['codigo'] ?> - <?= $h['modelo'] ?></strong></p>
                        <form method="POST">
                            <input type="hidden" name="confirmar_recebimento_id" value="<?= $h['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data Emissão/Recebimento</label>
                                <input type="date" name="data_recebimento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Observações sobre a Carga</label>
                                <textarea name="observacoes" class="form-control" rows="2" placeholder="Ex: Carga em perfeito estado, NF anexada..."></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning text-dark fw-bold">Registrar Entrada</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<h4 class="mt-5 mb-3 text-info"><i class="ph-fill ph-check-circle me-2"></i>Itens no Depósito (Aguardando Retirada da TI)</h4>
<div class="row g-3">
    <?php if (empty($recebidos)): ?>
        <div class="col-12"><div class="alert alert-light border text-muted">A fila está vazia. Todos os itens recebidos já foram retirados pela TI.</div></div>
    <?php endif; ?>
    <?php foreach ($recebidos as $h): ?>
    <div class="col-md-6">
        <div class="card bg-light border-0 shadow-sm border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-dark fw-bold"><?= $h['codigo'] ?> <span class="badge bg-info ms-2">Recebido</span></h6>
                        <small class="text-muted d-block"><?= $h['titulo'] ?></small>
                        <small class="text-secondary mt-2 d-block"><i class="ph ph-user"></i> Acusado por <?= getUserById($h['recebido_por'])['nome'] ?> em <?= date('d/m/Y', strtotime($h['data_recebimento'])) ?></small>
                    </div>
                    <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="btn btn-sm btn-outline-info">Ver Guia</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
