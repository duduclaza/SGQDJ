<?php
/**
 * Tour/Demo Interativo para módulo de Não Conformidades
 * - Spotlight sem escurecimento no elemento focado
 * - Abre o formulário para explicar os campos
 * - Posicionamento corrigido
 */
?>

<!-- Botão Flutuante de Ajuda/Tour -->
<button id="btnTourNC" onclick="iniciarTourNC()" 
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full shadow-lg hover:shadow-xl hover:scale-110 transition-all z-[9998] flex items-center justify-center group"
        title="Tutorial do Módulo">
  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>
  <span class="absolute -top-10 right-0 bg-gray-900 text-white text-xs px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
    Ver Tutorial
  </span>
</button>

<style>
@keyframes bounce-in {
  0% { opacity: 0; transform: scale(0.8); }
  50% { transform: scale(1.05); }
  100% { opacity: 1; transform: scale(1); }
}
.animate-bounce-in {
  animation: bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Pulse no botão de ajuda */
#btnTourNC::before {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 100%;
  background: inherit;
  opacity: 0.3;
  animation: pulse-ring 2s ease-out infinite;
}

@keyframes pulse-ring {
  0% { transform: scale(0.9); opacity: 0.5; }
  100% { transform: scale(1.3); opacity: 0; }
}

/* Tour Spotlight - elemento fica visível sem escurecimento */
.tour-spotlight {
  position: fixed !important;
  border: 3px solid #3b82f6 !important;
  border-radius: 12px !important;
  box-shadow: 
    0 0 0 9999px rgba(0, 0, 0, 0.8),
    0 0 30px rgba(59, 130, 246, 0.5),
    inset 0 0 0 2px rgba(255,255,255,0.3) !important;
  z-index: 999998 !important;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
  pointer-events: none !important;
  background: transparent !important;
}

.tour-spotlight::before {
  content: '';
  position: absolute;
  inset: -6px;
  border: 2px dashed rgba(59, 130, 246, 0.5);
  border-radius: 16px;
  animation: pulse-border 1.5s ease-in-out infinite;
}

@keyframes pulse-border {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; }
}

.tour-tooltip {
  position: fixed !important;
  z-index: 9999999 !important;
  background: white;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  padding: 20px;
  max-width: 380px;
  width: 380px;
  animation: tooltip-appear 0.3s ease-out;
}

@keyframes tooltip-appear {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.tour-overlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  z-index: 999990 !important;
  pointer-events: none;
}
</style>

<script>
// ===== CONFIGURAÇÃO DO TOUR COM FORMULÁRIO =====
const tourSteps = [
  {
    element: null,
    title: "📋 Módulo de Não Conformidades",
    description: "Aqui você gerencia todas as ocorrências de qualidade da empresa. Vamos conhecer as principais funcionalidades!",
    icon: "🎯",
    action: null
  },
  {
    element: "button[onclick='abrirModalNovaNC()']",
    title: "➕ Nova Ocorrência",
    description: "Este botão abre o formulário para registrar uma nova não conformidade. Vamos abrir para você ver os campos!",
    icon: "📝",
    action: null
  },
  {
    element: "#modalNovaNC",
    title: "📝 Formulário de Nova NC",
    description: "Aqui você preenche os dados da ocorrência: título, descrição detalhada, seleciona o responsável e pode anexar fotos como evidência.",
    icon: "✍️",
    action: () => {
      // Abrir o modal de nova NC
      if (typeof abrirModalNovaNC === 'function') {
        abrirModalNovaNC();
      }
    }
  },
  {
    element: "#modalNovaNC",
    title: "📎 Campos do Formulário",
    description: "• Título: Resumo da ocorrência\n• Descrição: Detalhes completos\n• Responsável: Quem vai tratar\n• Anexos: Fotos de evidência",
    icon: "📋",
    action: null
  },
  {
    element: null,
    title: "✖️ Fechando o Formulário",
    description: "Agora vamos fechar o formulário e continuar o tour pelos outros elementos.",
    icon: "🔄",
    action: () => {
      // Fechar o modal
      const modal = document.getElementById('modalNovaNC');
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
      }
    }
  },
  {
    element: "#tab-pendentes",
    title: "🔴 Aba Pendentes",
    description: "Aqui ficam as NCs que acabaram de ser registradas e aguardam início do tratamento.",
    icon: "⏳",
    action: null
  },
  {
    element: "#tab-em_andamento",
    title: "🟡 Aba Em Andamento",
    description: "NCs que já estão sendo tratadas. O responsável pode registrar ações corretivas aqui.",
    icon: "🔧",
    action: null
  },
  {
    element: "#tab-solucionadas",
    title: "🟢 Aba Solucionadas",
    description: "Histórico de todas as NCs que foram resolvidas. Ótimo para consultas e auditorias!",
    icon: "✅",
    action: null
  },
  {
    element: "#btnTourNC",
    title: "❓ Botão de Ajuda",
    description: "Sempre que precisar, clique aqui para ver este tutorial novamente. Bom trabalho! 🚀",
    icon: "💡",
    action: null
  }
];

let tourAtual = 0;
const TOUR_KEY = 'nc_tour_visto';
let spotlightEl = null;
let tooltipEl = null;

// Criar elementos do tour
function criarElementosTour() {
  // Remover existentes
  document.getElementById('tourSpotlight')?.remove();
  document.getElementById('tourTooltipBox')?.remove();
  document.getElementById('tourWelcomeModal')?.remove();
  
  // Criar spotlight (buraco na escuridão)
  spotlightEl = document.createElement('div');
  spotlightEl.id = 'tourSpotlight';
  spotlightEl.className = 'tour-spotlight';
  spotlightEl.style.display = 'none';
  document.body.appendChild(spotlightEl);
  
  // Criar tooltip
  tooltipEl = document.createElement('div');
  tooltipEl.id = 'tourTooltipBox';
  tooltipEl.className = 'tour-tooltip';
  tooltipEl.style.display = 'none';
  tooltipEl.innerHTML = `
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:16px;">
      <div id="tourIconBox" style="width:44px;height:44px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span style="font-size:24px;">🎯</span>
      </div>
      <div style="flex:1;">
        <h3 id="tourTitleBox" style="font-weight:700;color:#1f2937;font-size:17px;margin:0 0 6px 0;">Título</h3>
        <p id="tourDescBox" style="color:#6b7280;font-size:14px;margin:0;line-height:1.6;white-space:pre-line;">Descrição</p>
      </div>
    </div>
    
    <div style="width:100%;background:#e5e7eb;border-radius:999px;height:4px;margin-bottom:16px;">
      <div id="tourProgressBox" style="background:linear-gradient(90deg,#3b82f6,#2563eb);height:4px;border-radius:999px;transition:width 0.4s ease;width:0%;"></div>
    </div>
    
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <button onclick="pularTourNC()" style="font-size:13px;color:#9ca3af;background:none;border:none;cursor:pointer;padding:8px 0;">
        Pular Tutorial
      </button>
      <div style="display:flex;gap:8px;">
        <button id="btnTourPrev" onclick="tourAnterior()" style="padding:10px 18px;font-size:14px;font-weight:600;color:#4b5563;background:#f3f4f6;border:none;border-radius:10px;cursor:pointer;display:none;transition:all 0.2s;">
          ← Anterior
        </button>
        <button id="btnTourNext" onclick="tourProximo()" style="padding:10px 18px;font-size:14px;font-weight:600;color:white;background:linear-gradient(135deg,#3b82f6,#2563eb);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(59,130,246,0.3);transition:all 0.2s;">
          Próximo →
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(tooltipEl);
  
  // Modal de boas-vindas
  const welcomeEl = document.createElement('div');
  welcomeEl.id = 'tourWelcomeModal';
  welcomeEl.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:9999999;display:none;align-items:center;justify-content:center;padding:20px;';
  welcomeEl.innerHTML = `
    <div class="animate-bounce-in" style="background:white;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:420px;width:100%;padding:36px;text-align:center;">
      <div style="width:80px;height:80px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:0 10px 25px -5px rgba(239,68,68,0.4);">
        <svg style="width:40px;height:40px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      
      <h2 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:12px;">Bem-vindo às Não Conformidades! 🎯</h2>
      <p style="color:#6b7280;margin-bottom:28px;line-height:1.7;font-size:15px;">
        Este módulo ajuda você a gerenciar ocorrências e garantir a qualidade. 
        <br><br>
        Quer fazer um <strong>tour interativo</strong> para conhecer as funcionalidades? Vamos até abrir o formulário para você ver!
      </p>
      
      <div style="display:flex;gap:12px;justify-content:center;">
        <button onclick="fecharWelcome(false)" style="padding:14px 28px;color:#4b5563;background:#f3f4f6;border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;transition:all 0.2s;">
          Agora não
        </button>
        <button onclick="fecharWelcome(true)" style="padding:14px 28px;color:white;background:linear-gradient(135deg,#3b82f6,#2563eb);border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;box-shadow:0 8px 20px -4px rgba(59,130,246,0.4);transition:all 0.2s;">
          🚀 Iniciar Tour
        </button>
      </div>
      
      <p style="font-size:12px;color:#9ca3af;margin-top:20px;">
        Você pode reiniciar o tour clicando no botão <strong style="color:#3b82f6;">❓</strong> no canto inferior direito.
      </p>
    </div>
  `;
  document.body.appendChild(welcomeEl);
}

// Verificar primeira visita
document.addEventListener('DOMContentLoaded', function() {
  criarElementosTour();
  
  const tourVisto = localStorage.getItem(TOUR_KEY);
  if (!tourVisto) {
    setTimeout(() => {
      document.getElementById('tourWelcomeModal').style.display = 'flex';
    }, 600);
  }
});

function fecharWelcome(iniciar) {
  document.getElementById('tourWelcomeModal').style.display = 'none';
  localStorage.setItem(TOUR_KEY, 'true');
  
  if (iniciar) {
    setTimeout(() => iniciarTourNC(), 300);
  }
}

function iniciarTourNC() {
  tourAtual = 0;
  spotlightEl.style.display = 'block';
  tooltipEl.style.display = 'block';
  mostrarStepTour();
}

function mostrarStepTour() {
  const step = tourSteps[tourAtual];
  
  // Executar ação do step (como abrir modal)
  if (step.action) {
    step.action();
    // Aguardar animação do modal
    setTimeout(() => posicionarElementos(step), 350);
  } else {
    posicionarElementos(step);
  }
}

function posicionarElementos(step) {
  // Atualizar conteúdo
  document.getElementById('tourTitleBox').textContent = step.title;
  document.getElementById('tourDescBox').textContent = step.description;
  document.getElementById('tourIconBox').innerHTML = `<span style="font-size:24px;">${step.icon}</span>`;
  
  // Atualizar progresso
  const progressPercent = ((tourAtual + 1) / tourSteps.length) * 100;
  document.getElementById('tourProgressBox').style.width = progressPercent + '%';
  
  // Atualizar botões
  document.getElementById('btnTourPrev').style.display = tourAtual === 0 ? 'none' : 'inline-block';
  document.getElementById('btnTourNext').textContent = 
    tourAtual === tourSteps.length - 1 ? '✓ Finalizar' : 'Próximo →';
  
  // Posicionar spotlight e tooltip
  if (step.element) {
    const el = document.querySelector(step.element);
    if (el) {
      const rect = el.getBoundingClientRect();
      const padding = 12;
      
      // Posicionar spotlight
      spotlightEl.style.display = 'block';
      spotlightEl.style.left = (rect.left - padding + window.scrollX) + 'px';
      spotlightEl.style.top = (rect.top - padding + window.scrollY) + 'px';
      spotlightEl.style.width = (rect.width + padding * 2) + 'px';
      spotlightEl.style.height = (rect.height + padding * 2) + 'px';
      
      // Posicionar tooltip
      posicionarTooltip(rect);
      
      // Scroll suave
      if (rect.top < 100 || rect.bottom > window.innerHeight - 100) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    } else {
      centralizarTooltip();
    }
  } else {
    spotlightEl.style.display = 'none';
    centralizarTooltip();
  }
}

function posicionarTooltip(rect) {
  const tooltipWidth = 380;
  const tooltipHeight = 220;
  const margin = 20;
  
  let top, left;
  
  // Tentar posicionar embaixo
  if (rect.bottom + tooltipHeight + margin < window.innerHeight) {
    top = rect.bottom + margin;
    left = rect.left;
  } 
  // Tentar posicionar em cima
  else if (rect.top - tooltipHeight - margin > 0) {
    top = rect.top - tooltipHeight - margin;
    left = rect.left;
  }
  // Posicionar ao lado
  else {
    top = Math.max(margin, rect.top);
    if (rect.right + tooltipWidth + margin < window.innerWidth) {
      left = rect.right + margin;
    } else {
      left = rect.left - tooltipWidth - margin;
    }
  }
  
  // Ajustar horizontalmente
  if (left + tooltipWidth > window.innerWidth - margin) {
    left = window.innerWidth - tooltipWidth - margin;
  }
  if (left < margin) left = margin;
  
  // Ajustar verticalmente
  if (top + tooltipHeight > window.innerHeight - margin) {
    top = window.innerHeight - tooltipHeight - margin;
  }
  if (top < margin) top = margin;
  
  tooltipEl.style.top = top + 'px';
  tooltipEl.style.left = left + 'px';
  tooltipEl.style.transform = 'none';
}

function centralizarTooltip() {
  spotlightEl.style.display = 'none';
  tooltipEl.style.top = '50%';
  tooltipEl.style.left = '50%';
  tooltipEl.style.transform = 'translate(-50%, -50%)';
}

function tourProximo() {
  if (tourAtual < tourSteps.length - 1) {
    tourAtual++;
    mostrarStepTour();
  } else {
    finalizarTour();
  }
}

function tourAnterior() {
  if (tourAtual > 0) {
    tourAtual--;
    mostrarStepTour();
  }
}

function pularTourNC() {
  finalizarTour();
}

function finalizarTour() {
  // Fechar modal de NC se estiver aberto
  const modal = document.getElementById('modalNovaNC');
  if (modal) {
    modal.classList.add('hidden');
    modal.style.display = 'none';
  }
  
  spotlightEl.style.display = 'none';
  tooltipEl.style.display = 'none';
  localStorage.setItem(TOUR_KEY, 'true');
}
</script>
