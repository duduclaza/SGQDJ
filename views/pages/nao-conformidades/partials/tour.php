<?php
/**
 * Tour/Demo Interativo para módulo de Não Conformidades
 * - Overlay escuro sempre visível durante o tour
 * - Spotlight no elemento focado
 * - Abre o formulário durante o tour
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

/* Overlay escuro do tour - SEMPRE visível durante o tour */
#tourDarkOverlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  background: rgba(0, 0, 0, 0.75) !important;
  z-index: 999990 !important;
  pointer-events: auto;
}

/* Spotlight - buraco no escuro */
#tourSpotlight {
  position: fixed !important;
  border: 3px solid #3b82f6 !important;
  border-radius: 12px !important;
  box-shadow: 
    0 0 0 9999px rgba(0, 0, 0, 0.8),
    0 0 30px rgba(59, 130, 246, 0.6) !important;
  z-index: 999995 !important;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
  pointer-events: none !important;
  background: transparent !important;
}

#tourSpotlight::before {
  content: '';
  position: absolute;
  inset: -8px;
  border: 2px dashed rgba(59, 130, 246, 0.6);
  border-radius: 16px;
  animation: pulse-border 1.5s ease-in-out infinite;
}

@keyframes pulse-border {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.02); }
}

/* Tooltip do tour */
#tourTooltipBox {
  position: fixed !important;
  z-index: 9999999 !important;
  background: white;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
  padding: 20px;
  max-width: 380px;
  width: 380px;
}

@keyframes tooltip-appear {
  from { opacity: 0; transform: translateY(10px) scale(0.98); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<script>
// ===== CONFIGURAÇÃO DO TOUR =====
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
      if (typeof abrirModalNovaNC === 'function') {
        abrirModalNovaNC();
      }
    }
  },
  {
    element: "#modalNovaNC",
    title: "📎 Campos Importantes",
    description: "• Título: Resumo claro da ocorrência\n• Descrição: Detalhes completos\n• Responsável: Quem vai resolver\n• Anexos: Fotos de evidência",
    icon: "📋",
    action: null
  },
  {
    element: null,
    title: "✖️ Fechando o Formulário",
    description: "Vamos fechar o formulário e continuar conhecendo as abas de status.",
    icon: "🔄",
    action: () => {
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
    description: "NCs recém registradas que aguardam início do tratamento.",
    icon: "⏳",
    action: null
  },
  {
    element: "#tab-em_andamento",
    title: "🟡 Aba Em Andamento",
    description: "NCs que estão sendo tratadas. O responsável registra ações corretivas.",
    icon: "🔧",
    action: null
  },
  {
    element: "#tab-solucionadas",
    title: "🟢 Aba Solucionadas",
    description: "Histórico de NCs resolvidas. Ótimo para consultas e auditorias!",
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
let darkOverlay = null;
let spotlightEl = null;
let tooltipEl = null;

// Criar elementos do tour
function criarElementosTour() {
  // Remover existentes
  document.getElementById('tourDarkOverlay')?.remove();
  document.getElementById('tourSpotlight')?.remove();
  document.getElementById('tourTooltipBox')?.remove();
  document.getElementById('tourWelcomeModal')?.remove();
  
  // Overlay escuro (sempre visível durante o tour)
  darkOverlay = document.createElement('div');
  darkOverlay.id = 'tourDarkOverlay';
  darkOverlay.style.display = 'none';
  darkOverlay.onclick = (e) => {
    if (e.target === darkOverlay) pularTourNC();
  };
  document.body.appendChild(darkOverlay);
  
  // Spotlight
  spotlightEl = document.createElement('div');
  spotlightEl.id = 'tourSpotlight';
  spotlightEl.style.display = 'none';
  document.body.appendChild(spotlightEl);
  
  // Tooltip
  tooltipEl = document.createElement('div');
  tooltipEl.id = 'tourTooltipBox';
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
  welcomeEl.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.75);backdrop-filter:blur(4px);z-index:9999999;display:none;align-items:center;justify-content:center;padding:20px;';
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
        Quer fazer um <strong>tour interativo</strong>? Vamos até abrir o formulário para você ver!
      </p>
      
      <div style="display:flex;gap:12px;justify-content:center;">
        <button onclick="fecharWelcome(false)" style="padding:14px 28px;color:#4b5563;background:#f3f4f6;border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;">
          Agora não
        </button>
        <button onclick="fecharWelcome(true)" style="padding:14px 28px;color:white;background:linear-gradient(135deg,#3b82f6,#2563eb);border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;box-shadow:0 8px 20px -4px rgba(59,130,246,0.4);">
          🚀 Iniciar Tour
        </button>
      </div>
      
      <p style="font-size:12px;color:#9ca3af;margin-top:20px;">
        Clique no botão <strong style="color:#3b82f6;">❓</strong> para reiniciar o tour.
      </p>
    </div>
  `;
  document.body.appendChild(welcomeEl);
}

// Verificar primeira visita
document.addEventListener('DOMContentLoaded', function() {
  // Aguardar layout carregar completamente
  setTimeout(() => {
    criarElementosTour();
    
    const tourVisto = localStorage.getItem(TOUR_KEY);
    if (!tourVisto) {
      setTimeout(() => {
        document.getElementById('tourWelcomeModal').style.display = 'flex';
      }, 300);
    }
  }, 100);
});

function fecharWelcome(iniciar) {
  document.getElementById('tourWelcomeModal').style.display = 'none';
  localStorage.setItem(TOUR_KEY, 'true');
  
  if (iniciar) {
    setTimeout(() => iniciarTourNC(), 200);
  }
}

function iniciarTourNC() {
  tourAtual = 0;
  
  // Mostrar overlay escuro
  darkOverlay.style.display = 'block';
  tooltipEl.style.display = 'block';
  
  // Pequeno delay para garantir que elementos estão no DOM
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      mostrarStepTour();
    });
  });
}

function mostrarStepTour() {
  const step = tourSteps[tourAtual];
  
  // Executar ação primeiro (como abrir modal)
  if (step.action) {
    step.action();
  }
  
  // Aguardar animações e layout estabilizar
  setTimeout(() => {
    atualizarConteudo(step);
    posicionarElementos(step);
  }, step.action ? 400 : 50);
}

function atualizarConteudo(step) {
  document.getElementById('tourTitleBox').textContent = step.title;
  document.getElementById('tourDescBox').textContent = step.description;
  document.getElementById('tourIconBox').innerHTML = `<span style="font-size:24px;">${step.icon}</span>`;
  
  // Progresso
  const progressPercent = ((tourAtual + 1) / tourSteps.length) * 100;
  document.getElementById('tourProgressBox').style.width = progressPercent + '%';
  
  // Botões
  document.getElementById('btnTourPrev').style.display = tourAtual === 0 ? 'none' : 'inline-block';
  document.getElementById('btnTourNext').textContent = 
    tourAtual === tourSteps.length - 1 ? '✓ Finalizar' : 'Próximo →';
}

function posicionarElementos(step) {
  if (step.element) {
    const el = document.querySelector(step.element);
    if (el) {
      // Forçar recálculo de layout
      el.offsetHeight;
      
      const rect = el.getBoundingClientRect();
      const padding = 12;
      
      // Posicionar spotlight - usar coordenadas de viewport diretamente
      spotlightEl.style.display = 'block';
      spotlightEl.style.left = (rect.left - padding) + 'px';
      spotlightEl.style.top = (rect.top - padding) + 'px';
      spotlightEl.style.width = (rect.width + padding * 2) + 'px';
      spotlightEl.style.height = (rect.height + padding * 2) + 'px';
      
      // Posicionar tooltip
      posicionarTooltip(rect);
      
      // Scroll se necessário
      if (rect.top < 50 || rect.bottom > window.innerHeight - 50) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Recalcular posição após scroll
        setTimeout(() => {
          const newRect = el.getBoundingClientRect();
          spotlightEl.style.left = (newRect.left - padding) + 'px';
          spotlightEl.style.top = (newRect.top - padding) + 'px';
          posicionarTooltip(newRect);
        }, 400);
      }
    } else {
      esconderSpotlight();
      centralizarTooltip();
    }
  } else {
    esconderSpotlight();
    centralizarTooltip();
  }
}

function posicionarTooltip(rect) {
  const tooltipWidth = 380;
  const tooltipHeight = tooltipEl.offsetHeight || 200;
  const margin = 20;
  
  let top, left;
  
  // Tentar embaixo primeiro
  if (rect.bottom + tooltipHeight + margin < window.innerHeight) {
    top = rect.bottom + margin;
    left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
  } 
  // Tentar em cima
  else if (rect.top - tooltipHeight - margin > 0) {
    top = rect.top - tooltipHeight - margin;
    left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
  }
  // Posicionar ao lado direito
  else if (rect.right + tooltipWidth + margin < window.innerWidth) {
    top = rect.top + (rect.height / 2) - (tooltipHeight / 2);
    left = rect.right + margin;
  }
  // Posicionar ao lado esquerdo
  else {
    top = rect.top + (rect.height / 2) - (tooltipHeight / 2);
    left = rect.left - tooltipWidth - margin;
  }
  
  // Ajustar limites da tela - horizontal
  if (left + tooltipWidth > window.innerWidth - margin) {
    left = window.innerWidth - tooltipWidth - margin;
  }
  if (left < margin) left = margin;
  
  // Ajustar limites da tela - vertical
  if (top + tooltipHeight > window.innerHeight - margin) {
    top = window.innerHeight - tooltipHeight - margin;
  }
  if (top < margin) top = margin;
  
  tooltipEl.style.top = top + 'px';
  tooltipEl.style.left = left + 'px';
  tooltipEl.style.transform = 'none';
}

function esconderSpotlight() {
  spotlightEl.style.display = 'none';
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
  // Fechar modal se aberto
  const modal = document.getElementById('modalNovaNC');
  if (modal) {
    modal.classList.add('hidden');
    modal.style.display = 'none';
  }
  
  // Esconder todos os elementos do tour
  darkOverlay.style.display = 'none';
  spotlightEl.style.display = 'none';
  tooltipEl.style.display = 'none';
  
  localStorage.setItem(TOUR_KEY, 'true');
}

// Atualizar posição ao redimensionar a janela
window.addEventListener('resize', () => {
  if (darkOverlay && darkOverlay.style.display === 'block') {
    const step = tourSteps[tourAtual];
    if (step && step.element) {
      setTimeout(() => posicionarElementos(step), 100);
    }
  }
});
</script>
