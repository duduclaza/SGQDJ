<!-- CONTEÚDO ABA NÃO CONFORMIDADES -->
<div id="content-nao-conformidades" class="tab-content space-y-6">
  
  <!-- Filtros -->
  <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
      <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
      </svg>
      🔍 Filtros de Análise
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">🏢 Departamento</label>
        <select id="filtroNcDepartamento" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
          <option value="">Todos os Departamentos</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">📊 Status</label>
        <select id="filtroNcStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
          <option value="">Todos os Status</option>
          <option value="pendente">⏳ Pendente</option>
          <option value="em_andamento">🔄 Em Andamento</option>
          <option value="solucionada">✅ Solucionada</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">📅 Data Inicial</label>
        <input type="date" id="filtroNcDataInicial" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">📅 Data Final</label>
        <input type="date" id="filtroNcDataFinal" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
      </div>
    </div>
    <div class="mt-4 flex space-x-3">
      <button onclick="aplicarFiltrosNC()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <span>Aplicar Filtros</span>
      </button>
      <button onclick="limparFiltrosNC()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span>Limpar</span>
      </button>
    </div>
  </div>
  
  <!-- Cards de Status -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- Card: Pendentes -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <h3 class="text-sm font-medium text-white text-opacity-90 mb-2">NCs Pendentes</h3>
      <div class="flex items-end justify-between">
        <p id="ncPendentes" class="text-4xl font-bold">0</p>
      </div>
      <div class="mt-4 pt-4 border-t border-white border-opacity-20">
        <p class="text-xs text-white text-opacity-80">⏳ Aguardando início</p>
      </div>
    </div>

    <!-- Card: Em Andamento -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <h3 class="text-sm font-medium text-white text-opacity-90 mb-2">NCs Em Andamento</h3>
      <div class="flex items-end justify-between">
        <p id="ncEmAndamento" class="text-4xl font-bold">0</p>
      </div>
      <div class="mt-4 pt-4 border-t border-white border-opacity-20">
        <p class="text-xs text-white text-opacity-80">🔄 Em tratamento</p>
      </div>
    </div>

    <!-- Card: Solucionadas -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <h3 class="text-sm font-medium text-white text-opacity-90 mb-2">NCs Solucionadas</h3>
      <div class="flex items-end justify-between">
        <p id="ncSolucionadas" class="text-4xl font-bold">0</p>
      </div>
      <div class="mt-4 pt-4 border-t border-white border-opacity-20">
        <p class="text-xs text-white text-opacity-80">✅ Concluídas</p>
      </div>
    </div>

  </div>

  <!-- Gráficos: 3 gráficos separados por status -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Gráfico 1: NCs Pendentes por Departamento -->
    <div class="bg-white rounded-lg shadow-lg border-l-4 border-red-500">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
          <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          ⏳ NCs Pendentes
        </h3>
        <p class="text-sm text-gray-600 mt-1">Clique em uma barra para ver detalhes</p>
      </div>
      <div class="p-6">
        <canvas id="ncPendentesPorDepartamentoChart" width="400" height="300"></canvas>
      </div>
    </div>

    <!-- Gráfico 2: NCs Em Andamento por Departamento -->
    <div class="bg-white rounded-lg shadow-lg border-l-4 border-yellow-500">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
          <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          🔄 NCs Em Andamento
        </h3>
        <p class="text-sm text-gray-600 mt-1">Clique em uma barra para ver detalhes</p>
      </div>
      <div class="p-6">
        <canvas id="ncEmAndamentoPorDepartamentoChart" width="400" height="300"></canvas>
      </div>
    </div>

    <!-- Gráfico 3: NCs Solucionadas por Departamento -->
    <div class="bg-white rounded-lg shadow-lg border-l-4 border-green-500">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
          <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          ✅ NCs Solucionadas
        </h3>
        <p class="text-sm text-gray-600 mt-1">Clique em uma barra para ver detalhes</p>
      </div>
      <div class="p-6">
        <canvas id="ncSolucionadasPorDepartamentoChart" width="400" height="300"></canvas>
      </div>
    </div>

  </div>

</div>
<!-- FIM CONTEÚDO ABA NÃO CONFORMIDADES -->

<!-- Modal: Detalhes de NCs por Departamento -->
<div id="modalDetalhesNcDepartamento" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300" style="z-index: 99999;">
  <div class="bg-white rounded-2xl shadow-2xl w-[95vw] h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalDetalhesNcContent">
    <!-- Cabeçalho -->
    <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          <span>NCs do Departamento: <span id="modalNcDepartamento" class="font-extrabold">-</span></span>
          <span class="ml-2 text-sm font-normal">(<span id="modalNcStatus">-</span>)</span>
        </h3>
        <button onclick="fecharModalNcDepartamento()" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Conteúdo -->
    <div class="h-[calc(95vh-80px)] overflow-y-auto p-6">
      <!-- Loading -->
      <div id="modalNcLoading" class="flex flex-col items-center justify-center py-16">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-red-600 mb-4"></div>
        <p class="text-gray-600">Carregando NCs...</p>
      </div>

      <!-- Cards de Resumo -->
      <div id="modalNcResumo" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500">
          <p class="text-sm text-red-600 font-medium">⏳ Pendentes</p>
          <p id="modalNcPendentes" class="text-2xl font-bold text-red-700">0</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
          <p class="text-sm text-yellow-600 font-medium">🔄 Em Andamento</p>
          <p id="modalNcEmAndamento" class="text-2xl font-bold text-yellow-700">0</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
          <p class="text-sm text-green-600 font-medium">✅ Solucionadas</p>
          <p id="modalNcSolucionadas" class="text-2xl font-bold text-green-700">0</p>
        </div>
      </div>

      <!-- Tabela de NCs -->
      <div id="modalNcTabela" class="hidden bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
              </tr>
            </thead>
            <tbody id="modalNcTabelaBody" class="bg-white divide-y divide-gray-200">
              <!-- Preenchido via JS -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Erro -->
      <div id="modalNcErro" class="hidden text-center py-16">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
        <p class="text-gray-600 font-medium">Erro ao carregar NCs</p>
        <p id="modalNcErroMensagem" class="text-sm text-gray-500 mt-2"></p>
      </div>
    </div>
  </div>
</div>

