<?php
require_once __DIR__ . '/layout/header.php';

$u = getUsuarioLogado();

// Bloqueio visual para outros perfis, mas permitindo acesso caso queiram testar
if ($u['perfil'] !== 'compras') {
    echo "<div class='alert alert-danger'>Acesso restrito. Apenas o setor de Compras pode abrir homologações.</div>";
    require_once __DIR__ . '/layout/footer.php';
    exit;
}

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
?>

<div class="mb-4">
    <h2>Nova Homologação de TI</h2>
    <p class="text-muted">Preencha os dados do equipamento para iniciar o processo.</p>
</div>

<div class="card border-0 shadow-sm custom-card">
    <div class="card-body p-4">
        <form method="POST" action="">
            <input type="hidden" name="criar_homologacao" value="1">
            
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Título da Homologação</label>
                    <input type="text" name="titulo" class="form-control" required placeholder="Ex: Avaliação Novo Lote Monitores Dell">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo do Equipamento</label>
                    <select name="tipo_equipamento" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Impressora">Impressora</option>
                        <option value="Notebook">Notebook</option>
                        <option value="Suprimento de Impressora">Suprimento de Impressora</option>
                        <option value="Peça de Impressora">Peça de Impressora</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição do Contexto</label>
                    <textarea name="descricao" class="form-control" rows="3" required placeholder="Descreva por que este item está sendo homologado..."></textarea>
                </div>
                
                <h5 class="mt-5 mb-2 border-bottom pb-2 text-secondary"><i class="ph ph-cpu me-2"></i>Detalhes Técnicos do Equipamento</h5>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fornecedor</label>
                    <input type="text" name="fornecedor" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Modelo</label>
                    <input type="text" name="modelo" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de Série / Lote</label>
                    <input type="text" name="numero_serie" class="form-control" placeholder="Opcional se ainda não tiver">
                </div>
                
                <h5 class="mt-5 mb-2 border-bottom pb-2 text-secondary"><i class="ph ph-truck me-2"></i>Logística e SLA</h5>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data Prevista de Chegada</label>
                    <input type="date" name="data_prevista_chegada" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Alerta de Antecedência (Dias)</label>
                    <input type="number" name="dias_antecedencia_notif" class="form-control" value="3" min="1" max="15" required>
                    <div class="form-text">Quantos dias antes avisar a Logística/TI?</div>
                </div>
                
                <h5 class="mt-5 mb-2 border-bottom pb-2 text-secondary"><i class="ph ph-users me-2"></i>Responsáveis pela Homologação</h5>
                <div class="col-12">
                    <p class="text-muted small">Selecione os analistas de TI responsáveis por executar os testes.</p>
                    <div class="d-flex flex-wrap gap-4">
                        <?php foreach ($responsaveis as $resp): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="responsaveis[]" value="<?= $resp['id'] ?>" id="resp_<?= $resp['id'] ?>">
                            <label class="form-check-label" for="resp_<?= $resp['id'] ?>">
                                <?= $resp['nome'] ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-12 mt-5">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-medium shadow-sm"><i class="ph ph-check-circle me-1"></i> Abrir Processo de Homologação</button>
                    <a href="index.php" class="btn btn-light ms-2">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
