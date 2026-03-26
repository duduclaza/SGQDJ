<?php
// Forçar cabeçalhos de não-cache no PHP (mais forte que HTML meta tags)
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$title = $title ?? 'SGQ OTI - DJ';
$viewFile = $viewFile ?? __DIR__ . '/../pages/home.php';
$sidebar = __DIR__ . '/../partials/sidebar.php';
// Versão dinâmica para evitar cache (time() força atualização a cada reload)
// Em produção, isso pode ser alterado para uma string fixa para performance
$assetVersion = time();
// Safe helper fallbacks in case global helpers are not loaded
if (!function_exists('e')) {
  function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('flash')) {
  function flash($key) { return null; }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Meta tags de cache (reforço) -->
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <link rel="icon" href="data:,">
  <title><?= e($title) ?></title>
  <script>if(window.console){const o=console.warn;console.warn=(...a)=>{if(a[0]&&String(a[0]).includes('cdn.tail'))return;o.apply(console,a)}}</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script>
    // Configurar tema inicial
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
  <link rel="stylesheet" href="/src/Support/modal-styles.css?v=<?= urlencode($assetVersion) ?>">
  <script src="/src/Support/modal-utils.js?v=<?= urlencode($assetVersion) ?>"></script>
  <script>
    // ===== TOGGLE SUBMENU - GLOBAL FUNCTION =====
    // Definir PRIMEIRO, antes de qualquer outra coisa
    window.toggleSubmenu = function(button) {
      // console.log('toggleSubmenu global chamada!', button);
      const submenu = button.parentElement.querySelector('.submenu');
      const arrow = button.querySelector('.submenu-arrow');
      if (submenu && arrow) {
        submenu.classList.toggle('hidden');
        arrow.style.transform = submenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        // console.log('Submenu toggled - hidden:', submenu.classList.contains('hidden'));
      } else {
        // console.error('ERRO: Submenu ou arrow não encontrado!', {submenu, arrow, parent: button.parentElement});
      }
    }
    // console.log('[LAYOUT] toggleSubmenu definida:', typeof window.toggleSubmenu);
    
    // User permissions for frontend
    window.userPermissions = <?= json_encode($_SESSION['user_permissions'] ?? []) ?>;
  </script>
  <script>
    // Tailwind config with dark theme
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81'
            },
          }
        }
      }
    }
  </script>
  <style>
    /* Page transition styles */
    .page-transition {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s ease-in-out;
    }
    .page-transition.loaded {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }
    
    /* Loading overlay removido - causava problemas globais */
  </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
  <div class="flex h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-300">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header/Navbar -->
      <header class="bg-white dark:bg-slate-800 shadow-sm border-b border-gray-200 dark:border-slate-700/50 transition-colors duration-300">
        <div class="flex items-center justify-between px-6 py-3">
          <!-- Espaço vazio à esquerda -->
          <div></div>
          
          <div class="flex items-center gap-4">
            <!-- Ícones do canto superior direito removidos a pedido do usuário -->
          </div>
        </div>
      </header>
      
      <!-- Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-slate-900 p-6 transition-colors duration-300">
        <!-- Aviso de migração de email removido - Resend API ativo -->
        
        <?php if ($msg = flash('success')): ?>
          <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
          <div class="mb-4 rounded-md border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>
        <div class="page-transition">
          <?php include $viewFile; ?>
        </div>
      </main>
    </div>
  </div>

  <!-- Container para modais globais -->
  <div id="global-modals-container"></div>

  <!-- ===== GLOBAL TOAST SYSTEM ===== -->
  <div id="global-toast-stack" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2 pointer-events-none" style="max-width:380px;"></div>

  <!-- ===== GLOBAL CONFIRM MODAL ===== -->
  <div id="global-confirm-overlay" class="hidden fixed inset-0 z-[99998] flex items-center justify-center p-4" style="background:rgba(15,23,42,0.65);backdrop-filter:blur(4px);">
    <div id="global-confirm-box" class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-700 w-full max-w-sm p-6 text-center transform transition-all scale-95 opacity-0" style="transition:transform .2s ease,opacity .2s ease;">
      <div id="global-confirm-icon" class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100 dark:bg-red-900/30">
        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
      </div>
      <h3 id="global-confirm-title" class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirmar ação</h3>
      <p id="global-confirm-msg" class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tem certeza que deseja continuar?</p>
      <div class="flex gap-3 justify-center">
        <button id="global-confirm-cancel" class="flex-1 px-4 py-2.5 text-sm font-bold bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition-all">
          Cancelar
        </button>
        <button id="global-confirm-ok" class="flex-1 px-4 py-2.5 text-sm font-bold bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all shadow-lg hover:shadow-red-500/20">
          Confirmar
        </button>
      </div>
    </div>
  </div>

  <style>
    /* Toast animations */
    @keyframes toastIn {
      from { opacity:0; transform:translateX(100%); }
      to   { opacity:1; transform:translateX(0); }
    }
    @keyframes toastOut {
      from { opacity:1; transform:translateX(0); }
      to   { opacity:0; transform:translateX(110%); }
    }
    .toast-item { animation: toastIn .3s cubic-bezier(.16,1,.3,1) forwards; pointer-events:all; }
    .toast-item.removing { animation: toastOut .25s ease-in forwards; }

    /* Button loading state */
    .btn-loading { position:relative; pointer-events:none; opacity:.85; }
    .btn-loading .btn-text { opacity:0; }
    .btn-loading::after {
      content:'';
      position:absolute;
      width:16px; height:16px;
      top:50%; left:50%;
      margin:-8px 0 0 -8px;
      border:2px solid rgba(255,255,255,.4);
      border-top-color:#fff;
      border-radius:50%;
      animation:spin .7s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }
  </style>

  <script>
    // ========== GLOBAL TOAST ==========
    window.showToast = function(message, type = 'success', duration = 4000) {
      const stack = document.getElementById('global-toast-stack');
      if (!stack) return;

      const colors = {
        success: { bg: 'bg-emerald-600', icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>` },
        error:   { bg: 'bg-red-600',     icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>` },
        warning: { bg: 'bg-amber-500',   icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>` },
        info:    { bg: 'bg-blue-600',    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>` },
      };
      const c = colors[type] || colors.info;

      const el = document.createElement('div');
      el.className = `toast-item flex items-center gap-3 px-4 py-3 rounded-xl text-white text-sm font-medium shadow-2xl ${c.bg}`;
      el.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><${c.icon}</svg>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.classList.add('removing');setTimeout(()=>this.parentElement.remove(),250)" class="opacity-60 hover:opacity-100 flex-shrink-0 transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
      stack.appendChild(el);

      setTimeout(() => {
        if (el.parentElement) {
          el.classList.add('removing');
          setTimeout(() => el.remove(), 250);
        }
      }, duration);
    };

    // ========== GLOBAL CONFIRM ==========
    window.showConfirm = function(message, onConfirm, options = {}) {
      const overlay = document.getElementById('global-confirm-overlay');
      const box     = document.getElementById('global-confirm-box');
      const msgEl   = document.getElementById('global-confirm-msg');
      const titleEl = document.getElementById('global-confirm-title');
      const okBtn   = document.getElementById('global-confirm-ok');
      const cancelBtn = document.getElementById('global-confirm-cancel');

      msgEl.textContent   = message;
      titleEl.textContent = options.title   || 'Confirmar ação';
      okBtn.textContent   = options.okText  || 'Confirmar';
      okBtn.className     = `flex-1 px-4 py-2.5 text-sm font-bold text-white rounded-xl transition-all shadow-lg ${options.danger !== false ? 'bg-red-600 hover:bg-red-700 hover:shadow-red-500/20' : 'bg-blue-600 hover:bg-blue-700 hover:shadow-blue-500/20'}`;

      overlay.classList.remove('hidden');
      requestAnimationFrame(() => { box.style.transform='scale(1)'; box.style.opacity='1'; });

      const close = () => {
        box.style.transform='scale(.95)'; box.style.opacity='0';
        setTimeout(() => overlay.classList.add('hidden'), 200);
      };

      const newOk = okBtn.cloneNode(true);
      const newCancel = cancelBtn.cloneNode(true);
      okBtn.replaceWith(newOk);
      cancelBtn.replaceWith(newCancel);

      document.getElementById('global-confirm-ok').textContent = options.okText || 'Confirmar';
      document.getElementById('global-confirm-ok').className = okBtn.className;
      document.getElementById('global-confirm-ok').addEventListener('click', () => { close(); onConfirm(); });
      document.getElementById('global-confirm-cancel').addEventListener('click', close);
      overlay.addEventListener('click', e => { if (e.target === overlay) close(); }, { once: true });
    };

    // ========== BUTTON LOADING HELPER ==========
    window.setButtonLoading = function(btn, loading, text) {
      if (!btn) return;
      if (loading) {
        btn._originalHTML = btn.innerHTML;
        btn.classList.add('btn-loading');
        btn.innerHTML = `<span class="btn-text">${btn.textContent}</span>`;
        btn.disabled = true;
      } else {
        btn.classList.remove('btn-loading');
        if (btn._originalHTML) btn.innerHTML = btn._originalHTML;
        if (text) btn.textContent = text;
        btn.disabled = false;
      }
    };

    // Page transition
    document.addEventListener('DOMContentLoaded', function() {
      const pageContent = document.querySelector('.page-transition');
      if (pageContent) setTimeout(() => pageContent.classList.add('loaded'), 100);
    });
  </script>
  
  <!-- Debug Panel (só se debug estiver ativo) -->
  <?php 
  $showDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true' || isset($_GET['debug']);
  if ($showDebug): 
      include __DIR__ . '/../partials/debug-panel.php'; 
  endif; 
  ?>
</body>
</html>
