// ===== NÃO CONFORMIDADES - DASHBOARD =====

let ncPendentesChart = null;
let ncEmAndamentoChart = null;
let ncSolucionadasChart = null;
let ncDashboardData = null; // Armazena os dados para uso no modal

// Carregar dados de Não Conformidades
async function loadDashboardNaoConformidades() {
  try {
    // Pegar valores dos filtros
    const departamento = document.getElementById('filtroNcDepartamento')?.value || '';
    const status = document.getElementById('filtroNcStatus')?.value || '';
    const dataInicial = document.getElementById('filtroNcDataInicial')?.value || '';
    const dataFinal = document.getElementById('filtroNcDataFinal')?.value || '';
    
    // Montar URL com parâmetros
    const params = new URLSearchParams();
    if (departamento) params.append('departamento', departamento);
    if (status) params.append('status', status);
    if (dataInicial) params.append('data_inicial', dataInicial);
    if (dataFinal) params.append('data_final', dataFinal);
    
    const url = `/admin/dashboard/nao-conformidades-data${params.toString() ? '?' + params.toString() : ''}`;
    
    const res = await fetch(url);
    const response = await res.json();
    
    if (!response.success) {
      console.error('Erro ao carregar dados de NCs:', response.message);
      alert('Erro ao carregar dados: ' + response.message);
      return;
    }
    
    ncDashboardData = response.data; // Armazenar para uso no modal
    
    // Atualizar cards
    const ncPendentesEl = document.getElementById('ncPendentes');
    const ncEmAndamentoEl = document.getElementById('ncEmAndamento');
    const ncSolucionadasEl = document.getElementById('ncSolucionadas');
    
    if (ncPendentesEl) ncPendentesEl.textContent = ncDashboardData.cards.pendentes;
    if (ncEmAndamentoEl) ncEmAndamentoEl.textContent = ncDashboardData.cards.em_andamento;
    if (ncSolucionadasEl) ncSolucionadasEl.textContent = ncDashboardData.cards.solucionadas;
    
    // Popular select de departamentos (apenas na primeira vez)
    if (ncDashboardData.filtros && ncDashboardData.filtros.departamentos_disponiveis && ncDashboardData.filtros.departamentos_disponiveis.length > 0) {
      const selectDept = document.getElementById('filtroNcDepartamento');
      if (selectDept && selectDept.options.length === 1) {
        ncDashboardData.filtros.departamentos_disponiveis.forEach(dept => {
          const option = document.createElement('option');
          option.value = dept;
          option.textContent = dept;
          selectDept.appendChild(option);
        });
      }
    }
    
    // Criar os 3 gráficos
    criarGraficoNcPendentes();
    criarGraficoNcEmAndamento();
    criarGraficoNcSolucionadas();
    
  } catch (error) {
    console.error('Erro ao carregar dashboard de NCs:', error);
    alert('Erro ao carregar dashboard: ' + error.message);
  }
}

// Gráfico 1: NCs Pendentes por Departamento
function criarGraficoNcPendentes() {
  const ctx = document.getElementById('ncPendentesPorDepartamentoChart');
  if (!ctx) return;
  
  if (ncPendentesChart) ncPendentesChart.destroy();
  
  if (!ncDashboardData.departamentos.labels || ncDashboardData.departamentos.labels.length === 0) {
    ctx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum dado disponível</p>';
    return;
  }
  
  ncPendentesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ncDashboardData.departamentos.labels,
      datasets: [{
        label: 'Pendentes',
        data: ncDashboardData.departamentos.pendentes,
        backgroundColor: 'rgba(239, 68, 68, 0.8)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      onClick: (event, elements) => {
        if (elements.length > 0) {
          const index = elements[0].index;
          const departamento = ncDashboardData.departamentos.labels[index];
          abrirModalNcDepartamento(departamento, 'pendente');
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          callbacks: {
            label: (context) => `${context.parsed.y} NC(s) Pendente(s)`
          }
        }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
}

// Gráfico 2: NCs Em Andamento por Departamento
function criarGraficoNcEmAndamento() {
  const ctx = document.getElementById('ncEmAndamentoPorDepartamentoChart');
  if (!ctx) return;
  
  if (ncEmAndamentoChart) ncEmAndamentoChart.destroy();
  
  if (!ncDashboardData.departamentos.labels || ncDashboardData.departamentos.labels.length === 0) {
    ctx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum dado disponível</p>';
    return;
  }
  
  ncEmAndamentoChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ncDashboardData.departamentos.labels,
      datasets: [{
        label: 'Em Andamento',
        data: ncDashboardData.departamentos.em_andamento,
        backgroundColor: 'rgba(251, 191, 36, 0.8)',
        borderColor: 'rgba(251, 191, 36, 1)',
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      onClick: (event, elements) => {
        if (elements.length > 0) {
          const index = elements[0].index;
          const departamento = ncDashboardData.departamentos.labels[index];
          abrirModalNcDepartamento(departamento, 'em_andamento');
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          callbacks: {
            label: (context) => `${context.parsed.y} NC(s) Em Andamento`
          }
        }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
}

// Gráfico 3: NCs Solucionadas por Departamento
function criarGraficoNcSolucionadas() {
  const ctx = document.getElementById('ncSolucionadasPorDepartamentoChart');
  if (!ctx) return;
  
  if (ncSolucionadasChart) ncSolucionadasChart.destroy();
  
  if (!ncDashboardData.departamentos.labels || ncDashboardData.departamentos.labels.length === 0) {
    ctx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum dado disponível</p>';
    return;
  }
  
  ncSolucionadasChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ncDashboardData.departamentos.labels,
      datasets: [{
        label: 'Solucionadas',
        data: ncDashboardData.departamentos.solucionadas,
        backgroundColor: 'rgba(34, 197, 94, 0.8)',
        borderColor: 'rgba(34, 197, 94, 1)',
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      onClick: (event, elements) => {
        if (elements.length > 0) {
          const index = elements[0].index;
          const departamento = ncDashboardData.departamentos.labels[index];
          abrirModalNcDepartamento(departamento, 'solucionada');
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          callbacks: {
            label: (context) => `${context.parsed.y} NC(s) Solucionada(s)`
          }
        }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
}

// Abrir modal com NCs do departamento
async function abrirModalNcDepartamento(departamento, status) {
  const modal = document.getElementById('modalDetalhesNcDepartamento');
  const modalContent = document.getElementById('modalDetalhesNcContent');
  
  // Mostrar modal
  modal.classList.remove('hidden');
  setTimeout(() => {
    modalContent.classList.remove('scale-95', 'opacity-0');
    modalContent.classList.add('scale-100', 'opacity-100');
  }, 10);
  
  // Atualizar título
  document.getElementById('modalNcDepartamento').textContent = departamento;
  const statusLabel = status === 'pendente' ? '⏳ Pendentes' : status === 'em_andamento' ? '🔄 Em Andamento' : '✅ Solucionadas';
  document.getElementById('modalNcStatus').textContent = statusLabel;
  
  // Mostrar loading
  document.getElementById('modalNcLoading').classList.remove('hidden');
  document.getElementById('modalNcResumo').classList.add('hidden');
  document.getElementById('modalNcTabela').classList.add('hidden');
  document.getElementById('modalNcErro').classList.add('hidden');
  
  try {
    // Buscar NCs do departamento
    const res = await fetch(`/nao-conformidades/por-departamento?departamento=${encodeURIComponent(departamento)}&status=${status}`);
    const data = await res.json();
    
    if (data.success) {
      preencherModalNcDepartamento(data.ncs, data.resumo);
    } else {
      throw new Error(data.message || 'Erro ao carregar NCs');
    }
  } catch (error) {
    document.getElementById('modalNcLoading').classList.add('hidden');
    document.getElementById('modalNcErro').classList.remove('hidden');
    document.getElementById('modalNcErroMensagem').textContent = error.message;
  }
}

// Preencher modal com dados
function preencherModalNcDepartamento(ncs, resumo) {
  document.getElementById('modalNcLoading').classList.add('hidden');
  document.getElementById('modalNcResumo').classList.remove('hidden');
  document.getElementById('modalNcTabela').classList.remove('hidden');
  
  // Atualizar resumo
  document.getElementById('modalNcPendentes').textContent = resumo.pendentes || 0;
  document.getElementById('modalNcEmAndamento').textContent = resumo.em_andamento || 0;
  document.getElementById('modalNcSolucionadas').textContent = resumo.solucionadas || 0;
  
  // Preencher tabela
  const tbody = document.getElementById('modalNcTabelaBody');
  tbody.innerHTML = '';
  
  if (ncs.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma NC encontrada</td></tr>';
    return;
  }
  
  ncs.forEach(nc => {
    const statusBadge = nc.status === 'pendente' 
      ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">⏳ Pendente</span>'
      : nc.status === 'em_andamento'
        ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">🔄 Em Andamento</span>'
        : '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">✅ Solucionada</span>';
    
    const row = `
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 text-sm font-medium text-gray-900">#${nc.id}</td>
        <td class="px-4 py-3 text-sm text-gray-900">${nc.titulo}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${nc.responsavel_nome || '-'}</td>
        <td class="px-4 py-3 text-sm">${statusBadge}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${new Date(nc.created_at).toLocaleDateString('pt-BR')}</td>
        <td class="px-4 py-3 text-center">
          <a href="/nao-conformidades" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver Detalhes</a>
        </td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
}

// Fechar modal
function fecharModalNcDepartamento() {
  const modal = document.getElementById('modalDetalhesNcDepartamento');
  const modalContent = document.getElementById('modalDetalhesNcContent');
  
  modalContent.classList.remove('scale-100', 'opacity-100');
  modalContent.classList.add('scale-95', 'opacity-0');
  
  setTimeout(() => {
    modal.classList.add('hidden');
  }, 300);
}

// Aplicar filtros
function aplicarFiltrosNC() {
  loadDashboardNaoConformidades();
}

// Limpar filtros
function limparFiltrosNC() {
  document.getElementById('filtroNcDepartamento').value = '';
  document.getElementById('filtroNcStatus').value = '';
  document.getElementById('filtroNcDataInicial').value = '';
  document.getElementById('filtroNcDataFinal').value = '';
  loadDashboardNaoConformidades();
}

// Função para trocar de aba (se não existir globalmente)
if (typeof switchTab === 'undefined') {
  window.switchTab = function(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
      content.style.display = 'none';
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('active');
    });
    
    const selectedContent = document.getElementById(`content-${tabName}`);
    const selectedButton = document.getElementById(`tab-${tabName}`);
    
    if (selectedContent) {
      selectedContent.classList.add('active');
      selectedContent.style.display = 'block';
    }
    
    if (selectedButton) {
      selectedButton.classList.add('active');
    }
    
    if (tabName === 'nao-conformidades') {
      loadDashboardNaoConformidades();
    } else if (tabName === 'amostragens') {
      if (typeof loadDashboardAmostragens !== 'undefined') loadDashboardAmostragens();
    } else if (tabName === 'retornados') {
      if (typeof loadDashboardData !== 'undefined') loadDashboardData();
    } else if (tabName === 'melhorias') {
      if (typeof carregarDadosMelhorias !== 'undefined') carregarDadosMelhorias();
    }
  };
}
