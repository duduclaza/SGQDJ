<?php
/**
 * Módulo: Usabilidade do SGQ
 * Acesso: Exclusivo para Super Admin
 * 
 * Funcionalidades:
 * - Gráfico de logins por dia
 * - Histórico detalhado de acessos
 * - Estatísticas de uso do sistema
 */
?>

<style>
/* ========================================
   USABILIDADE DO SGQ - ESTILOS MODERNOS
   ======================================== */

.usabilidade-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 1.5rem;
}

/* Header Premium */
.usabilidade-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3d7ab5 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.usabilidade-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.usabilidade-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.usabilidade-header p {
    opacity: 0.9;
    font-size: 1rem;
}

.badge-super-admin {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1e3a5f;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Cards de Estatísticas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.stat-card .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-card .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-card .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card .stat-icon.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.stat-card .stat-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.stat-card .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-card .stat-label {
    font-size: 0.875rem;
    color: #6b7280;
}

/* Gráfico Container */
.chart-container {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.chart-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-filters {
    display: flex;
    gap: 0.5rem;
}

.chart-filters button {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid #e5e7eb;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.chart-filters button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.chart-filters button.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

#loginsChart {
    width: 100%;
    height: 350px;
}

/* Tabela de Histórico */
.history-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.history-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.history-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.history-filters {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.history-filters input {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    min-width: 180px;
}

.history-filters input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.history-table-wrapper {
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th {
    background: #f9fafb;
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e5e7eb;
}

.history-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
    color: #374151;
}

.history-table tr:hover td {
    background: #f9fafb;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-details .user-name {
    font-weight: 500;
    color: #111827;
}

.user-details .user-email {
    font-size: 0.75rem;
    color: #6b7280;
}

.ip-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    border-radius: 6px;
    font-size: 0.75rem;
    font-family: monospace;
    color: #4b5563;
}

/* Paginação */
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
}

.pagination-controls button {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.875rem;
}

.pagination-controls button:hover:not(:disabled) {
    background: #f9fafb;
    border-color: #d1d5db;
}

.pagination-controls button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-controls button.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

/* Top Users */
.top-users-container {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.top-users-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.top-user-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.top-user-item:last-child {
    border-bottom: none;
}

.top-user-rank {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
    margin-right: 0.75rem;
}

.top-user-rank.gold { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; }
.top-user-rank.silver { background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%); color: white; }
.top-user-rank.bronze { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: white; }
.top-user-rank.default { background: #f3f4f6; color: #6b7280; }

.top-user-info {
    flex: 1;
}

.top-user-info .name {
    font-weight: 500;
    color: #111827;
    font-size: 0.875rem;
}

.top-user-info .email {
    font-size: 0.75rem;
    color: #6b7280;
}

.top-user-count {
    font-weight: 600;
    color: #2563eb;
    font-size: 0.875rem;
}

/* Loading States */
.skeleton {
    background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Responsivo */
@media (max-width: 1024px) {
    .usabilidade-container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .usabilidade-header {
        padding: 1.5rem;
    }
    
    .usabilidade-header h1 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .chart-header, .history-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="usabilidade-container">
    <!-- Header Premium -->
    <div class="usabilidade-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3v18h18"></path>
                <path d="m19 9-5 5-4-4-3 3"></path>
            </svg>
            Usabilidade do SGQ
            <span class="badge-super-admin">Super Admin</span>
        </h1>
        <p>Monitore o uso do sistema, logins por dia e histórico de acessos.</p>
    </div>
    
    <!-- Cards de Estatísticas -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-value" id="statLoginsHoje">-</div>
            <div class="stat-label">Logins Hoje</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </div>
            <div class="stat-value" id="statLogins7dias">-</div>
            <div class="stat-label">Logins (7 dias)</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="stat-value" id="statLogins30dias">-</div>
            <div class="stat-label">Logins (30 dias)</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-value" id="statUsuariosUnicos">-</div>
            <div class="stat-label">Usuários Únicos (30d)</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon pink">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <div class="stat-value" id="statTotalLogins">-</div>
            <div class="stat-label">Total de Logins</div>
        </div>
    </div>
    
    <!-- Layout em Grid: Gráfico + Top Usuários -->
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Gráfico de Logins -->
        <div class="chart-container">
            <div class="chart-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Logins por Dia
                </h2>
                <div class="chart-filters">
                    <button onclick="carregarGrafico(7)" data-dias="7">7 dias</button>
                    <button onclick="carregarGrafico(14)" data-dias="14">14 dias</button>
                    <button onclick="carregarGrafico(30)" data-dias="30" class="active">30 dias</button>
                    <button onclick="carregarGrafico(60)" data-dias="60">60 dias</button>
                </div>
            </div>
            <canvas id="loginsChart"></canvas>
        </div>
        
        <!-- Top Usuários -->
        <div class="top-users-container">
            <div class="top-users-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    Usuários Mais Ativos (30d)
                </h3>
            </div>
            <div id="topUsersContainer">
                <!-- Carregado via JS -->
            </div>
        </div>
    </div>
    
    <!-- Histórico de Logins -->
    <div class="history-container">
        <div class="history-header">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Histórico de Acessos
            </h2>
            <div class="history-filters">
                <input type="text" id="filtroUsuario" placeholder="🔍 Buscar usuário..." oninput="filtrarHistorico()">
                <input type="date" id="filtroData" onchange="filtrarHistorico()">
            </div>
        </div>
        
        <div class="history-table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Data/Hora</th>
                        <th>Endereço IP</th>
                        <th>Navegador</th>
                    </tr>
                </thead>
                <tbody id="historicoTableBody">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 3rem;">
                            <div class="skeleton" style="width: 200px; height: 20px; margin: 0 auto;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <div class="pagination-info" id="paginationInfo">
                Carregando...
            </div>
            <div class="pagination-controls" id="paginationControls">
                <!-- Gerado via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ================================
// USABILIDADE DO SGQ - JAVASCRIPT
// ================================

let loginsChart = null;
let paginaAtual = 1;
const limitePorPagina = 50;
let debounceTimer = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    carregarEstatisticas();
    carregarGrafico(30);
    carregarHistorico(1);
});

// Carregar Estatísticas
async function carregarEstatisticas() {
    try {
        const response = await fetch('/usabilidade/api/estatisticas');
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            document.getElementById('statLoginsHoje').textContent = data.logins_hoje.toLocaleString('pt-BR');
            document.getElementById('statLogins7dias').textContent = data.logins_7_dias.toLocaleString('pt-BR');
            document.getElementById('statLogins30dias').textContent = data.logins_30_dias.toLocaleString('pt-BR');
            document.getElementById('statUsuariosUnicos').textContent = data.usuarios_unicos_30_dias.toLocaleString('pt-BR');
            document.getElementById('statTotalLogins').textContent = data.total_logins.toLocaleString('pt-BR');
            
            // Renderizar Top Usuários
            renderizarTopUsuarios(data.usuarios_mais_ativos);
        }
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error);
    }
}

// Renderizar Top Usuários
function renderizarTopUsuarios(usuarios) {
    const container = document.getElementById('topUsersContainer');
    
    if (!usuarios || usuarios.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; padding: 2rem;">Nenhum dado disponível</p>';
        return;
    }
    
    let html = '';
    usuarios.forEach((usuario, index) => {
        const rankClass = index === 0 ? 'gold' : (index === 1 ? 'silver' : (index === 2 ? 'bronze' : 'default'));
        const inicial = usuario.user_name.charAt(0).toUpperCase();
        
        html += `
            <div class="top-user-item">
                <div style="display: flex; align-items: center;">
                    <div class="top-user-rank ${rankClass}">${index + 1}</div>
                    <div class="top-user-info">
                        <div class="name">${escapeHtml(usuario.user_name)}</div>
                        <div class="email">${escapeHtml(usuario.user_email)}</div>
                    </div>
                </div>
                <div class="top-user-count">${usuario.total_logins} logins</div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Carregar Gráfico
async function carregarGrafico(dias) {
    // Atualizar botões ativos
    document.querySelectorAll('.chart-filters button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.dias == dias);
    });
    
    try {
        const response = await fetch(`/usabilidade/api/logins-por-dia?dias=${dias}`);
        const result = await response.json();
        
        if (result.success) {
            renderizarGrafico(result.data);
        }
    } catch (error) {
        console.error('Erro ao carregar gráfico:', error);
    }
}

// Renderizar Gráfico
function renderizarGrafico(dados) {
    const ctx = document.getElementById('loginsChart').getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (loginsChart) {
        loginsChart.destroy();
    }
    
    const labels = dados.map(d => d.data_formatada);
    const totalLogins = dados.map(d => d.total_logins);
    const usuariosUnicos = dados.map(d => d.usuarios_unicos);
    
    loginsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total de Logins',
                    data: totalLogins,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#3b82f6'
                },
                {
                    label: 'Usuários Únicos',
                    data: usuariosUnicos,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return ` ${context.dataset.label}: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

// Carregar Histórico
async function carregarHistorico(pagina) {
    paginaAtual = pagina;
    
    const filtroUsuario = document.getElementById('filtroUsuario').value;
    const filtroData = document.getElementById('filtroData').value;
    
    let url = `/usabilidade/api/historico?page=${pagina}&limit=${limitePorPagina}`;
    if (filtroUsuario) url += `&usuario=${encodeURIComponent(filtroUsuario)}`;
    if (filtroData) url += `&data=${filtroData}`;
    
    try {
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            renderizarHistorico(result.data);
            renderizarPaginacao(result.pagination);
        }
    } catch (error) {
        console.error('Erro ao carregar histórico:', error);
    }
}

// Renderizar Histórico
function renderizarHistorico(logs) {
    const tbody = document.getElementById('historicoTableBody');
    
    if (!logs || logs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" style="text-align: center; padding: 3rem; color: #6b7280;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; display: block; opacity: 0.5;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 15h8"></path>
                        <path d="M9 9h.01"></path>
                        <path d="M15 9h.01"></path>
                    </svg>
                    Nenhum registro encontrado
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    logs.forEach(log => {
        const inicial = log.user_name.charAt(0).toUpperCase();
        const navegador = parseUserAgent(log.user_agent);
        
        html += `
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${inicial}</div>
                        <div class="user-details">
                            <div class="user-name">${escapeHtml(log.user_name)}</div>
                            <div class="user-email">${escapeHtml(log.user_email)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-weight: 500;">${log.login_formatado}</span>
                </td>
                <td>
                    <span class="ip-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        ${escapeHtml(log.ip_address || 'N/A')}
                    </span>
                </td>
                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(log.user_agent || '')}">
                    ${navegador}
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Parse User Agent para nome amigável
function parseUserAgent(ua) {
    if (!ua) return 'Desconhecido';
    
    if (ua.includes('Chrome') && !ua.includes('Edg')) return '🌐 Chrome';
    if (ua.includes('Firefox')) return '🦊 Firefox';
    if (ua.includes('Safari') && !ua.includes('Chrome')) return '🧭 Safari';
    if (ua.includes('Edg')) return '🌀 Edge';
    if (ua.includes('Opera') || ua.includes('OPR')) return '🔴 Opera';
    
    return '🌐 Navegador';
}

// Renderizar Paginação
function renderizarPaginacao(pagination) {
    const info = document.getElementById('paginationInfo');
    const controls = document.getElementById('paginationControls');
    
    const inicio = ((pagination.page - 1) * pagination.limit) + 1;
    const fim = Math.min(pagination.page * pagination.limit, pagination.total);
    
    info.textContent = `Mostrando ${inicio} a ${fim} de ${pagination.total} registros`;
    
    let html = '';
    
    // Botão Anterior
    html += `<button onclick="carregarHistorico(${pagination.page - 1})" ${pagination.page <= 1 ? 'disabled' : ''}>← Anterior</button>`;
    
    // Números das páginas
    const maxBotoes = 5;
    let inicio_pag = Math.max(1, pagination.page - Math.floor(maxBotoes / 2));
    let fim_pag = Math.min(pagination.total_pages, inicio_pag + maxBotoes - 1);
    
    if (fim_pag - inicio_pag < maxBotoes - 1) {
        inicio_pag = Math.max(1, fim_pag - maxBotoes + 1);
    }
    
    for (let i = inicio_pag; i <= fim_pag; i++) {
        html += `<button onclick="carregarHistorico(${i})" class="${i === pagination.page ? 'active' : ''}">${i}</button>`;
    }
    
    // Botão Próximo
    html += `<button onclick="carregarHistorico(${pagination.page + 1})" ${pagination.page >= pagination.total_pages ? 'disabled' : ''}>Próximo →</button>`;
    
    controls.innerHTML = html;
}

// Filtrar Histórico com debounce
function filtrarHistorico() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        carregarHistorico(1);
    }, 300);
}

// Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
