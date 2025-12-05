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

  <!-- Gráfico: NCs por Departamento -->
  <div class="bg-white rounded-lg shadow-lg border-l-4 border-red-500">
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        🏢 Top 10 Departamentos - Não Conformidades
      </h3>
      <p class="text-sm text-gray-600 mt-1">Clique em uma barra para ver detalhes</p>
    </div>
    <div class="p-6">
      <canvas id="ncPorDepartamentoChart" width="400" height="300"></canvas>
    </div>
  </div>

</div>
<!-- FIM CONTEÚDO ABA NÃO CONFORMIDADES -->
