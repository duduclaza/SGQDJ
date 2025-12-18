<?php
/**
 * Cadastros 2.0 - Módulo Unificado de Produtos
 * Agrupa Toners, Peças e Máquinas em uma única interface
 */

$produtos = $produtos ?? [];
$stats = $stats ?? ['toner' => 0, 'peca' => 0, 'maquina' => 0];
$tipoAtual = $_GET['tipo'] ?? 'toner';

// Helper para escape
if (!function_exists('e')) {
    function e($value) { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📦 Cadastros 2.0</h1>
            <p class="text-gray-600 mt-1">Gerenciamento unificado de Produtos</p>
        </div>
        <button onclick="abrirModalProduto()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Produto
        </button>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Toners</p>
                    <p class="text-2xl font-bold"><?= $stats['toner'] ?></p>
                </div>
                <div class="text-4xl opacity-50">🖨️</div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">Peças</p>
                    <p class="text-2xl font-bold"><?= $stats['peca'] ?></p>
                </div>
                <div class="text-4xl opacity-50">🔧</div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Máquinas</p>
                    <p class="text-2xl font-bold"><?= $stats['maquina'] ?></p>
                </div>
                <div class="text-4xl opacity-50">🖥️</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="mudarTipo('toner')" class="tab-btn <?= $tipoAtual === 'toner' ? 'active' : '' ?>" data-tipo="toner">
                    <span class="mr-2">🖨️</span> Toners
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700"><?= $stats['toner'] ?></span>
                </button>
                <button onclick="mudarTipo('peca')" class="tab-btn <?= $tipoAtual === 'peca' ? 'active' : '' ?>" data-tipo="peca">
                    <span class="mr-2">🔧</span> Peças
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700"><?= $stats['peca'] ?></span>
                </button>
                <button onclick="mudarTipo('maquina')" class="tab-btn <?= $tipoAtual === 'maquina' ? 'active' : '' ?>" data-tipo="maquina">
                    <span class="mr-2">🖥️</span> Máquinas
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700"><?= $stats['maquina'] ?></span>
                </button>
            </nav>
        </div>

        <!-- Filtros -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="busca-produto" placeholder="Buscar por código, modelo ou descrição..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button onclick="carregarProdutos()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Buscar
                </button>
            </div>
        </div>

        <!-- Grid de Produtos -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase toner-only">Cor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase toner-only">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase toner-only">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Criado por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-produtos" class="bg-white divide-y divide-gray-200">
                    <!-- Dados carregados via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div id="no-data" class="text-center py-8 hidden">
            <p class="text-gray-500">Nenhum produto encontrado.</p>
        </div>
    </div>
</section>

<!-- Modal Novo/Editar Produto -->
<div id="modal-produto" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-900" id="modal-titulo">Novo Produto</h3>
            <button onclick="fecharModalProduto()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="form-produto">
            <input type="hidden" id="produto-id" name="id">
            
            <!-- Seleção de Tipo -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Produto *</label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="tipo_produto" value="toner" class="sr-only peer" checked>
                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:bg-gray-50 transition-colors">
                            <div class="text-2xl mb-1">🖨️</div>
                            <div class="font-medium">Toner</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="tipo_produto" value="peca" class="sr-only peer">
                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-colors">
                            <div class="text-2xl mb-1">🔧</div>
                            <div class="font-medium">Peça</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="tipo_produto" value="maquina" class="sr-only peer">
                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-colors">
                            <div class="text-2xl mb-1">🖥️</div>
                            <div class="font-medium">Máquina</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Campos Comuns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" id="codigo" name="codigo" placeholder="Ex: P-001, TN-123" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                    <input type="text" id="modelo" name="modelo" placeholder="Ex: HP CF280A" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea id="descricao" name="descricao" rows="2" placeholder="Descrição detalhada do produto"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Campos Específicos de Toner -->
            <div id="campos-toner" class="border-t pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <span class="mr-2">🖨️</span> Campos Específicos de Toner
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso Cheio (g)</label>
                        <input type="number" step="0.01" name="peso_cheio" placeholder="Ex: 850.50" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso Vazio (g)</label>
                        <input type="number" step="0.01" name="peso_vazio" placeholder="Ex: 120.30" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Capacidade Folhas</label>
                        <input type="number" name="capacidade_folhas" placeholder="Ex: 2700" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preço (R$)</label>
                        <input type="number" step="0.01" name="preco" placeholder="Ex: 89.90" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
                        <select name="cor" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Selecione</option>
                            <option value="Black">Black</option>
                            <option value="Cyan">Cyan</option>
                            <option value="Magenta">Magenta</option>
                            <option value="Yellow">Yellow</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="tipo_toner" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Selecione</option>
                            <option value="Original">Original</option>
                            <option value="Compativel">Compatível</option>
                            <option value="Remanufaturado">Remanufaturado</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="fecharModalProduto()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    💾 Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.tab-btn {
    padding: 1rem 1.5rem;
    font-weight: 500;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    display: flex;
    align-items: center;
}
.tab-btn:hover {
    color: #374151;
    background-color: #f9fafb;
}
.tab-btn.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    background-color: #eff6ff;
}
</style>

<script>
let tipoAtual = '<?= e($tipoAtual) ?>';
let produtosCache = [];

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    carregarProdutos();
    
    // Mostrar/ocultar campos de toner baseado no tipo
    document.querySelectorAll('input[name="tipo_produto"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const camposToner = document.getElementById('campos-toner');
            camposToner.style.display = this.value === 'toner' ? 'block' : 'none';
        });
    });
    
    // Busca em tempo real
    document.getElementById('busca-produto').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') carregarProdutos();
    });
});

// Mudar tipo
function mudarTipo(tipo) {
    tipoAtual = tipo;
    
    // Atualizar tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.tipo === tipo) {
            btn.classList.add('active');
        }
    });
    
    // Mostrar/ocultar colunas específicas de toner
    document.querySelectorAll('.toner-only').forEach(el => {
        el.style.display = tipo === 'toner' ? '' : 'none';
    });
    
    carregarProdutos();
}

// Carregar produtos
function carregarProdutos() {
    const busca = document.getElementById('busca-produto').value;
    const params = new URLSearchParams();
    params.append('tipo', tipoAtual);
    if (busca) params.append('busca', busca);
    
    fetch(`/cadastros-2/list?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                produtosCache = data.data;
                renderizarTabela();
            } else {
                alert('Erro ao carregar produtos: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

// Renderizar tabela
function renderizarTabela() {
    const tbody = document.getElementById('tabela-produtos');
    const noData = document.getElementById('no-data');
    
    if (produtosCache.length === 0) {
        tbody.innerHTML = '';
        noData.classList.remove('hidden');
        return;
    }
    
    noData.classList.add('hidden');
    
    tbody.innerHTML = produtosCache.map(p => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono bg-gray-100 text-gray-700">
                ${escapeHtml(p.codigo || '-')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${escapeHtml(p.modelo || '-')}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="${escapeHtml(p.descricao || '')}">
                ${escapeHtml(p.descricao || '-')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm toner-only" style="display: ${tipoAtual === 'toner' ? '' : 'none'}">
                ${getCorBadge(p.cor)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm toner-only" style="display: ${tipoAtual === 'toner' ? '' : 'none'}">
                ${escapeHtml(p.tipo_toner || '-')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm toner-only" style="display: ${tipoAtual === 'toner' ? '' : 'none'}">
                ${p.preco ? 'R$ ' + parseFloat(p.preco).toFixed(2) : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${escapeHtml(p.criador_nome || '-')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatarData(p.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button onclick="editarProduto(${p.id})" class="text-blue-600 hover:text-blue-800 mr-2">
                    ✏️ Editar
                </button>
                <button onclick="excluirProduto(${p.id})" class="text-red-600 hover:text-red-800">
                    🗑️ Excluir
                </button>
            </td>
        </tr>
    `).join('');
}

// Badge de cor
function getCorBadge(cor) {
    const cores = {
        'Black': 'bg-gray-800 text-white',
        'Cyan': 'bg-cyan-500 text-white',
        'Magenta': 'bg-pink-500 text-white',
        'Yellow': 'bg-yellow-400 text-gray-800'
    };
    if (!cor) return '-';
    return `<span class="px-2 py-1 text-xs rounded-full ${cores[cor] || 'bg-gray-200'}">${escapeHtml(cor)}</span>`;
}

// Abrir modal
function abrirModalProduto() {
    document.getElementById('modal-titulo').textContent = 'Novo Produto';
    document.getElementById('form-produto').reset();
    document.getElementById('produto-id').value = '';
    
    // Selecionar o tipo atual
    const radio = document.querySelector(`input[name="tipo_produto"][value="${tipoAtual}"]`);
    if (radio) radio.checked = true;
    
    // Mostrar/ocultar campos de toner
    document.getElementById('campos-toner').style.display = tipoAtual === 'toner' ? 'block' : 'none';
    
    document.getElementById('modal-produto').classList.remove('hidden');
}

// Fechar modal
function fecharModalProduto() {
    document.getElementById('modal-produto').classList.add('hidden');
}

// Editar produto
function editarProduto(id) {
    const produto = produtosCache.find(p => p.id == id);
    if (!produto) return;
    
    document.getElementById('modal-titulo').textContent = 'Editar Produto';
    document.getElementById('produto-id').value = produto.id;
    document.getElementById('codigo').value = produto.codigo || '';
    document.getElementById('modelo').value = produto.modelo || '';
    document.getElementById('descricao').value = produto.descricao || '';
    
    // Tipo
    const radio = document.querySelector(`input[name="tipo_produto"][value="${produto.tipo_produto}"]`);
    if (radio) radio.checked = true;
    
    // Campos de toner
    document.querySelector('input[name="peso_cheio"]').value = produto.peso_cheio || '';
    document.querySelector('input[name="peso_vazio"]').value = produto.peso_vazio || '';
    document.querySelector('input[name="capacidade_folhas"]').value = produto.capacidade_folhas || '';
    document.querySelector('input[name="preco"]').value = produto.preco || '';
    document.querySelector('select[name="cor"]').value = produto.cor || '';
    document.querySelector('select[name="tipo_toner"]').value = produto.tipo_toner || '';
    
    // Mostrar campos de toner se necessário
    document.getElementById('campos-toner').style.display = produto.tipo_produto === 'toner' ? 'block' : 'none';
    
    document.getElementById('modal-produto').classList.remove('hidden');
}

// Excluir produto
function excluirProduto(id) {
    if (!confirm('Tem certeza que deseja excluir este produto?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('/cadastros-2/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            carregarProdutos();
            mostrarNotificacao('Produto excluído com sucesso!', 'success');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao excluir produto');
    });
}

// Salvar produto
document.getElementById('form-produto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = document.getElementById('produto-id').value;
    const url = id ? '/cadastros-2/update' : '/cadastros-2/store';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fecharModalProduto();
            carregarProdutos();
            mostrarNotificacao(data.message, 'success');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar produto');
    });
});

// Helpers
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function formatarData(data) {
    if (!data) return '-';
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR');
}

function mostrarNotificacao(mensagem, tipo = 'info') {
    const container = document.createElement('div');
    container.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        tipo === 'success' ? 'bg-green-500 text-white' : 
        tipo === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    container.textContent = mensagem;
    document.body.appendChild(container);
    
    setTimeout(() => {
        container.classList.add('opacity-0');
        setTimeout(() => container.remove(), 300);
    }, 3000);
}
</script>
