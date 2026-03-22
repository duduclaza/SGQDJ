<?php
$pageTitle = 'Homologações - Kanban';
require_once __DIR__ . '/../partials/header.php';

$motivosHomologacao = [
    'novo_item' => 'Novo Item',
    'troca_fornecedor' => 'Troca de Fornecedor',
    'atualizacao_tecnica' => 'Atualização Técnica',
    'melhoria_custo' => 'Melhoria de Custo',
    'descontinuacao' => 'Descontinuação de Produto',
    'novo_fornecedor' => 'Novo Fornecedor'
];

$statusLabels = [
    'pendente_recebimento' => 'Pendente Recebimento',
    'em_analise' => 'Em Análise',
    'aprovado' => 'Aprovado',
    'reprovado' => 'Reprovado'
];

$statusColors = [
    'pendente_recebimento' => 'bg-yellow-100 border-yellow-300',
    'em_analise' => 'bg-blue-100 border-blue-300',
    'aprovado' => 'bg-green-100 border-green-300',
    'reprovado' => 'bg-red-100 border-red-300'
];
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📋 Homologações</h1>
            <p class="text-gray-600 mt-1">Gerenciamento de homologações de produtos e serviços</p>
        </div>
    </div>

    <!-- Faixa de atualização do módulo -->
    <div id="homologUpdateBanner" style="display:block;" class="mb-6 rounded-lg p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 rounded-full bg-yellow-500 animate-ping"></div>
            <div>
                <strong>Atualização em andamento:</strong>
                <span class="block text-sm">Este módulo está sendo atualizado — podem ocorrer bugs ou instabilidades.</span>
            </div>
        </div>
        <button id="closeBannerBtn" class="text-yellow-800 hover:text-yellow-900" onclick="(function(){document.getElementById('homologUpdateBanner').style.display='none'; try{localStorage.setItem('homologBannerClosed','1')}catch(e){}})();">Fechar ✖</button>
    </div>

    <?php if ($canCreate): ?>
    <!-- Formulário Inline de Criação -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">✨ Nova Solicitação de Homologação</h2>
            <button type="button" id="toggleFormBtn" class="text-blue-600 hover:text-blue-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <form id="formHomologacao" class="space-y-5" style="display: block;">
            <!-- Seção 1: Informações Básicas -->
            <fieldset class="border border-gray-300 rounded-lg p-4 space-y-4">
                <legend class="text-sm font-semibold text-gray-900 px-2">📋 Informações Básicas</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Código de Referência -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Código de Referência <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cod_referencia" id="cod_referencia" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: PROD-001">
                    </div>

                    <!-- Data de Vencimento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data de Vencimento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="data_vencimento" id="data_vencimento" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição <span class="text-red-500">*</span>
                    </label>
                    <textarea name="descricao" id="descricao" required rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Descreva o produto/serviço a ser homologado"></textarea>
                </div>

                <!-- Observação -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observação
                    </label>
                    <textarea name="observacao" id="observacao" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Adicione observações gerais"></textarea>
                </div>
            </fieldset>

            <!-- Seção 2: Responsáveis e Departamento -->
            <fieldset class="border border-gray-300 rounded-lg p-4 space-y-4">
                <legend class="text-sm font-semibold text-gray-900 px-2">👥 Responsáveis</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Departamento (Funil) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Departamento (Funil) <span class="text-red-500">*</span>
                        </label>
                        <select name="departamento_resp_id" id="departamento_resp_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecione...</option>
                            <?php foreach ($departamentos as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pessoas Responsáveis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pessoas Responsáveis <span class="text-red-500">*</span>
                        </label>
                        <select name="responsaveis[]" id="responsaveis" multiple required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="min-height: 100px;">
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario['id'] ?>">
                                    <?= htmlspecialchars($usuario['name']) ?> (<?= htmlspecialchars($usuario['email'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">💡 Selecione Ctrl+Click para múltiplos responsáveis</p>
                    </div>
                </div>

                <!-- Avisar Logística -->
                <div class="flex items-center">
                    <input type="checkbox" name="avisar_logistica" id="avisar_logistica" value="1"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="avisar_logistica" class="ml-2 text-sm text-gray-700">
                        🚚 Notificar Logística sobre chegada da homologação
                    </label>
                </div>
            </fieldset>

            <!-- Seção 3: Detalhes da Homologação -->
            <fieldset class="border border-gray-300 rounded-lg p-4 space-y-4">
                <legend class="text-sm font-semibold text-gray-900 px-2">🔍 Detalhes da Homologação</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tipo de Homologação -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Homologação
                        </label>
                        <select name="tipo_homologacao" id="tipo_homologacao"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                onchange="toggle_nome_cliente()">
                            <option value="">Selecione...</option>
                            <option value="interna">Interna</option>
                            <option value="cliente">Em Cliente</option>
                        </select>
                    </div>

                    <!-- Nome do Cliente (condicional) -->
                    <div id="div_nome_cliente" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nome do Cliente <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nome_cliente" id="nome_cliente"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Digite o nome do cliente">
                    </div>
                </div>

                <!-- Data de Instalação -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Instalação
                    </label>
                    <input type="date" name="data_instalacao" id="data_instalacao"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Observações Detalhes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observações Adicionais
                    </label>
                    <textarea name="observacoes_detalhes" id="observacoes_detalhes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Adicione observações específicas da homologação"></textarea>
                </div>

                <!-- Equipamento Atendeu Especificativas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Equipamento Atendeu as Especificativas?
                        </label>
                        <select name="equipamento_atendeu_especificativas" id="equipamento_atendeu_especificativas"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                onchange="toggle_motivo_nao_atendeu()">
                            <option value="">Selecione...</option>
                            <option value="sim">Sim, atendeu completamente</option>
                            <option value="parcial">Parcialmente</option>
                            <option value="nao">Não, não atendeu</option>
                        </select>
                    </div>

                    <!-- Motivo Não Atendeu (condicional) -->
                    <div id="div_motivo_nao_atendeu" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Motivo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="motivo_nao_atendeu" id="motivo_nao_atendeu" placeholder="Descreva brevemente o motivo"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </fieldset>

            <!-- Botões -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" id="btnCancelar"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="btnSalvar"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Criar Homologação</span>
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>


    <!-- Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <?php foreach ($statusLabels as $status => $label): ?>
        <div class="bg-gray-100 rounded-lg p-4 min-h-[600px]">
            <!-- Cabeçalho da Coluna -->
            <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-gray-300">
                <h3 class="font-semibold text-gray-900 text-lg"><?= $label ?></h3>
                <span class="bg-gray-600 text-white text-xs font-bold rounded-full px-2 py-1">
                    <?= count($homologacoes[$status] ?? []) ?>
                </span>
            </div>

            <!-- Cartões -->
            <div class="space-y-3 kanban-column" data-status="<?= $status ?>">
                <?php if (isset($homologacoes[$status]) && count($homologacoes[$status]) > 0): ?>
                    <?php foreach ($homologacoes[$status] as $item): ?>
                        <div class="kanban-card bg-white rounded-lg shadow-sm border-l-4 <?= $statusColors[$status] ?> p-4 cursor-pointer hover:shadow-md transition-shadow"
                             data-id="<?= $item['id'] ?>"
                             data-status="<?= $item['status'] ?>"
                             onclick="verDetalhes(<?= $item['id'] ?>)">
                            
                            <!-- Código do Produto -->
                            <div class="flex items-start justify-between mb-2">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">
                                    <?= htmlspecialchars($item['codigo_produto']) ?>
                                </span>
                                <button onclick="event.stopPropagation(); openCardMenu(<?= $item['id'] ?>)" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Descrição -->
                            <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                <?= htmlspecialchars($item['descricao']) ?>
                            </h4>

                            <!-- Fornecedor -->
                            <p class="text-sm text-gray-600 mb-2">
                                🏢 <?= htmlspecialchars($item['fornecedor']) ?>
                            </p>

                            <!-- Motivo -->
                            <div class="mb-3">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    <?= $motivosHomologacao[$item['motivo_homologacao']] ?? $item['motivo_homologacao'] ?>
                                </span>
                            </div>

                            <!-- Footer do Card -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t">
                                <div class="flex items-center space-x-2">
                                    <!-- Responsáveis -->
                                    <div class="flex items-center" title="Responsáveis: <?= htmlspecialchars($item['responsaveis_nomes'] ?? 'N/A') ?>">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                        </svg>
                                        <span><?= substr_count($item['responsaveis_nomes'] ?? '', ',') + 1 ?></span>
                                    </div>

                                    <!-- Anexos -->
                                    <?php if ($item['total_anexos'] > 0): ?>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><?= $item['total_anexos'] ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Aviso Logística -->
                                    <?php if ($item['avisar_logistica']): ?>
                                    <div class="flex items-center text-orange-600" title="Logística notificada">
                                        🚚
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Data -->
                                <span><?= date('d/m/Y', strtotime($item['data_solicitacao'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-gray-400 py-8">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Nenhum cartão</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Detalhes -->
<div id="modalDetalhes" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900">Detalhes da Homologação</h3>
            <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent" class="p-6">
            <!-- Conteúdo carregado via AJAX -->
        </div>
    </div>
</div>

<script>
// Funções de toggle de campos condicionais
function toggle_nome_cliente() {
    const tipoHomologacao = document.getElementById('tipo_homologacao').value;
    const divNomeCliente = document.getElementById('div_nome_cliente');
    const inputNomeCliente = document.getElementById('nome_cliente');
    
    if (tipoHomologacao === 'cliente') {
        divNomeCliente.style.display = 'block';
        inputNomeCliente.required = true;
    } else {
        divNomeCliente.style.display = 'none';
        inputNomeCliente.required = false;
        inputNomeCliente.value = '';
    }
}

function toggle_motivo_nao_atendeu() {
    const atendeuEspecificativas = document.getElementById('equipamento_atendeu_especificativas').value;
    const divMotivoNaoAtendeu = document.getElementById('div_motivo_nao_atendeu');
    const inputMotivoNaoAtendeu = document.getElementById('motivo_nao_atendeu');
    
    if (atendeuEspecificativas === 'nao' || atendeuEspecificativas === 'parcial') {
        divMotivoNaoAtendeu.style.display = 'block';
        inputMotivoNaoAtendeu.required = true;
    } else {
        divMotivoNaoAtendeu.style.display = 'none';
        inputMotivoNaoAtendeu.required = false;
        inputMotivoNaoAtendeu.value = '';
    }
}

// Toggle do formulário
document.getElementById('toggleFormBtn')?.addEventListener('click', function() {
    const form = document.getElementById('formHomologacao');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('btnCancelar')?.addEventListener('click', function() {
    document.getElementById('formHomologacao').reset();
    document.getElementById('formHomologacao').style.display = 'none';
    // Resetar campos condicionais
    document.getElementById('div_nome_cliente').style.display = 'none';
    document.getElementById('div_motivo_nao_atendeu').style.display = 'none';
});

// Submit do formulário
document.getElementById('formHomologacao')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btnSalvar = document.getElementById('btnSalvar');
    btnSalvar.disabled = true;
    btnSalvar.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Criando...';
    
    try {
        const formData = new FormData(this);
        const response = await fetch('/homologacoes/store', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Homologação criada com sucesso!');
            location.reload();
        } else {
            alert('❌ ' + (result.message || 'Erro ao criar homologação'));
        }
    } catch (error) {
        alert('❌ Erro: ' + error.message);
    } finally {
        btnSalvar.disabled = false;
        btnSalvar.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> <span>Criar Homologação</span>';
    }
});

// Ver detalhes
async function verDetalhes(id) {
    document.getElementById('modalDetalhes').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div><p class="mt-4 text-gray-600">Carregando...</p></div>';
    
    try {
        const response = await fetch(`/homologacoes/${id}/details`);
        const result = await response.json();
        
        if (result.success) {
            mostrarDetalhes(result);
        } else {
            document.getElementById('modalContent').innerHTML = '<p class="text-red-600">Erro ao carregar detalhes</p>';
        }
    } catch (error) {
        document.getElementById('modalContent').innerHTML = '<p class="text-red-600">Erro: ' + error.message + '</p>';
    }
}

function mostrarDetalhes(data) {
    const h = data.homologacao;
    
    let html = `
        <div class="space-y-4">
            <!-- Informações Básicas -->
            <div class="border-b pb-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">📋 Informações Básicas</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="text-gray-500">Código Referência</label>
                        <p class="font-mono text-gray-900">${h.cod_referencia}</p>
                    </div>
                    <div>
                        <label class="text-gray-500">Departamento</label>
                        <p class="font-gray-900">${h.departamento_nome || 'N/A'}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-gray-500">Descrição</label>
                        <p class="text-gray-900">${h.descricao}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-gray-500">Observação</label>
                        <p class="text-gray-900">${h.observacao || 'N/A'}</p>
                    </div>
                </div>
            </div>

            <!-- Responsáveis -->
            <div class="border-b pb-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">👥 Responsáveis</h4>
                <div class="space-y-2 text-sm">
                    ${data.responsaveis.map(r => `<div class="flex items-center justify-between bg-blue-50 p-2 rounded">
                        <span>${r.name}</span>
                        <span class="text-gray-500 text-xs">${r.email}</span>
                    </div>`).join('')}
                </div>
            </div>

            <!-- Detalhes da Homologação -->
            <div class="border-b pb-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">🔍 Detalhes da Homologação</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="text-gray-500">Tipo</label>
                        <p class="font-gray-900">${h.tipo_homologacao ? (h.tipo_homologacao === 'interna' ? 'Interna' : 'Em Cliente') : 'N/A'}</p>
                    </div>
                    ${h.tipo_homologacao === 'cliente' ? `<div>
                        <label class="text-gray-500">Nome do Cliente</label>
                        <p class="font-gray-900">${h.nome_cliente || 'N/A'}</p>
                    </div>` : ''}
                    <div>
                        <label class="text-gray-500">Data de Instalação</label>
                        <p class="font-gray-900">${h.data_instalacao ? new Date(h.data_instalacao).toLocaleDateString('pt-BR') : 'N/A'}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-gray-500">Observações Adicionais</label>
                        <p class="text-gray-900">${h.observacoes_detalhes || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="text-gray-500">Equipamento Atendeu Especificativas</label>
                        <p class="font-gray-900">${h.equipamento_atendeu_especificativas ? (h.equipamento_atendeu_especificativas === 'sim' ? '✅ Sim' : (h.equipamento_atendeu_especificativas === 'parcial' ? '⚠️ Parcial' : '❌ Não')) : 'N/A'}</p>
                    </div>
                    ${h.motivo_nao_atendeu ? `<div class="col-span-2">
                        <label class="text-gray-500">Motivo</label>
                        <p class="text-gray-900">${h.motivo_nao_atendeu}</p>
                    </div>` : ''}
                </div>
            </div>

            <!-- Status e Datas -->
            <div class="border-b pb-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">📊 Status</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="text-gray-500">Status Atual</label>
                        <p class="font-gray-900 uppercase">${h.status}</p>
                    </div>
                    <div>
                        <label class="text-gray-500">Data Vencimento</label>
                        <p class="font-gray-900">${h.data_vencimento ? new Date(h.data_vencimento).toLocaleDateString('pt-BR') : 'N/A'}</p>
                    </div>
                    <div>
                        <label class="text-gray-500">Criado por</label>
                        <p class="font-gray-900">${h.criador_nome}</p>
                    </div>
                    <div>
                        <label class="text-gray-500">Data Criação</label>
                        <p class="font-gray-900">${new Date(h.created_at).toLocaleDateString('pt-BR')}</p>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">📜 Histórico</h4>
                <div class="space-y-2 max-h-[300px] overflow-y-auto">
                    ${data.historico.map(h => `<div class="text-xs bg-gray-50 p-2 rounded border-l-2 border-gray-300">
                        <p class="font-semibold text-gray-900">${h.usuario_nome || 'Sistema'}</p>
                        <p class="text-gray-600">${h.observacao || 'Sem observação'}</p>
                        <p class="text-gray-400 text-[10px]">${new Date(h.created_at).toLocaleString('pt-BR')}</p>
                    </div>`).join('')}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('modalContent').innerHTML = html;
}

function fecharModal() {
    document.getElementById('modalDetalhes').classList.add('hidden');
}

// Fechar modal ao clicar fora
document.getElementById('modalDetalhes')?.addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModal();
    }
});

// Banner: fechar e persistir preferência
document.getElementById('closeBannerBtn')?.addEventListener('click', function() {
    const b = document.getElementById('homologUpdateBanner');
    if (b) {
        b.style.display = 'none';
        try { localStorage.setItem('homologBannerClosed', '1'); } catch (e) {}
    }
});

// Esconder banner se usuário já fechou antes
try {
    if (localStorage.getItem('homologBannerClosed') === '1') {
        const b = document.getElementById('homologUpdateBanner');
        if (b) b.style.display = 'none';
    }
} catch (e) {}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.kanban-card {
    transition: all 0.2s ease;
}

.kanban-card:hover {
    transform: translateY(-2px);
}

.renovation-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(90deg, rgba(255,200,0,0.12), rgba(255,200,0,0.02));
    color: #92400e;
    padding: 0.15rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.85rem;
    animation: pulse 1.8s infinite;
}

@keyframes pulse {
    0% { transform: translateY(0); opacity: 1; }
    50% { transform: translateY(-2px); opacity: 0.85; }
    100% { transform: translateY(0); opacity: 1; }
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
