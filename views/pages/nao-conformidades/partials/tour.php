<?php
/**
 * Tour/Demo Interativo para módulo de Não Conformidades
 * Aparece na primeira visita e pode ser reiniciado pelo botão de ajuda
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

/* Tour Styles - fora do container */
.tour-overlay-global {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  z-index: 999999 !important;
}

.tour-tooltip-global {
  position: fixed !important;
  z-index: 9999999 !important;
}

.tour-highlight-global {
  position: fixed !important;
  z-index: 999998 !important;
}
</style>

<script>
// ===== CONFIGURAÇÃO DO TOUR =====
const tourSteps = [
  {
    element: null, // Sem elemento = intro geral
    title: "📋 Módulo de Não Conformidades",
    description: "Aqui você gerencia todas as ocorrências de qualidade da empresa. Vamos conhecer as principais funcionalidades!",
    icon: "🎯"
  },
  {
    element: "button[onclick='abrirModalNovaNC()']",
    title: "➕ Nova Ocorrência",
    description: "Clique aqui para registrar uma nova não conformidade. Você vai preencher título, descrição, responsável e anexar evidências.",
    icon: "📝"
  },
  {
    element: "#tab-pendentes",
    title: "🔴 Aba Pendentes",
    description: "Aqui ficam as NCs que acabaram de ser registradas e aguardam início do tratamento.",
    icon: "⏳"
  },
  {
    element: "#tab-em_andamento",
    title: "🟡 Aba Em Andamento",
    description: "NCs que já estão sendo tratadas. O responsável pode registrar ações corretivas aqui.",
    icon: "🔧"
  },
  {
    element: "#tab-solucionadas",
    title: "🟢 Aba Solucionadas",
    description: "Histórico de todas as NCs que foram resolvidas. Ótimo para consultas e auditorias!",
    icon: "✅"
  },
  {
    element: "#btnTourNC",
    title: "❓ Botão de Ajuda",
    description: "Sempre que precisar, clique aqui para ver este tutorial novamente. Bom trabalho! 🚀",
    icon: "💡"
  }
];

let tourAtual = 0;
const TOUR_KEY = 'nc_tour_visto';
let tourOverlayEl = null;
let tourTooltipEl = null;
let tourHighlightEl = null;
let tourWelcomeEl = null;

// Criar elementos do tour diretamente no body
function criarElementosTour() {
  // Remover elementos existentes se houver
  document.getElementById('tourOverlayGlobal')?.remove();
  document.getElementById('tourWelcomeGlobal')?.remove();
  
  // Criar overlay do tour
  tourOverlayEl = document.createElement('div');
  tourOverlayEl.id = 'tourOverlayGlobal';
  tourOverlayEl.className = 'tour-overlay-global hidden';
  tourOverlayEl.innerHTML = `
    <!-- Máscara escura -->
    <div id="tourMaskGlobal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.75);z-index:999997;"></div>
    
    <!-- Highlight do elemento atual -->
    <div id="tourHighlightGlobal" class="tour-highlight-global" style="border:4px solid #60a5fa;border-radius:12px;box-shadow:0 0 0 9999px rgba(0,0,0,0.75);transition:all 0.3s;pointer-events:none;opacity:0;"></div>
    
    <!-- Tooltip do Tour -->
    <div id="tourTooltipGlobal" class="tour-tooltip-global bg-white rounded-2xl shadow-2xl p-5" style="max-width:360px;width:360px;">
      <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
        <div id="tourIconGlobal" style="width:40px;height:40px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <span style="font-size:24px;">🎯</span>
        </div>
        <div>
          <h3 id="tourTitleGlobal" style="font-weight:700;color:#111827;font-size:18px;margin:0;">Título</h3>
          <p id="tourDescGlobal" style="color:#6b7280;font-size:14px;margin-top:4px;line-height:1.5;">Descrição</p>
        </div>
      </div>
      
      <!-- Progress bar -->
      <div style="width:100%;background:#e5e7eb;border-radius:9999px;height:6px;margin-bottom:16px;">
        <div id="tourProgressGlobal" style="background:#3b82f6;height:6px;border-radius:9999px;transition:all 0.3s;width:0%;"></div>
      </div>
      
      <!-- Botões -->
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <button onclick="pularTourNC()" style="font-size:14px;color:#6b7280;background:none;border:none;cursor:pointer;">
          Pular Tutorial
        </button>
        <div style="display:flex;gap:8px;">
          <button id="btnTourAnteriorGlobal" onclick="tourAnterior()" style="padding:8px 16px;font-size:14px;font-weight:500;color:#4b5563;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;display:none;">
            ← Anterior
          </button>
          <button id="btnTourProximoGlobal" onclick="tourProximo()" style="padding:8px 16px;font-size:14px;font-weight:500;color:white;background:#2563eb;border:none;border-radius:8px;cursor:pointer;">
            Próximo →
          </button>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(tourOverlayEl);
  
  // Criar modal de boas-vindas
  tourWelcomeEl = document.createElement('div');
  tourWelcomeEl.id = 'tourWelcomeGlobal';
  tourWelcomeEl.className = 'tour-overlay-global hidden';
  tourWelcomeEl.style.cssText = 'display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);padding:16px;';
  tourWelcomeEl.innerHTML = `
    <div class="animate-bounce-in" style="background:white;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:400px;width:100%;padding:32px;text-align:center;">
      <div style="width:80px;height:80px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:0 10px 15px -3px rgba(239,68,68,0.3);">
        <svg style="width:40px;height:40px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      
      <h2 style="font-size:24px;font-weight:700;color:#111827;margin-bottom:12px;">Bem-vindo às Não Conformidades! 🎯</h2>
      <p style="color:#6b7280;margin-bottom:24px;line-height:1.6;">
        Este módulo ajuda você a gerenciar ocorrências e garantir a qualidade. 
        <br><br>
        Quer fazer um <strong>tour rápido</strong> para conhecer as funcionalidades?
      </p>
      
      <div style="display:flex;gap:12px;justify-content:center;">
        <button onclick="fecharWelcome(false)" style="padding:12px 24px;color:#4b5563;background:#f3f4f6;border:none;border-radius:12px;cursor:pointer;font-weight:500;">
          Agora não
        </button>
        <button onclick="fecharWelcome(true)" style="padding:12px 24px;color:white;background:linear-gradient(135deg,#3b82f6,#2563eb);border:none;border-radius:12px;cursor:pointer;font-weight:500;box-shadow:0 10px 15px -3px rgba(59,130,246,0.3);">
          🚀 Iniciar Tour
        </button>
      </div>
      
      <p style="font-size:12px;color:#9ca3af;margin-top:16px;">
        Você pode reiniciar o tour a qualquer momento clicando no botão <strong>❓</strong> no canto inferior direito.
      </p>
    </div>
  `;
  document.body.appendChild(tourWelcomeEl);
  
  // Event listener para fechar ao clicar na máscara
  document.getElementById('tourMaskGlobal')?.addEventListener('click', pularTourNC);
}

// Verificar se é primeira visita
document.addEventListener('DOMContentLoaded', function() {
  criarElementosTour();
  
  const tourVisto = localStorage.getItem(TOUR_KEY);
  if (!tourVisto) {
    setTimeout(() => {
      document.getElementById('tourWelcomeGlobal').classList.remove('hidden');
      document.getElementById('tourWelcomeGlobal').style.display = 'flex';
    }, 500);
  }
});

// Fechar welcome e iniciar ou não o tour
function fecharWelcome(iniciar) {
  const welcome = document.getElementById('tourWelcomeGlobal');
  welcome.classList.add('hidden');
  welcome.style.display = 'none';
  localStorage.setItem(TOUR_KEY, 'true');
  
  if (iniciar) {
    setTimeout(() => iniciarTourNC(), 300);
  }
}

// Iniciar tour
function iniciarTourNC() {
  tourAtual = 0;
  const overlay = document.getElementById('tourOverlayGlobal');
  overlay.classList.remove('hidden');
  overlay.style.display = 'block';
  mostrarStepTour();
}

// Mostrar step atual
function mostrarStepTour() {
  const step = tourSteps[tourAtual];
  const highlight = document.getElementById('tourHighlightGlobal');
  const tooltip = document.getElementById('tourTooltipGlobal');
  const progress = document.getElementById('tourProgressGlobal');
  
  // Atualizar conteúdo
  document.getElementById('tourTitleGlobal').textContent = step.title;
  document.getElementById('tourDescGlobal').textContent = step.description;
  document.getElementById('tourIconGlobal').innerHTML = `<span style="font-size:24px;">${step.icon}</span>`;
  
  // Atualizar progresso
  const progressPercent = ((tourAtual + 1) / tourSteps.length) * 100;
  progress.style.width = progressPercent + '%';
  
  // Atualizar botões
  document.getElementById('btnTourAnteriorGlobal').style.display = tourAtual === 0 ? 'none' : 'block';
  document.getElementById('btnTourProximoGlobal').textContent = 
    tourAtual === tourSteps.length - 1 ? '✓ Finalizar' : 'Próximo →';
  
  // Posicionar highlight e tooltip
  if (step.element) {
    const el = document.querySelector(step.element);
    if (el) {
      const rect = el.getBoundingClientRect();
      const padding = 8;
      
      // Posicionar highlight
      highlight.style.left = (rect.left - padding) + 'px';
      highlight.style.top = (rect.top - padding) + 'px';
      highlight.style.width = (rect.width + padding * 2) + 'px';
      highlight.style.height = (rect.height + padding * 2) + 'px';
      highlight.style.opacity = '1';
      
      // Calcular posição do tooltip
      const tooltipWidth = 360;
      let tooltipTop = rect.bottom + 16;
      let tooltipLeft = rect.left;
      
      // Se elemento está na direita da tela, posicionar tooltip à esquerda dele
      if (rect.left > window.innerWidth / 2) {
        tooltipLeft = rect.right - tooltipWidth;
      }
      
      // Ajustar se sair da tela - vertical
      if (tooltipTop + 220 > window.innerHeight) {
        tooltipTop = rect.top - 220;
      }
      if (tooltipTop < 16) tooltipTop = 16;
      
      // Ajustar se sair da tela - horizontal
      if (tooltipLeft + tooltipWidth > window.innerWidth - 16) {
        tooltipLeft = window.innerWidth - tooltipWidth - 20;
      }
      if (tooltipLeft < 16) tooltipLeft = 16;
      
      tooltip.style.top = tooltipTop + 'px';
      tooltip.style.left = tooltipLeft + 'px';
      tooltip.style.transform = 'none';
      
      // Scroll para elemento
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
      centralizarTooltip();
    }
  } else {
    // Sem elemento, centralizar tooltip
    highlight.style.opacity = '0';
    centralizarTooltip();
  }
}

function centralizarTooltip() {
  const tooltip = document.getElementById('tourTooltipGlobal');
  const highlight = document.getElementById('tourHighlightGlobal');
  
  highlight.style.opacity = '0';
  tooltip.style.top = '50%';
  tooltip.style.left = '50%';
  tooltip.style.transform = 'translate(-50%, -50%)';
}

// Próximo step
function tourProximo() {
  if (tourAtual < tourSteps.length - 1) {
    tourAtual++;
    document.getElementById('tourTooltipGlobal').style.transform = 'none';
    mostrarStepTour();
  } else {
    finalizarTour();
  }
}

// Step anterior
function tourAnterior() {
  if (tourAtual > 0) {
    tourAtual--;
    document.getElementById('tourTooltipGlobal').style.transform = 'none';
    mostrarStepTour();
  }
}

// Pular tour
function pularTourNC() {
  finalizarTour();
}

// Finalizar tour
function finalizarTour() {
  const overlay = document.getElementById('tourOverlayGlobal');
  overlay.classList.add('hidden');
  overlay.style.display = 'none';
  localStorage.setItem(TOUR_KEY, 'true');
}
</script>

