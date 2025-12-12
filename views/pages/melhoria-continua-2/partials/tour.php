<?php
/**
 * Tour/Demo Interativo para módulo de Melhoria Contínua 2.0
 * - Overlay escuro sempre visível durante o tour
 * - Spotlight no elemento focado
 * - Abre o formulário durante o tour
 */
?>

<!-- Botão Flutuante de Ajuda/Tour -->
<button id="btnTourMC" onclick="iniciarTourMC()" 
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full shadow-lg hover:shadow-xl hover:scale-110 transition-all z-[9998] flex items-center justify-center group"
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
#btnTourMC::before {
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
#tourDarkOverlayMC {
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
#tourSpotlightMC {
  position: fixed !important;
  border: 3px solid #10b981 !important;
  border-radius: 12px !important;
  box-shadow: 
    0 0 0 9999px rgba(0, 0, 0, 0.8),
    0 0 30px rgba(16, 185, 129, 0.6) !important;
  z-index: 999995 !important;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
  pointer-events: none !important;
  background: transparent !important;
}

#tourSpotlightMC::before {
  content: '';
  position: absolute;
  inset: -8px;
  border: 2px dashed rgba(16, 185, 129, 0.6);
  border-radius: 16px;
  animation: pulse-border 1.5s ease-in-out infinite;
}

@keyframes pulse-border {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.02); }
}

/* Tooltip do tour */
#tourTooltipBoxMC {
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
// ===== CONFIGURAÇÃO DO TOUR MELHORIA CONTÍNUA =====
const tourStepsMC = [
  {
    element: null,
    title: "🚀 Módulo Melhoria Contínua 2.0",
    description: "Bem-vindo ao módulo de Melhoria Contínua! Aqui você pode registrar ideias de melhoria, acompanhar seu status e contribuir para a evolução da empresa. Vamos conhecer as funcionalidades!",
    icon: "🎯",
    action: null
  },
  {
    element: "button[onclick='openMelhoriaModal()']",
    title: "➕ Nova Melhoria",
    description: "Clique neste botão para registrar uma nova ideia de melhoria. Qualquer colaborador pode sugerir melhorias para processos, produtos ou ambiente de trabalho!",
    icon: "💡",
    action: null
  },
  {
    element: "#melhoriaFormContainer",
    title: "📝 Formulário de Nova Melhoria",
    description: "Este é o formulário para cadastrar sua ideia. Você vai preencher título, descrição, resultado esperado e o plano de ação seguindo a metodologia 5W2H.",
    icon: "✍️",
    action: () => {
      if (typeof openMelhoriaModal === 'function') {
        openMelhoriaModal();
      }
    }
  },
  {
    element: "#melhoriaFormContainer",
    title: "📋 Metodologia 5W2H",
    description: "O formulário usa a metodologia 5W2H: O que, Como, Onde, Por que, Quando e Quanto custa. Isso ajuda a estruturar bem a sua ideia e facilita a análise pelos gestores.",
    icon: "📊",
    action: null
  },
  {
    element: null,
    title: "✖️ Fechando o Formulário",
    description: "Vamos fechar o formulário e conhecer os outros recursos do módulo.",
    icon: "🔄",
    action: () => {
      if (typeof closeMelhoriaModal === 'function') {
        closeMelhoriaModal();
      }
    }
  },
  {
    element: "form[method='GET'][action='/melhoria-continua-2']",
    title: "🔍 Filtros Avançados",
    description: "Use os filtros para encontrar melhorias específicas. Você pode filtrar por departamento, status, idealizador e período.",
    icon: "🎛️",
    action: null
  },
  {
    element: "#melhoriaTable",
    title: "📊 Tabela de Melhorias",
    description: "Aqui você visualiza todas as melhorias cadastradas. As colunas mostram: Data do registro, Departamento, Título, Descrição, Resultado Esperado, Status atual, Idealizador da ideia, quem Criou o registro, Responsáveis pela execução, Data Prevista e as Ações disponíveis.",
    icon: "📋",
    action: null
  },
  {
    element: "#btnTourMC",
    title: "❓ Botão de Ajuda",
    description: "Sempre que precisar, clique aqui para ver este tutorial novamente. Continue sugerindo melhorias e contribuindo para a evolução contínua! 🚀",
    icon: "💡",
    action: null
  }
];

let tourAtualMC = 0;
const TOUR_KEY_MC = 'mc_tour_visto';
let darkOverlayMC = null;
let spotlightElMC = null;
let tooltipElMC = null;

// ===== CONFIGURAÇÕES DE NARRAÇÃO =====
let narracaoAtivaMC = true;
let audioAtualMC = null;
const NARRACAO_RATE_MC = 1.0;

// Caminho dos áudios pré-gravados
const AUDIO_BASE_PATH_MC = '/audio/tour-mc';

// Verificar suporte a Web Speech API (usado apenas como fallback)
const speechSupportedMC = 'speechSynthesis' in window;

// Função para narrar usando arquivos de áudio pré-gravados
function narrarMC(stepNumber) {
  if (!narracaoAtivaMC) return;
  
  // Parar narração anterior
  pararNarracaoMC();
  
  // Caminho do arquivo de áudio para este passo
  const audioPath = `${AUDIO_BASE_PATH_MC}/step-${stepNumber}.mp3`;
  
  // Tentar reproduzir o áudio pré-gravado
  audioAtualMC = new Audio(audioPath);
  
  audioAtualMC.onerror = () => {
    console.warn(`Áudio não encontrado: ${audioPath}, usando fallback`);
    // Fallback: usar texto do passo atual com Web Speech API
    const step = tourStepsMC[tourAtualMC];
    if (step) {
      narrarFallbackMC(step.title + '. ' + step.description);
    }
  };
  
  audioAtualMC.play().catch(err => {
    console.warn('Erro ao reproduzir áudio:', err);
  });
}

// Fallback usando Web Speech API (quando áudio não existe)
function narrarFallbackMC(texto) {
  if (!speechSupportedMC) return;
  
  // Limpar emojis
  const textoLimpo = texto
    .replace(/[🚀➕📝✍️📋📊✖️🔄🔍🎛️❓🎯💡💡]/g, '')
    .replace(/•/g, ', ')
    .replace(/\n/g, '. ')
    .trim();
  
  const utterance = new SpeechSynthesisUtterance(textoLimpo);
  utterance.lang = 'pt-BR';
  utterance.rate = NARRACAO_RATE_MC;
  
  const vozes = speechSynthesis.getVoices();
  const vozPtBr = vozes.find(v => v.lang.includes('pt-BR'));
  if (vozPtBr) utterance.voice = vozPtBr;
  
  speechSynthesis.speak(utterance);
}

// Função para parar narração
function pararNarracaoMC() {
  if (audioAtualMC) {
    audioAtualMC.pause();
    audioAtualMC.currentTime = 0;
    audioAtualMC = null;
  }
  if (speechSupportedMC) {
    speechSynthesis.cancel();
  }
}

// Alternar narração (mute/unmute)
function toggleNarracaoMC() {
  narracaoAtivaMC = !narracaoAtivaMC;
  
  const btnMute = document.getElementById('btnTourMuteMC');
  if (btnMute) {
    btnMute.innerHTML = narracaoAtivaMC ? '🔊' : '🔇';
    btnMute.title = narracaoAtivaMC ? 'Desativar narração' : 'Ativar narração';
  }
  
  if (!narracaoAtivaMC) {
    pararNarracaoMC();
  } else {
    narrarMC(tourAtualMC + 1);
  }
}

// Carregar vozes (necessário para alguns navegadores) - apenas para fallback
if (speechSupportedMC) {
  speechSynthesis.onvoiceschanged = () => {
    speechSynthesis.getVoices();
  };
}

// Criar elementos do tour
function criarElementosTourMC() {
  // Remover existentes
  document.getElementById('tourDarkOverlayMC')?.remove();
  document.getElementById('tourSpotlightMC')?.remove();
  document.getElementById('tourTooltipBoxMC')?.remove();
  document.getElementById('tourWelcomeModalMC')?.remove();
  
  // Overlay escuro
  darkOverlayMC = document.createElement('div');
  darkOverlayMC.id = 'tourDarkOverlayMC';
  darkOverlayMC.style.display = 'none';
  darkOverlayMC.onclick = (e) => {
    if (e.target === darkOverlayMC) pularTourMC();
  };
  document.body.appendChild(darkOverlayMC);
  
  // Spotlight
  spotlightElMC = document.createElement('div');
  spotlightElMC.id = 'tourSpotlightMC';
  spotlightElMC.style.display = 'none';
  document.body.appendChild(spotlightElMC);
  
  // Tooltip
  tooltipElMC = document.createElement('div');
  tooltipElMC.id = 'tourTooltipBoxMC';
  tooltipElMC.style.display = 'none';
  tooltipElMC.innerHTML = `
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:16px;">
      <div id="tourIconBoxMC" style="width:44px;height:44px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span style="font-size:24px;">🎯</span>
      </div>
      <div style="flex:1;">
        <h3 id="tourTitleBoxMC" style="font-weight:700;color:#1f2937;font-size:17px;margin:0 0 6px 0;">Título</h3>
        <p id="tourDescBoxMC" style="color:#6b7280;font-size:14px;margin:0;line-height:1.6;white-space:pre-line;">Descrição</p>
      </div>
    </div>
    
    <div style="width:100%;background:#e5e7eb;border-radius:999px;height:4px;margin-bottom:16px;">
      <div id="tourProgressBoxMC" style="background:linear-gradient(90deg,#10b981,#059669);height:4px;border-radius:999px;transition:width 0.4s ease;width:0%;"></div>
    </div>
    
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <button onclick="pularTourMC()" style="font-size:13px;color:#9ca3af;background:none;border:none;cursor:pointer;padding:8px 0;">
        Pular Tutorial
      </button>
      <div style="display:flex;gap:8px;align-items:center;">
        <button id="btnTourMuteMC" onclick="toggleNarracaoMC()" style="padding:8px;font-size:16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;transition:all 0.2s;" title="Desativar narração">
          🔊
        </button>
        <button id="btnTourPrevMC" onclick="tourAnteriorMC()" style="padding:10px 18px;font-size:14px;font-weight:600;color:#4b5563;background:#f3f4f6;border:none;border-radius:10px;cursor:pointer;display:none;transition:all 0.2s;">
          ← Anterior
        </button>
        <button id="btnTourNextMC" onclick="tourProximoMC()" style="padding:10px 18px;font-size:14px;font-weight:600;color:white;background:linear-gradient(135deg,#10b981,#059669);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,0.3);transition:all 0.2s;">
          Próximo →
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(tooltipElMC);
  
  // Modal de boas-vindas
  const welcomeEl = document.createElement('div');
  welcomeEl.id = 'tourWelcomeModalMC';
  welcomeEl.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.75);backdrop-filter:blur(4px);z-index:9999999;display:none;align-items:center;justify-content:center;padding:20px;';
  welcomeEl.innerHTML = `
    <div class="animate-bounce-in" style="background:white;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:420px;width:100%;padding:36px;text-align:center;">
      <div style="width:80px;height:80px;background:linear-gradient(135deg,#10b981,#059669);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:0 10px 25px -5px rgba(16,185,129,0.4);">
        <svg style="width:40px;height:40px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
      </div>
      
      <h2 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:12px;">Bem-vindo à Melhoria Contínua! 🚀</h2>
      <p style="color:#6b7280;margin-bottom:28px;line-height:1.7;font-size:15px;">
        Este módulo permite que você sugira melhorias e acompanhe a evolução das ideias. 
        <br><br>
        Quer fazer um <strong>tour interativo</strong>? Vou te mostrar como usar!
      </p>
      
      <div style="display:flex;gap:12px;justify-content:center;">
        <button onclick="fecharWelcomeMC(false)" style="padding:14px 28px;color:#4b5563;background:#f3f4f6;border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;">
          Agora não
        </button>
        <button onclick="fecharWelcomeMC(true)" style="padding:14px 28px;color:white;background:linear-gradient(135deg,#10b981,#059669);border:none;border-radius:12px;cursor:pointer;font-weight:600;font-size:15px;box-shadow:0 8px 20px -4px rgba(16,185,129,0.4);">
          🚀 Iniciar Tour
        </button>
      </div>
      
      <p style="font-size:12px;color:#9ca3af;margin-top:20px;">
        Clique no botão <strong style="color:#10b981;">❓</strong> para reiniciar o tour.
      </p>
    </div>
  `;
  document.body.appendChild(welcomeEl);
}

// Verificar primeira visita
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(() => {
    criarElementosTourMC();
    
    const tourVisto = localStorage.getItem(TOUR_KEY_MC);
    if (!tourVisto) {
      setTimeout(() => {
        document.getElementById('tourWelcomeModalMC').style.display = 'flex';
      }, 300);
    }
  }, 100);
});

function fecharWelcomeMC(iniciar) {
  document.getElementById('tourWelcomeModalMC').style.display = 'none';
  localStorage.setItem(TOUR_KEY_MC, 'true');
  
  if (iniciar) {
    setTimeout(() => iniciarTourMC(), 200);
  }
}

function iniciarTourMC() {
  tourAtualMC = 0;
  window.scrollTo({ top: 0, behavior: 'smooth' });
  
  setTimeout(() => {
    darkOverlayMC.style.display = 'block';
    tooltipElMC.style.display = 'block';
    
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        mostrarStepTourMC();
      });
    });
  }, 400);
}

function mostrarStepTourMC() {
  const step = tourStepsMC[tourAtualMC];
  
  if (step.action) {
    step.action();
  }
  
  setTimeout(() => {
    atualizarConteudoMC(step);
    posicionarElementosMC(step);
  }, step.action ? 400 : 50);
}

function atualizarConteudoMC(step) {
  document.getElementById('tourTitleBoxMC').textContent = step.title;
  document.getElementById('tourDescBoxMC').textContent = step.description;
  document.getElementById('tourIconBoxMC').innerHTML = `<span style="font-size:24px;">${step.icon}</span>`;
  
  const progressPercent = ((tourAtualMC + 1) / tourStepsMC.length) * 100;
  document.getElementById('tourProgressBoxMC').style.width = progressPercent + '%';
  
  document.getElementById('btnTourPrevMC').style.display = tourAtualMC === 0 ? 'none' : 'inline-block';
  document.getElementById('btnTourNextMC').textContent = 
    tourAtualMC === tourStepsMC.length - 1 ? '✓ Finalizar' : 'Próximo →';
  
  narrarMC(tourAtualMC + 1);
}

function posicionarElementosMC(step) {
  if (step.element) {
    const el = document.querySelector(step.element);
    if (el) {
      el.offsetHeight;
      
      const rect = el.getBoundingClientRect();
      const padding = 12;
      
      spotlightElMC.style.display = 'block';
      spotlightElMC.style.left = (rect.left - padding) + 'px';
      spotlightElMC.style.top = (rect.top - padding) + 'px';
      spotlightElMC.style.width = (rect.width + padding * 2) + 'px';
      spotlightElMC.style.height = (rect.height + padding * 2) + 'px';
      
      posicionarTooltipMC(rect);
      
      if (rect.top < 50 || rect.bottom > window.innerHeight - 50) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
          const newRect = el.getBoundingClientRect();
          spotlightElMC.style.left = (newRect.left - padding) + 'px';
          spotlightElMC.style.top = (newRect.top - padding) + 'px';
          posicionarTooltipMC(newRect);
        }, 400);
      }
    } else {
      esconderSpotlightMC();
      centralizarTooltipMC();
    }
  } else {
    esconderSpotlightMC();
    centralizarTooltipMC();
  }
}

function posicionarTooltipMC(rect) {
  const tooltipWidth = 380;
  const tooltipHeight = tooltipElMC.offsetHeight || 200;
  const margin = 20;
  
  let top, left;
  
  if (rect.bottom + tooltipHeight + margin < window.innerHeight) {
    top = rect.bottom + margin;
    left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
  } 
  else if (rect.top - tooltipHeight - margin > 0) {
    top = rect.top - tooltipHeight - margin;
    left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
  }
  else if (rect.right + tooltipWidth + margin < window.innerWidth) {
    top = rect.top + (rect.height / 2) - (tooltipHeight / 2);
    left = rect.right + margin;
  }
  else {
    top = rect.top + (rect.height / 2) - (tooltipHeight / 2);
    left = rect.left - tooltipWidth - margin;
  }
  
  if (left + tooltipWidth > window.innerWidth - margin) {
    left = window.innerWidth - tooltipWidth - margin;
  }
  if (left < margin) left = margin;
  
  if (top + tooltipHeight > window.innerHeight - margin) {
    top = window.innerHeight - tooltipHeight - margin;
  }
  if (top < margin) top = margin;
  
  tooltipElMC.style.top = top + 'px';
  tooltipElMC.style.left = left + 'px';
  tooltipElMC.style.transform = 'none';
}

function esconderSpotlightMC() {
  spotlightElMC.style.display = 'none';
}

function centralizarTooltipMC() {
  spotlightElMC.style.display = 'none';
  tooltipElMC.style.top = '50%';
  tooltipElMC.style.left = '50%';
  tooltipElMC.style.transform = 'translate(-50%, -50%)';
}

function tourProximoMC() {
  if (tourAtualMC < tourStepsMC.length - 1) {
    tourAtualMC++;
    mostrarStepTourMC();
  } else {
    finalizarTourMC();
  }
}

function tourAnteriorMC() {
  if (tourAtualMC > 0) {
    tourAtualMC--;
    mostrarStepTourMC();
  }
}

function pularTourMC() {
  finalizarTourMC();
}

function finalizarTourMC() {
  pararNarracaoMC();
  
  // Fechar formulário se aberto
  if (typeof closeMelhoriaModal === 'function') {
    closeMelhoriaModal();
  }
  
  darkOverlayMC.style.display = 'none';
  spotlightElMC.style.display = 'none';
  tooltipElMC.style.display = 'none';
  
  localStorage.setItem(TOUR_KEY_MC, 'true');
}

// Atualizar posição ao redimensionar a janela
window.addEventListener('resize', () => {
  if (darkOverlayMC && darkOverlayMC.style.display === 'block') {
    const step = tourStepsMC[tourAtualMC];
    if (step && step.element) {
      setTimeout(() => posicionarElementosMC(step), 100);
    }
  }
});
</script>
