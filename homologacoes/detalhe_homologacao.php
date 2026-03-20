<?php
require_once __DIR__ . '/layout/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$h = getHomologacaoById($id);

if (!$h) {
    echo "<div class='alert alert-danger'>Homologação não encontrada.</div>";
    require_once __DIR__ . '/layout/footer.php';
    exit;
}

$u = getUsuarioLogado();
$data = getMockData();

// ===== AÇÕES DO FLUXO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];
        
        if ($acao === 'confirmar_recebimento' && $u['perfil'] === 'logistica') {
            atualizarHomologacaoMock($id, [
                'status' => 'item_recebido',
                'data_recebimento' => $_POST['data_recebimento'],
                'recebido_por' => $u['id']
            ]);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Item recebido registrado com sucesso!'];
        }
        elseif ($acao === 'iniciar_homologacao' && $u['perfil'] === 'responsavel') {
            atualizarHomologacaoMock($id, [
                'status' => 'em_homologacao',
                'local_homologacao' => $_POST['local_homologacao'],
                'data_inicio_homologacao' => $_POST['data_inicio_homologacao'],
                'nome_cliente' => $_POST['nome_cliente'] ?? null,
                'data_instalacao_cliente' => $_POST['data_instalacao_cliente'] ?? null
            ]);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Homologação iniciada. Preencha o checklist!'];
        }
        elseif ($acao === 'salvar_checklist' && $u['perfil'] === 'responsavel') {
            $respostas = $_POST['checklist'] ?? [];
            $booleadas = [];
            foreach ($respostas as $k => $v) {
                if ($v === '1') $booleadas[$k] = true;
                elseif ($v === '0') $booleadas[$k] = false;
                else $booleadas[$k] = null;
            }
            atualizarHomologacaoMock($id, [
                'checklist_respostas' => $booleadas,
                'observacoes_checklist' => $_POST['observacoes_checklist']
            ]);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Progresso salvo!'];
        }
        elseif ($acao === 'finalizar_homologacao' && $u['perfil'] === 'responsavel') {
            atualizarHomologacaoMock($id, [
                'status' => 'concluida',
                'data_fim_homologacao' => $_POST['data_fim_homologacao'],
                'resultado' => $_POST['resultado'],
                'parecer_final' => $_POST['parecer_final']
            ]);
            // Save checklist one last time
            $respostas = $_POST['checklist'] ?? [];
            $booleadas = [];
            foreach ($respostas as $k => $v) {
                if ($v === '1') $booleadas[$k] = true;
                elseif ($v === '0') $booleadas[$k] = false;
                else $booleadas[$k] = null;
            }
            atualizarHomologacaoMock($id, ['checklist_respostas' => $booleadas]);
            
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Processo de Homologação Finalizado!'];
        }
        
        // Refresh page to load updated mock data
        header("Location: detalhe_homologacao.php?id=$id");
        exit;
    }
}

// Recarregar os dados mockados
$h = getHomologacaoById($id);
$checklistItems = $data['checklists'][$h['tipo_equipamento']] ?? [];
$respostas = $h['checklist_respostas'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= $h['codigo'] ?></h2>
        <h5 class="text-secondary"><?= $h['titulo'] ?></h5>
    </div>
    <div>
        <span class="badge <?= getBadgeClass($h['status']) ?> px-3 py-2 fs-6 rounded-pill">
            <i class="ph ph-flag me-1"></i> Status: <?= getStatusLabel($h['status']) ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna Esquerda: Informações Gerais e Timeline -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h6 class="mb-0 fw-bold"><i class="ph ph-info me-2 text-primary"></i>Informações Gerais</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted" width="120">Tipo</td><td><strong class="text-dark"><?= $h['tipo_equipamento'] ?></strong></td></tr>
                    <tr><td class="text-muted">Fornecedor</td><td><?= $h['fornecedor'] ?></td></tr>
                    <tr><td class="text-muted">Modelo</td><td><?= $h['modelo'] ?></td></tr>
                    <tr><td class="text-muted">N/S Lote</td><td><?= $h['numero_serie'] ?: '<span class="text-muted fst-italic">Não informado</span>' ?></td></tr>
                    <tr><td class="text-muted">Descrição</td><td><small><?= $h['descricao'] ?></small></td></tr>
                </table>
                <hr class="border-light opacity-50">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted" width="120">Criado por</td><td><?= getUserById($h['criado_por'])['nome'] ?> em <?= date('d/m/Y', strtotime($h['data_criacao'])) ?></td></tr>
                    <tr><td class="text-muted">Previsão</td><td><?= $h['data_prevista_chegada'] ? date('d/m/Y', strtotime($h['data_prevista_chegada'])) : '-' ?></td></tr>
                    <tr><td class="text-muted align-middle">Responsáveis</td>
                        <td>
                            <?php foreach ($h['responsaveis'] as $resp_id): ?>
                                <span class="badge bg-light text-dark border shadow-sm"><?= getUserById($resp_id)['nome'] ?></span>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h6 class="mb-0 fw-bold"><i class="ph ph-git-commit me-2 text-primary"></i>Linha do Tempo</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- PASSO 1: CRIAÇÃO -->
                    <div class="timeline-item">
                        <strong class="text-dark d-block">Homologação Criada</strong>
                        <small class="text-muted">Aberta por <?= getUserById($h['criado_por'])['nome'] ?> (Compras) em <?= date('d/m/Y', strtotime($h['data_criacao'])) ?></small>
                    </div>
                    
                    <!-- PASSO 2: RECEBIMENTO -->
                    <?php if ($h['data_recebimento']): ?>
                        <div class="timeline-item">
                            <strong class="text-dark d-block">Item Recebido na Logística</strong>
                            <small class="text-muted">Confirmado por <?= getUserById($h['recebido_por'])['nome'] ?> em <?= date('d/m/Y', strtotime($h['data_recebimento'])) ?></small>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item pending">
                            <strong class="text-secondary d-block">Aguardando Recebimento</strong>
                            <small class="text-muted">Logística precisa dar o aceite do item.</small>
                        </div>
                    <?php endif; ?>
                    
                    <!-- PASSO 3: INÍCIO DA HOMOLOGAÇÃO -->
                    <?php if ($h['data_inicio_homologacao']): ?>
                        <div class="timeline-item">
                            <strong class="text-dark d-block">Homologação Iniciada</strong>
                            <small class="text-muted">Local: <?= ucfirst($h['local_homologacao']) ?>. Iniciada em <?= date('d/m/Y', strtotime($h['data_inicio_homologacao'])) ?></small>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item pending">
                            <strong class="text-secondary d-block">Aguardando Início</strong>
                            <small class="text-muted">Responsável técnico precisa coletar e iniciar testes.</small>
                        </div>
                    <?php endif; ?>
                    
                    <!-- PASSO 4: CONCLUSÃO -->
                    <?php if ($h['data_fim_homologacao']): ?>
                        <div class="timeline-item">
                            <strong class="text-dark d-block">Processo Finalizado</strong>
                            <small class="text-muted mb-1 d-block">Encerrado em <?= date('d/m/Y', strtotime($h['data_fim_homologacao'])) ?></small>
                            <span class="badge text-uppercase <?= $h['resultado'] === 'aprovado' ? 'bg-success' : ($h['resultado'] === 'reprovado' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                Resultado: <?= $h['resultado'] ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item pending">
                            <strong class="text-secondary d-block">Conclusão Pendente</strong>
                            <small class="text-muted">Processo ainda em andamento.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Painel de Ações e Formulários -->
    <div class="col-md-7">
        
        <?php if ($h['status'] === 'aguardando_chegada'): ?>
            <!-- AÇÃO: LOGÍSTICA RECEBE -->
            <?php if ($u['perfil'] === 'logistica'): ?>
                <div class="card border-0 shadow bg-white border-top border-info border-4">
                    <div class="card-body p-4">
                        <h5 class="text-info-emphasis mb-3"><i class="ph-fill ph-check-circle me-2"></i>Ação Requerida: Confirmar Recebimento</h5>
                        <p class="text-muted">Confirme que o equipamento chegou ao setor de logística para os técnicos de TI iniciarem as validações.</p>
                        
                        <form method="POST">
                            <input type="hidden" name="acao" value="confirmar_recebimento">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Data de Recebimento</label>
                                    <input type="date" name="data_recebimento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Observações da Entrega (Opcional)</label>
                                    <input type="text" name="observacoes_entrega" class="form-control" placeholder="Caixa chegou intacta? Faltou algo?">
                                </div>
                                <div class="col-12 mt-4">
                                    <button class="btn btn-info text-white px-4 fw-medium shadow-sm">Confirmar Recebimento Físico</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center border p-4 text-muted">
                    <i class="ph ph-clock fs-1 d-block mb-3 text-warning"></i>
                    Processo aguardando o setor de LOGÍSTICA registrar a chegada do equipamento físico.<br>
                    (Simule o login como perfil "Logística" para avançar o fluxo).
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($h['status'] === 'item_recebido'): ?>
            <!-- AÇÃO: TI INICIA HOMOLOGAÇÃO -->
            <?php if ($u['perfil'] === 'responsavel' && in_array($u['id'], $h['responsaveis'])): ?>
                <div class="card border-0 shadow bg-white border-top border-primary border-4">
                    <div class="card-body p-4">
                        <h5 class="text-primary mb-3"><i class="ph-fill ph-play-circle me-2"></i>Iniciar Testes de Homologação</h5>
                        <p class="text-muted">Registre onde e quando esta homologação está sendo iniciada para liberar o checklist técnico.</p>
                        
                        <form method="POST">
                            <input type="hidden" name="acao" value="iniciar_homologacao">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Data de Início</label>
                                    <input type="date" name="data_inicio_homologacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Local de Teste</label>
                                    <select name="local_homologacao" class="form-select" required onchange="document.getElementById('div_cliente').style.display = this.value === 'cliente' ? 'flex' : 'none'">
                                        <option value="">Selecione...</option>
                                        <option value="laboratorio">No Laboratório de TI</option>
                                        <option value="cliente">Em um Cliente (Produção)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-1" id="div_cliente" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label">Nome do Cliente/Setor</label>
                                    <input type="text" name="nome_cliente" class="form-control" placeholder="Ex: Clínica Alpha">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data de Instalação</label>
                                    <input type="date" name="data_instalacao_cliente" class="form-control">
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button class="btn btn-primary px-4 fw-medium shadow-sm">Iniciar Execução e Abrir Checklist</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center border p-4 text-muted">
                    <i class="ph ph-users fs-1 d-block mb-3 text-info"></i>
                    O item já chegou. Aguardando a equipe técnica retirar e iniciar o processo de homologação.<br>
                    (Simule o login como um "Responsável" para avançar).
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($h['status'] === 'em_homologacao' || $h['status'] === 'concluida'): ?>
            <!-- EXIBIR CHECKLIST (Editável se em_homologacao, Readonly se concluida) -->
            <?php 
                $canEdit = ($h['status'] === 'em_homologacao' && $u['perfil'] === 'responsavel' && in_array($u['id'], $h['responsaveis'])); 
            ?>
            <div class="card border-0 shadow bg-white border-top border-dark border-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-list-checks me-2"></i>Checklist Técnico de <?= $h['tipo_equipamento'] ?></h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <?php if ($canEdit): ?>
                            <input type="hidden" name="acao" value="salvar_checklist">
                        <?php endif; ?>
                        
                        <div class="checklist-container mb-4">
                            <?php foreach ($checklistItems as $key => $label): ?>
                                <?php 
                                    $val = $respostas[$key] ?? null;
                                    $isOk = $val === true;
                                    $isFail = $val === false;
                                ?>
                                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                                    <span class="text-dark"><?= $label ?></span>
                                    <?php if ($canEdit): ?>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="checklist[<?= $key ?>]" id="<?= $key ?>_none" value="" <?= $val === null ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-secondary" for="<?= $key ?>_none"><i class="ph ph-minus"></i></label>

                                        <input type="radio" class="btn-check" name="checklist[<?= $key ?>]" id="<?= $key ?>_ok" value="1" <?= $isOk ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success" for="<?= $key ?>_ok"><i class="ph ph-check"></i></label>

                                        <input type="radio" class="btn-check" name="checklist[<?= $key ?>]" id="<?= $key ?>_fail" value="0" <?= $isFail ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-danger" for="<?= $key ?>_fail"><i class="ph ph-x"></i></label>
                                    </div>
                                    <?php else: ?>
                                    <!-- Readonly View -->
                                    <div>
                                        <?php if ($val === null): ?>
                                            <span class="badge bg-secondary"><i class="ph ph-minus"></i> Pendente</span>
                                        <?php elseif ($isOk): ?>
                                            <span class="badge bg-success"><i class="ph ph-check"></i> Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="ph ph-x"></i> Reprovado</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Observações Gerais dos Testes</label>
                            <?php if ($canEdit): ?>
                            <textarea name="observacoes_checklist" class="form-control bg-light" rows="3" placeholder="Surgiram problemas de compatibilidade? Descreva os ensaios..."><?= $h['observacoes_checklist'] ?></textarea>
                            <?php else: ?>
                            <div class="p-3 bg-light rounded text-body-secondary fst-italic">
                                <?= $h['observacoes_checklist'] ?: 'Nenhuma observação informada.' ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($canEdit): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <span class="text-muted small"><i class="ph ph-info"></i> Você pode salvar o progresso agora e finalizar depois.</span>
                                <div>
                                    <button type="submit" class="btn btn-outline-dark">Salvar Progresso Parcial</button>
                                    <button type="button" class="btn btn-success ms-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFinalizar">Concluir Homologação</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <?php if ($h['status'] === 'concluida'): ?>
            <!-- Parecer Final (Somente Leitura) -->
            <div class="card border-0 shadow bg-white mt-4 border-start border-4 <?= $h['resultado'] === 'aprovado' ? 'border-success' : ($h['resultado'] === 'reprovado' ? 'border-danger' : 'border-warning') ?>">
                <div class="card-body p-4">
                    <h5 class="mb-3">
                        Resultado Final: 
                        <span class="text-uppercase fw-bold <?= $h['resultado'] === 'aprovado' ? 'text-success' : ($h['resultado'] === 'reprovado' ? 'text-danger' : 'text-warning') ?>"><?= $h['resultado'] ?></span>
                    </h5>
                    <p class="mb-1 fw-semibold text-secondary">Parecer do Laudo Técnico:</p>
                    <div class="p-3 bg-light rounded text-dark fs-6" style="border-left: 3px solid #ccc;">
                        <?= nl2br(e($h['parecer_final'])) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<!-- Modal Finalizar Homologação -->
<?php if ($h['status'] === 'em_homologacao' && $u['perfil'] === 'responsavel'): ?>
<div class="modal fade" id="modalFinalizar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Finalizar Homologação Técnica</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Atenção: Ao concluir, o status da homologação será fechado permanentemente e os relatórios ficam disponíveis para Compras.</p>
        <form method="POST" id="formFinalizar">
            <input type="hidden" name="acao" value="finalizar_homologacao">
            <!-- Pular inputs de checklist (serão copiados via js) -->
            <div id="hiddenChecklistData"></div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-success">Resultado da Homologação</label>
                <select name="resultado" class="form-select border-success" required>
                    <option value="">Selecione o veredito...</option>
                    <option value="aprovado">APROVADO (Equipamento validado)</option>
                    <option value="aprovado com ressalvas">APROVADO COM RESSALVAS</option>
                    <option value="reprovado">REPROVADO (Não atende os requisitos)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Data de Conclusão</label>
                <input type="date" name="data_fim_homologacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Parecer Final (Laudo descritivo)</label>
                <textarea name="parecer_final" class="form-control" rows="4" required placeholder="Forneça a justificativa técnica descrevendo o porquê foi aprovado ou reprovado."></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="submeterFormularioFinal()">Registrar Conclusão</button>
      </div>
    </div>
  </div>
</div>
<script>
    function submeterFormularioFinal() {
        // Clonar inputs originais pro hidden
        const formChecklistInputs = document.querySelectorAll('input[name^="checklist"]');
        const hiddenDiv = document.getElementById('hiddenChecklistData');
        hiddenDiv.innerHTML = '';
        formChecklistInputs.forEach(inp => {
            if (inp.checked) {
                const hiddenInp = document.createElement('input');
                hiddenInp.type = 'hidden';
                hiddenInp.name = inp.name;
                hiddenInp.value = inp.value;
                hiddenDiv.appendChild(hiddenInp);
            }
        });
        document.getElementById('formFinalizar').submit();
    }
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
