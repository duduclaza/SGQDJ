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

<!-- Overlay do Tour -->
<div id="tourOverlay" class="fixed inset-0 z-[99999] hidden">
  <!-- Máscara escura com buraco -->
  <div id="tourMask" class="absolute inset-0 bg-black/70 transition-all duration-300"></div>
  
  <!-- Highlight do elemento atual -->
  <div id="tourHighlight" class="absolute border-4 border-blue-400 rounded-xl shadow-[0_0_0_9999px_rgba(0,0,0,0.7)] transition-all duration-300 pointer-events-none"></div>
  
  <!-- Tooltip do Tour -->
  <div id="tourTooltip" class="absolute bg-white rounded-2xl shadow-2xl p-5 max-w-sm z-[100000] transition-all duration-300">
    <div class="flex items-start gap-3 mb-3">
      <div id="tourIcon" class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <div>
        <h3 id="tourTitle" class="font-bold text-gray-900 text-lg">Título</h3>
        <p id="tourDesc" class="text-gray-600 text-sm mt-1">Descrição</p>
      </div>
    </div>
    
    <!-- Progress bar -->
    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4">
      <div id="tourProgress" class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
    </div>
    
    <!-- Botões -->
    <div class="flex items-center justify-between">
      <button onclick="pularTourNC()" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
        Pular Tutorial
      </button>
      <div class="flex gap-2">
        <button id="btnTourAnterior" onclick="tourAnterior()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors hidden">
          ← Anterior
        </button>
        <button id="btnTourProximo" onclick="tourProximo()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
          Próximo →
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Boas-vindas (primeira vez) -->
<div id="tourWelcome" class="fixed inset-0 z-[99999] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center animate-bounce-in">
    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-red-500/30">
      <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
      </svg>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900 mb-3">Bem-vindo às Não Conformidades! 🎯</h2>
    <p class="text-gray-600 mb-6">
      Este módulo ajuda você a gerenciar ocorrências e garantir a qualidade. 
      <br><br>
      Quer fazer um <strong>tour rápido</strong> para conhecer as funcionalidades?
    </p>
    
    <div class="flex gap-3 justify-center">
      <button onclick="fecharWelcome(false)" class="px-6 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
        Agora não
      </button>
      <button onclick="fecharWelcome(true)" class="px-6 py-3 text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium shadow-lg shadow-blue-500/30">
        🚀 Iniciar Tour
      </button>
    </div>
    
    <p class="text-xs text-gray-400 mt-4">
      Você pode reiniciar o tour a qualquer momento clicando no botão <strong>❓</strong> no canto inferior direito.
    </p>
  </div>
</div>

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

// Verificar se é primeira visita
document.addEventListener('DOMContentLoaded', function() {
  const tourVisto = localStorage.getItem(TOUR_KEY);
  if (!tourVisto) {
    setTimeout(() => {
      document.getElementById('tourWelcome').classList.remove('hidden');
    }, 500);
  }
});

// Fechar welcome e iniciar ou não o tour
function fecharWelcome(iniciar) {
  document.getElementById('tourWelcome').classList.add('hidden');
  localStorage.setItem(TOUR_KEY, 'true');
  
  if (iniciar) {
    setTimeout(() => iniciarTourNC(), 300);
  }
}

// Iniciar tour
function iniciarTourNC() {
  tourAtual = 0;
  document.getElementById('tourOverlay').classList.remove('hidden');
  mostrarStepTour();
}

// Mostrar step atual
function mostrarStepTour() {
  const step = tourSteps[tourAtual];
  const overlay = document.getElementById('tourOverlay');
  const highlight = document.getElementById('tourHighlight');
  const tooltip = document.getElementById('tourTooltip');
  const progress = document.getElementById('tourProgress');
  
  // Atualizar conteúdo
  document.getElementById('tourTitle').textContent = step.title;
  document.getElementById('tourDesc').textContent = step.description;
  document.getElementById('tourIcon').innerHTML = `<span class="text-2xl">${step.icon}</span>`;
  
  // Atualizar progresso
  const progressPercent = ((tourAtual + 1) / tourSteps.length) * 100;
  progress.style.width = progressPercent + '%';
  
  // Atualizar botões
  document.getElementById('btnTourAnterior').classList.toggle('hidden', tourAtual === 0);
  document.getElementById('btnTourProximo').textContent = 
    tourAtual === tourSteps.length - 1 ? '✓ Finalizar' : 'Próximo →';
  
  // Posicionar highlight e tooltip
  if (step.element) {
    const el = document.querySelector(step.element);
    if (el) {
      const rect = el.getBoundingClientRect();
      const padding = 8;
      
      highlight.style.left = (rect.left - padding) + 'px';
      highlight.style.top = (rect.top - padding) + 'px';
      highlight.style.width = (rect.width + padding * 2) + 'px';
      highlight.style.height = (rect.height + padding * 2) + 'px';
      highlight.style.opacity = '1';
      
      // Definir largura do tooltip
      const tooltipWidth = 340;
      tooltip.style.width = tooltipWidth + 'px';
      
      // Posicionar tooltip
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
      tooltip.style.transform = '';
      
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
  const tooltip = document.getElementById('tourTooltip');
  const highlight = document.getElementById('tourHighlight');
  
  highlight.style.opacity = '0';
  tooltip.style.top = '50%';
  tooltip.style.left = '50%';
  tooltip.style.transform = 'translate(-50%, -50%)';
}

// Próximo step
function tourProximo() {
  if (tourAtual < tourSteps.length - 1) {
    tourAtual++;
    document.getElementById('tourTooltip').style.transform = '';
    mostrarStepTour();
  } else {
    finalizarTour();
  }
}

// Step anterior
function tourAnterior() {
  if (tourAtual > 0) {
    tourAtual--;
    document.getElementById('tourTooltip').style.transform = '';
    mostrarStepTour();
  }
}

// Pular tour
function pularTourNC() {
  finalizarTour();
}

// Finalizar tour
function finalizarTour() {
  document.getElementById('tourOverlay').classList.add('hidden');
  localStorage.setItem(TOUR_KEY, 'true');
}

// Fechar tour ao clicar fora
document.getElementById('tourMask')?.addEventListener('click', function(e) {
  if (e.target === this) {
    pularTourNC();
  }
});
</script>
