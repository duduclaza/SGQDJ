// ===== NÃO CONFORMIDADES - DASHBOARD =====

let ncPorDepartamentoChart = null;

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
    
    const data = response.data;
    
    // Atualizar cards
    const ncPendentesEl = document.getElementById('ncPendentes');
    const ncEmAndamentoEl = document.getElementById('ncEmAndamento');
    const ncSolucionadasEl = document.getElementById('ncSolucionadas');
    
    if (ncPendentesEl) ncPendentesEl.textContent = data.cards.pendentes;
    if (ncEmAndamentoEl) ncEmAndamentoEl.textContent = data.cards.em_andamento;
    if (ncSolucionadasEl) ncSolucionadasEl.textContent = data.cards.solucionadas;
    
    // Popular select de departamentos (apenas na primeira vez)
    if (data.filtros && data.filtros.departamentos_disponiveis && data.filtros.departamentos_disponiveis.length > 0) {
      const selectDept = document.getElementById('filtroNcDepartamento');
      if (selectDept && selectDept.options.length === 1) { // Só tem a opção "Todos"
        data.filtros.departamentos_disponiveis.forEach(dept => {
          const option = document.createElement('option');
          option.value = dept;
          option.textContent = dept;
          selectDept.appendChild(option);
        });
      }
    }
    
    // Criar/Atualizar gráfico de departamentos
    const ctx = document.getElementById('ncPorDepartamentoChart');
    if (!ctx) return;
    
    if (ncPorDepartamentoChart) {
      ncPorDepartamentoChart.destroy();
    }
    
    // Se não houver dados, mostrar mensagem
    if (!data.departamentos.labels || data.departamentos.labels.length === 0) {
      ctx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum dado disponível para exibir</p>';
      return;
    }
    
    ncPorDepartamentoChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.departamentos.labels,
        datasets: [
          {
            label: 'Pendentes',
            data: data.departamentos.pendentes,
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: 'rgba(239, 68, 68, 1)',
            borderWidth: 2
          },
          {
            label: 'Em Andamento',
            data: data.departamentos.em_andamento,
            backgroundColor: 'rgba(251, 191, 36, 0.8)',
            borderColor: 'rgba(251, 191, 36, 1)',
            borderWidth: 2
          },
          {
            label: 'Solucionadas',
            data: data.departamentos.solucionadas,
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        onClick: (event, elements) => {
          if (elements.length > 0) {
            const index = elements[0].index;
            const departamento = data.departamentos.labels[index];
            abrirDetalhesNcDepartamento(departamento);
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            borderColor: 'rgba(255, 255, 255, 0.2)',
            borderWidth: 1,
            cornerRadius: 8,
            callbacks: {
              footer: function(tooltipItems) {
                const index = tooltipItems[0].dataIndex;
                const total = data.departamentos.total[index];
                return `Total: ${total} NCs`;
              }
            }
          }
        },
        scales: {
          x: {
            stacked: true,
            grid: {
              display: false
            }
          },
          y: {
            stacked: true,
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
    
  } catch (error) {
    console.error('Erro ao carregar dashboard de NCs:', error);
    alert('Erro ao carregar dashboard: ' + error.message);
  }
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

// Função para abrir modal com detalhes das NCs do departamento
function abrirDetalhesNcDepartamento(departamento) {
  // Redirecionar para a página de NCs com filtro de departamento
  window.open(`/nao-conformidades?departamento=${encodeURIComponent(departamento)}`, '_blank');
}

// Função para trocar de aba (se não existir globalmente)
if (typeof switchTab === 'undefined') {
  window.switchTab = function(tabName) {
    // Esconder todas as abas
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
      content.style.display = 'none';
    });
    
    // Remover classe active de todos os botões
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('active');
    });
    
    // Mostrar aba selecionada
    const selectedContent = document.getElementById(`content-${tabName}`);
    const selectedButton = document.getElementById(`tab-${tabName}`);
    
    if (selectedContent) {
      selectedContent.classList.add('active');
      selectedContent.style.display = 'block';
    }
    
    if (selectedButton) {
      selectedButton.classList.add('active');
    }
    
    // Carregar dados específicos da aba
    if (tabName === 'nao-conformidades') {
      loadDashboardNaoConformidades();
    } else if (tabName === 'amostragens') {
      if (typeof loadDashboardAmostragens !== 'undefined') loadDashboardAmostragens();
    } else if (tabName === 'retornados') {
      if (typeof loadDashboardData !== 'undefined') loadDashboardData();
    } else if (tabName === 'fornecedores') {
      // Carregar dados de fornecedores se necessário
    } else if (tabName === 'garantias') {
      // Carregar dados de garantias se necessário
    } else if (tabName === 'melhorias') {
      if (typeof carregarDadosMelhorias !== 'undefined') carregarDadosMelhorias();
    }
  };
}
