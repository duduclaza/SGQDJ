<!-- CONTEÚDO ABA NÃO CONFORMIDADES -->
<div id="content-nao-conformidades" class="tab-content space-y-6">
  
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
