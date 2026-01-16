<section class="space-y-6">
  <!-- Header com navegação -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-4">
      <a href="/rh" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <div>
        <h1 class="text-2xl font-bold text-gray-900">🎯 Avaliação de Desempenho</h1>
        <p class="text-sm text-gray-600">Avalie a performance dos colaboradores com métricas e feedbacks estruturados</p>
      </div>
    </div>
    <button onclick="abrirModalNovaAvaliacao()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-lg">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
      </svg>
      Nova Avaliação
    </button>
  </div>

  <!-- Abas -->
  <div class="border-b border-gray-200">
    <nav class="flex space-x-4" aria-label="Tabs">
      <button onclick="trocarAba('dashboard')" id="tab-dashboard" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600">
        📊 Dashboard
      </button>
      <button onclick="trocarAba('avaliacoes')" id="tab-avaliacoes" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
        📋 Avaliações
      </button>
      <button onclick="trocarAba('formularios')" id="tab-formularios" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
        📝 Formulários
      </button>
      <button onclick="trocarAba('relatorios')" id="tab-relatorios" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
        📈 Relatórios
      </button>
    </nav>
  </div>

  <!-- Conteúdo das Abas -->
  
  <!-- Aba Dashboard -->
  <div id="content-dashboard" class="tab-content">
    <div class="mb-6 flex justify-between items-center">
      <h2 class="text-xl font-bold text-gray-800">Visão Geral</h2>
      <div class="flex gap-4">
         <select id="filtroColaboradorDashboard" onchange="carregarDashboardStats()" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white shadow-sm">
            <option value="">Todos os Colaboradores</option>
            <!-- Preenchido via JS -->
         </select>
         <button onclick="carregarDashboardStats()" class="p-2 text-gray-500 hover:text-blue-600 transition-colors" title="Atualizar">
           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
         </button>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1/2 bg-white/5 skew-x-12 transform origin-bottom-right"></div>
        <div class="flex items-center justify-between relative z-10">
          <div>
            <p class="text-blue-100 text-sm">Total de Avaliações</p>
            <p class="text-3xl font-bold" id="kpiTotal">-</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-2xl">📊</span>
          </div>
        </div>
        <p class="text-blue-100 text-xs mt-2 relative z-10" id="kpiTotalDesc">Carregando...</p>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm">Concluídas</p>
            <p class="text-3xl font-bold" id="kpiConcluidas">-</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-2xl">✅</span>
          </div>
        </div>
        <p class="text-green-100 text-xs mt-2" id="kpiConcluidasDesc">Carregando...</p>
      </div>
      
      <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-amber-100 text-sm">Pendentes</p>
            <p class="text-3xl font-bold" id="kpiPendentes">-</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-2xl">⏳</span>
          </div>
        </div>
        <p class="text-amber-100 text-xs mt-2">Aguardando avaliação</p>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm">Média Geral</p>
            <p class="text-3xl font-bold" id="kpiMedia">-</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-2xl">⭐</span>
          </div>
        </div>
        <p class="text-purple-100 text-xs mt-2">Nota de 0 a 10</p>
      </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Avaliações por Departamento</h3>
        <div class="space-y-4" id="chartDepartamentos">
          <div class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-4 py-1">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="space-y-2">
                <div class="h-4 bg-gray-200 rounded"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⭐ Distribuição de Notas</h3>
        <div class="space-y-4" id="chartNotas">
          <div class="animate-pulse flex space-x-4">
             <div class="flex-1 space-y-4 py-1">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="space-y-2">
                <div class="h-4 bg-gray-200 rounded"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Gráfico de Evolução -->
    <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Evolução da Média Geral (Últimos 12 Meses)</h3>
      <div class="relative h-80 w-full">
        <canvas id="chartEvolucao"></canvas>
      </div>
    </div>
    </div>
  </div>

  <!-- Aba Avaliações -->
  <div id="content-avaliacoes" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
      <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <input type="text" id="searchAvaliacoes" placeholder="Buscar colaborador..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
        <div class="flex items-center gap-2">
          <select id="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Todos os status</option>
            <option value="concluída">Concluída</option>
            <option value="pendente">Pendente</option>
            <option value="em_andamento">Em andamento</option>
          </select>
        </div>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avaliador</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody id="tabelaAvaliacoes" class="bg-white divide-y divide-gray-200">
          <!-- Dados carregados via JavaScript -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Aba Formulários -->
  <div id="content-formularios" class="tab-content hidden">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Modelos de Formulários</h3>
        <p class="text-sm text-gray-600">Crie formulários personalizados para diferentes tipos de avaliação</p>
      </div>
      <button onclick="abrirModalFormulario()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Novo Formulário
      </button>
    </div>
    
    <div id="formulariosGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Formulários carregados via JavaScript -->
      <div class="col-span-full text-center py-12 text-gray-500">
        <span class="text-4xl mb-2 block">📝</span>
        Carregando formulários...
      </div>
    </div>
  </div>

  <!-- Aba Relatórios -->
  <div id="content-relatorios" class="tab-content hidden">
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-8 text-center">
      <div class="text-purple-600 mb-4">
        <span class="text-5xl">📈</span>
      </div>
      <h3 class="text-xl font-semibold text-purple-800 mb-2">Relatórios e Análises</h3>
      <p class="text-purple-700 mb-4">Exporte relatórios detalhados e visualize tendências de desempenho.</p>
      <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-sm font-medium rounded-full">
        ⚙️ Em desenvolvimento
      </span>
    </div>
  </div>
</section>

<!-- Modal Nova Avaliação -->
<div id="modalNovaAvaliacao" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
    <div class="p-6 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-900">🎯 Nova Avaliação</h3>
        <button onclick="fecharModalNovaAvaliacao()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    <form id="formNovaAvaliacao" class="p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador *</label>
        <select id="selectColaborador" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
          <option value="">Carregando colaboradores...</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Avaliação *</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
          <option value="">Selecione o tipo</option>
          <option value="trimestral">Avaliação Trimestral</option>
          <option value="semestral">Avaliação Semestral</option>
          <option value="anual">Avaliação Anual</option>
          <option value="experiencia">Avaliação de Experiência</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Período de Referência</label>
        <input type="month" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <button type="button" onclick="fecharModalNovaAvaliacao()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancelar
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Iniciar Avaliação
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Criar/Editar Formulário -->
<div id="modalFormulario" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
  <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full my-8">
    <div class="p-6 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <h3 id="modalFormTitulo" class="text-xl font-semibold text-gray-900">📝 Novo Formulário de Avaliação</h3>
        <button onclick="fecharModalFormulario()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    <form id="formFormulario" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
      <input type="hidden" id="form_id" value="">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Título do Formulário *</label>
          <input type="text" id="form_titulo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Ex: Avaliação de Desempenho Trimestral">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
          <select id="form_tipo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            <option value="desempenho">Avaliação de Desempenho</option>
            <option value="experiencia">Avaliação de Experiência</option>
            <option value="trimestral">Trimestral</option>
            <option value="semestral">Semestral</option>
            <option value="anual">Anual</option>
            <option value="bonificacao">Bonificação</option>
            <option value="disc">DISC</option>
          </select>
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea id="form_descricao" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Breve descrição do formulário..."></textarea>
      </div>
      
      <div>
        <div class="flex justify-between items-center mb-3">
          <label class="block text-sm font-medium text-gray-700">Perguntas do Formulário *</label>
          <button type="button" onclick="adicionarPerguntaForm()" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Adicionar Pergunta
          </button>
        </div>
        
        <div id="perguntasFormContainer" class="space-y-3">
          <!-- Perguntas serão adicionadas aqui -->
        </div>
      </div>
      
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <button type="button" onclick="fecharModalFormulario()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancelar
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Salvar Formulário
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Funções de Abas
function trocarAba(aba) {
  // Esconder todos os conteúdos
  document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
  // Remover estilo ativo de todas as abas
  document.querySelectorAll('.tab-btn').forEach(el => {
    el.classList.remove('border-blue-600', 'text-blue-600');
    el.classList.add('border-transparent', 'text-gray-500');
  });
  
  // Mostrar conteúdo da aba selecionada
  document.getElementById('content-' + aba).classList.remove('hidden');
  // Ativar aba selecionada
  const tabBtn = document.getElementById('tab-' + aba);
  tabBtn.classList.add('border-blue-600', 'text-blue-600');
  tabBtn.classList.remove('border-transparent', 'text-gray-500');
  
  // Carregar dados se necessário
  if (aba === 'avaliacoes') {
    carregarAvaliacoes();
  } else if (aba === 'formularios') {
    carregarFormularios();
  }
}

// Carregar avaliações
function carregarAvaliacoes() {
  fetch('/rh/avaliacoes/listar')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        renderAvaliacoes(data.avaliacoes);
      }
    })
    .catch(err => console.error('Erro ao carregar avaliações:', err));
}

// Renderizar tabela de avaliações
function renderAvaliacoes(avaliacoes) {
  const tbody = document.getElementById('tabelaAvaliacoes');
  
  if (avaliacoes.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Nenhuma avaliação encontrada</td></tr>';
    return;
  }
  
  tbody.innerHTML = avaliacoes.map(a => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-4">
        <div class="font-medium text-gray-900">${escapeHtml(a.colaborador)}</div>
        <div class="text-sm text-gray-500">${escapeHtml(a.cargo)}</div>
      </td>
      <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(a.departamento)}</td>
      <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(a.avaliador)}</td>
      <td class="px-6 py-4 text-sm text-gray-600">${a.data_avaliacao ? formatarData(a.data_avaliacao) : '-'}</td>
      <td class="px-6 py-4">
        ${a.nota_geral !== null ? `
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium ${getNotaClass(a.nota_geral)}">
            ${a.nota_geral.toFixed(1)}
          </span>
        ` : '<span class="text-gray-400">-</span>'}
      </td>
      <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(a.status)}">
          ${getStatusLabel(a.status)}
        </span>
      </td>
      <td class="px-6 py-4">
        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver detalhes</button>
      </td>
    </tr>
  `).join('');
}

// Helpers
function escapeHtml(text) {
  const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return text.replace(/[&<>"']/g, m => map[m]);
}

function formatarData(dataStr) {
  return new Date(dataStr).toLocaleDateString('pt-BR');
}

function getNotaClass(nota) {
  if (nota >= 9) return 'bg-green-100 text-green-800';
  if (nota >= 7) return 'bg-blue-100 text-blue-800';
  if (nota >= 5) return 'bg-amber-100 text-amber-800';
  return 'bg-red-100 text-red-800';
}

function getStatusClass(status) {
  switch(status) {
    case 'concluída': return 'bg-green-100 text-green-800';
    case 'pendente': return 'bg-amber-100 text-amber-800';
    case 'em_andamento': return 'bg-blue-100 text-blue-800';
    default: return 'bg-gray-100 text-gray-800';
  }
}

function getStatusLabel(status) {
  switch(status) {
    case 'concluída': return '✓ Concluída';
    case 'pendente': return '⏳ Pendente';
    case 'em_andamento': return '🔄 Em andamento';
    default: return status;
  }
}

// Modal
function abrirModalNovaAvaliacao() {
  document.getElementById('modalNovaAvaliacao').classList.remove('hidden');
  carregarColaboradores();
}

function fecharModalNovaAvaliacao() {
  document.getElementById('modalNovaAvaliacao').classList.add('hidden');
}

// Carregar colaboradores do sistema
function carregarColaboradores() {
  const select = document.getElementById('selectColaborador');
  select.innerHTML = '<option value="">Carregando...</option>';
  
  fetch('/rh/colaboradores/listar')
    .then(r => r.json())
    .then(data => {
      if (data.success && data.colaboradores) {
        select.innerHTML = '<option value="">Selecione o colaborador</option>';
        data.colaboradores.forEach(c => {
          const setor = c.setor || 'Sem setor';
          select.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)} - ${escapeHtml(setor)}</option>`;
        });
      } else {
        select.innerHTML = '<option value="">Erro ao carregar</option>';
      }
    })
    .catch(err => {
      console.error('Erro ao carregar colaboradores:', err);
      select.innerHTML = '<option value="">Erro ao carregar</option>';
    });
}

// Form submit
document.getElementById('formNovaAvaliacao').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Funcionalidade em desenvolvimento!');
  fecharModalNovaAvaliacao();
});

// ========== FORMULÁRIOS DE AVALIAÇÃO ==========

let perguntasForm = [];

// Carregar formulários ao trocar para aba
function carregarFormularios() {
  const grid = document.getElementById('formulariosGrid');
  grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500"><span class="text-4xl mb-2 block">⏳</span>Carregando...</div>';
  
  fetch('/rh/formularios/listar')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        renderFormularios(data.formularios);
      } else {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Erro ao carregar formulários</div>';
      }
    })
    .catch(err => {
      console.error(err);
      grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Erro ao carregar</div>';
    });
}

// Renderizar grid de formulários
function renderFormularios(formularios) {
  const grid = document.getElementById('formulariosGrid');
  
  if (formularios.length === 0) {
    grid.innerHTML = `
      <div class="col-span-full text-center py-12">
        <span class="text-5xl mb-4 block">📝</span>
        <p class="text-gray-600 mb-4">Nenhum formulário criado ainda</p>
        <button onclick="abrirModalFormulario()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
          Criar Primeiro Formulário
        </button>
      </div>
    `;
    return;
  }
  
  grid.innerHTML = formularios.map(f => `
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-all duration-300">
      <div class="flex items-start justify-between mb-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-xl">📝</span>
        </div>
        <span class="px-2 py-1 text-xs font-medium rounded-full ${f.ativo == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
          ${f.ativo == 1 ? 'Ativo' : 'Inativo'}
        </span>
      </div>
      <h4 class="font-semibold text-gray-900 mb-1">${escapeHtml(f.titulo)}</h4>
      <p class="text-sm text-gray-600 mb-3 line-clamp-2">${escapeHtml(f.descricao || 'Sem descrição')}</p>
      <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
        <span>📊 ${f.total_perguntas} perguntas</span>
        <span>📋 ${f.total_avaliacoes} avaliações</span>
      </div>
      ${f.url_publica ? `
      <div class="flex items-center gap-2 mb-3">
        <button onclick="copiarLink('${f.url_publica}')" class="flex-1 text-center px-2 py-1.5 text-xs bg-green-100 text-green-700 hover:bg-green-200 rounded-lg transition-colors" title="Copiar Link">
          🔗 Copiar Link
        </button>
        <button onclick="mostrarQRCode('${f.url_publica}', '${escapeHtml(f.titulo).replace(/'/g, "\\'")}')" class="flex-1 text-center px-2 py-1.5 text-xs bg-purple-100 text-purple-700 hover:bg-purple-200 rounded-lg transition-colors" title="QR Code">
          📱 QR Code
        </button>
      </div>
      ` : ''}
      <div class="flex items-center gap-1 pt-3 border-t border-gray-100">
        <button onclick="duplicarFormularioRh(${f.id})" class="flex-1 text-center px-2 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors" title="Duplicar">
          📋
        </button>
        <button onclick="editarFormularioRh(${f.id}, ${f.total_avaliacoes})" class="flex-1 text-center px-2 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
          ✏️
        </button>
        <button onclick="excluirFormularioRh(${f.id}, ${f.total_avaliacoes})" class="flex-1 text-center px-2 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Excluir">
          🗑️
        </button>
      </div>
    </div>
  `).join('');
}

// Modal de formulário
function abrirModalFormulario() {
  document.getElementById('modalFormTitulo').textContent = '📝 Novo Formulário de Avaliação';
  document.getElementById('form_id').value = '';
  document.getElementById('form_titulo').value = '';
  document.getElementById('form_descricao').value = '';
  document.getElementById('form_tipo').value = 'desempenho';
  
  perguntasForm = [];
  document.getElementById('perguntasFormContainer').innerHTML = '';
  adicionarPerguntaForm(); // Adicionar primeira pergunta
  
  document.getElementById('modalFormulario').classList.remove('hidden');
}

function fecharModalFormulario() {
  document.getElementById('modalFormulario').classList.add('hidden');
}

// Adicionar pergunta ao formulário
function adicionarPerguntaForm() {
  const index = perguntasForm.length;
  perguntasForm.push({ texto: '', tipo: 'texto' });
  
  const container = document.getElementById('perguntasFormContainer');
  const div = document.createElement('div');
  div.className = 'border border-gray-300 rounded-lg p-4 bg-gray-50';
  div.id = `pergunta_form_${index}`;
  div.innerHTML = `
    <div class="flex justify-between items-center mb-3">
      <span class="text-sm font-medium text-gray-700">Pergunta ${index + 1}</span>
      <button type="button" onclick="removerPerguntaForm(${index})" class="text-red-600 hover:text-red-700 text-sm">
        Remover
      </button>
    </div>
    <input type="text" id="pf_texto_${index}" placeholder="Digite a pergunta" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-2 focus:ring-2 focus:ring-blue-500" required>
    <div class="grid grid-cols-2 gap-2">
      <select id="pf_tipo_${index}" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <option value="texto">Resposta de Texto</option>
        <option value="numero">Número (0-10)</option>
        <option value="escala_1_5">Escala 1-5</option>
        <option value="escala_1_10">Escala 1-10</option>
        <option value="sim_nao">Sim/Não</option>
        <option value="multipla">Múltipla Escolha</option>
      </select>
      <input type="number" id="pf_peso_${index}" placeholder="Peso (default: 1)" step="0.1" min="0" max="10" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>
  `;
  container.appendChild(div);
}

// Remover pergunta
function removerPerguntaForm(index) {
  const el = document.getElementById(`pergunta_form_${index}`);
  if (el) el.remove();
  // Renumerar
  let count = 1;
  document.querySelectorAll('#perguntasFormContainer > div').forEach(div => {
    const label = div.querySelector('.text-sm.font-medium');
    if (label) label.textContent = `Pergunta ${count++}`;
  });
}

// Carregar colaboradores
// Carregar colaboradores
function carregarColaboradores() {
  fetch('/rh/colaboradores/listar')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Preencher select do modal de nova avaliação (já existente)
        const selectModal = document.getElementById('selectColaborador');
        if (selectModal) {
          selectModal.innerHTML = '<option value="">Selecione o colaborador...</option>' + 
            data.colaboradores.map(c => `<option value="${c.id}">${c.nome} (${c.setor || 'N/A'})</option>`).join('');
        }

        // Preencher select de filtro do dashboard (NOVO)
        const selectFiltro = document.getElementById('filtroColaboradorDashboard');
        if (selectFiltro) {
          // Manter opção "Todos"
          const valorAtual = selectFiltro.value;
          selectFiltro.innerHTML = '<option value="">Todos os Colaboradores</option>' + 
            data.colaboradores.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
          selectFiltro.value = valorAtual;
        }
      }
    })
    .catch(console.error);
}

// Carregar formulário para edição
function editarFormularioRh(id, totalAvaliacoes) {
  if (parseInt(totalAvaliacoes) > 0) {
    alert('Não é possível editar! Este formulário possui ' + totalAvaliacoes + ' avaliação(ões) vinculada(s).');
    return;
  }
  fetch(`/rh/formularios/${id}/detalhes`)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const f = data.formulario;
        
        document.getElementById('modalFormTitulo').textContent = '✏️ Editar Formulário';
        document.getElementById('form_id').value = f.id;
        document.getElementById('form_titulo').value = f.titulo;
        document.getElementById('form_descricao').value = f.descricao || '';
        document.getElementById('form_tipo').value = f.tipo;
        
        perguntasForm = [];
        document.getElementById('perguntasFormContainer').innerHTML = '';
        
        if (f.perguntas && f.perguntas.length > 0) {
          f.perguntas.forEach((p, i) => {
            adicionarPerguntaForm();
            setTimeout(() => { // Pequeno delay para garantir que o DOM foi criado
                const elTexto = document.getElementById(`pf_texto_${i}`);
                const elTipo = document.getElementById(`pf_tipo_${i}`);
                const elPeso = document.getElementById(`pf_peso_${i}`);
                if(elTexto) elTexto.value = p.texto;
                if(elTipo) elTipo.value = p.tipo;
                if(elPeso) elPeso.value = p.peso || 1;
            }, 10);
          });
        } else {
          adicionarPerguntaForm();
        }
        
        document.getElementById('modalFormulario').classList.remove('hidden');
      } else {
        alert('Erro: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Erro ao carregar formulário');
    });
}

// Excluir formulário
function excluirFormularioRh(id, totalAvaliacoes) {
  if (parseInt(totalAvaliacoes) > 0) {
    alert('Não é possível excluir! Este formulário possui ' + totalAvaliacoes + ' avaliação(ões) vinculada(s).');
    return;
  }
  
  if (!confirm('Tem certeza que deseja excluir este formulário?')) return;
  
  const formData = new FormData();
  formData.append('id', id);
  
  fetch('/rh/formularios/excluir', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Formulário excluído!');
        carregarFormularios();
      } else {
        alert('Erro: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Erro ao excluir');
    });
}

// Submit do formulário
document.getElementById('formFormulario').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const id = document.getElementById('form_id').value;
  const titulo = document.getElementById('form_titulo').value.trim();
  const descricao = document.getElementById('form_descricao').value.trim();
  const tipo = document.getElementById('form_tipo').value;
  
  // Coletar perguntas
  const perguntas = [];
  document.querySelectorAll('#perguntasFormContainer > div').forEach((div, i) => {
    const textoEl = div.querySelector('input[id^="pf_texto_"]');
    const tipoEl = div.querySelector('select[id^="pf_tipo_"]');
    const pesoEl = div.querySelector('input[id^="pf_peso_"]');
    
    if (textoEl && textoEl.value.trim()) {
      perguntas.push({
        texto: textoEl.value.trim(),
        tipo: tipoEl ? tipoEl.value : 'texto',
        peso: pesoEl && pesoEl.value ? parseFloat(pesoEl.value) : 1.00
      });
    }
  });
  
  if (perguntas.length === 0) {
    alert('Adicione pelo menos uma pergunta!');
    return;
  }
  
  const formData = new FormData();
  if (id) formData.append('id', id);
  formData.append('titulo', titulo);
  formData.append('descricao', descricao);
  formData.append('tipo', tipo);
  formData.append('perguntas', JSON.stringify(perguntas));
  
  const url = id ? '/rh/formularios/editar' : '/rh/formularios/criar';
  
  fetch(url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        fecharModalFormulario();
        carregarFormularios();
      } else {
        alert('Erro: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Erro ao salvar formulário');
    });
});

// Copiar link do formulário
function copiarLink(token) {
  const url = window.location.origin + '/avaliacao/' + token;
  navigator.clipboard.writeText(url).then(() => {
    alert('Link copiado!\n\n' + url);
  }).catch(() => {
    prompt('Copie o link:', url);
  });
}

// Mostrar QR Code
function mostrarQRCode(token, titulo) {
  const url = window.location.origin + '/avaliacao/' + token;
  
  // Criar modal de QR Code
  const modal = document.createElement('div');
  modal.id = 'modalQRCode';
  modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
  modal.innerHTML = `
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 text-center">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-900">📱 QR Code</h3>
        <button onclick="document.getElementById('modalQRCode').remove()" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>
      <h4 class="text-lg font-medium text-gray-900 mb-4">${titulo}</h4>
      <div id="qrcode" class="flex justify-center mb-4"></div>
      <p class="text-sm text-gray-600 mb-4">Escaneie para acessar o formulário</p>
      <div class="flex gap-2">
        <button onclick="copiarLink('${token}')" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
          🔗 Copiar Link
        </button>
        <button onclick="document.getElementById('modalQRCode').remove()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
          Fechar
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  // Gerar QR Code usando API
  const qrImg = document.createElement('img');
  qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(url);
  qrImg.alt = 'QR Code';
  qrImg.className = 'mx-auto';
  document.getElementById('qrcode').appendChild(qrImg);
}

// Duplicar formulário
function duplicarFormularioRh(id) {
  if (!confirm('Deseja duplicar este formulário?')) return;
  
  const formData = new FormData();
  formData.append('id', id);
  
  fetch('/rh/formularios/duplicar', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        carregarFormularios();
      } else {
        alert('Erro: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Erro ao duplicar');
    });
}
// Carregar estatísticas do dashboard
function carregarDashboardStats() {
    const colaboradorId = document.getElementById('filtroColaboradorDashboard').value;
    const url = '/rh/dashboard/stats' + (colaboradorId ? '?colaborador_id=' + colaboradorId : '');

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const s = data.stats;
                
                // KPIs
                document.getElementById('kpiTotal').textContent = s.total;
                document.getElementById('kpiConcluidas').textContent = s.concluidas;
                document.getElementById('kpiPendentes').textContent = s.pendentes;
                document.getElementById('kpiMedia').textContent = s.media;
                
                document.getElementById('kpiTotalDesc').textContent = 'Atualizado agora';
                document.getElementById('kpiConcluidasDesc').textContent = 
                    s.total > 0 ? Math.round((s.concluidas / s.total) * 100) + '% de conclusão' : 'Sem dados';

                // Chart Departamentos (Barras)
                const chartDep = document.getElementById('chartDepartamentos');
                chartDep.innerHTML = '';
                
                if (s.departamentos.length === 0) {
                    chartDep.innerHTML = '<p class="text-gray-500 text-center py-4">Sem dados disponíveis</p>';
                } else {
                    const maxQtd = Math.max(...s.departamentos.map(d => d.qtd));
                    const colors = ['bg-blue-600', 'bg-green-600', 'bg-purple-600', 'bg-amber-600', 'bg-red-600'];
                    
                    s.departamentos.forEach((d, i) => {
                        const width = maxQtd > 0 ? (d.qtd / maxQtd) * 100 : 0;
                        const color = colors[i % colors.length];
                        
                        chartDep.innerHTML += `
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">${d.departamento}</span>
                                    <span class="font-medium">${d.qtd} avaliações</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="${color} h-2 rounded-full transition-all duration-1000" style="width: ${width}%"></div>
                                </div>
                            </div>
                        `;
                    });
                }

                // Chart Notas (Barras)
                const chartNotas = document.getElementById('chartNotas');
                chartNotas.innerHTML = '';
                
                if (s.notas.length === 0) {
                    chartNotas.innerHTML = '<p class="text-gray-500 text-center py-4">Sem dados disponíveis</p>';
                } else {
                    const maxQtd = Math.max(...s.notas.map(n => n.qtd));
                    
                    s.notas.forEach(n => {
                        const width = maxQtd > 0 ? (n.qtd / maxQtd) * 100 : 0;
                        let color = 'bg-gray-500';
                        if (n.faixa.includes('Excelente')) color = 'bg-green-500';
                        else if (n.faixa.includes('Bom')) color = 'bg-blue-500';
                        else if (n.faixa.includes('Regular')) color = 'bg-amber-500';
                        else if (n.faixa.includes('Ruim')) color = 'bg-red-500';
                        
                        chartNotas.innerHTML += `
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">${n.faixa}</span>
                                    <span class="font-medium">${n.qtd}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="${color} h-2 rounded-full transition-all duration-1000" style="width: ${width}%"></div>
                                </div>
                            </div>
                        `;
                    });
                }
            }
        })
        .catch(console.error);
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
  carregarDashboardStats();
  carregarColaboradores();
  carregarAvaliacoes();
  
  // Tabs
  window.trocarAba = function(aba) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('content-' + aba).classList.remove('hidden');
    
    document.querySelectorAll('.tab-btn').forEach(el => {
      el.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50');
      el.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    const activeBtn = document.querySelector(`button[onclick="trocarAba('${aba}')"]`);
    if(activeBtn) {
      activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
      activeBtn.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50');
    }
    
    // Carregar dados específicos da aba
    if (aba === 'formularios') {
        carregarFormularios();
    } else if (aba === 'dashboard') {
        carregarDashboardStats();
    } else if (aba === 'avaliacoes') {
        carregarAvaliacoes();
    }
  };
});
</script>
