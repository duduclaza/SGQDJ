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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
<body class="bg-gray-50 text-gray-900">
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header/Navbar -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-3">
          <!-- Espaço vazio à esquerda -->
          <div></div>
          
          <div class="flex items-center gap-4">
            <!-- Ícone de Suporte (Admin e Super Admin) -->
            <?php if (isAdmin()): ?>
            <?php 
              // Contar solicitações pendentes APENAS para Super Admin
              $suportePendentes = 0;
              if (isSuperAdmin()) {
                $suportePendentes = \App\Controllers\SuporteController::contarPendentes();
              }
            ?>
            <a href="/suporte" class="relative group">
              <button class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200" title="Suporte Técnico">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
                </svg>
                <!-- Badge com contador (APENAS Super Admin) -->
                <?php if (isSuperAdmin() && $suportePendentes > 0): ?>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1">
                  <?= $suportePendentes ?>
                </span>
                <?php endif; ?>
              </button>
              <div class="absolute right-0 mt-2 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                🆘 Suporte <?php if (isSuperAdmin() && $suportePendentes > 0): ?>(<?= $suportePendentes ?> pendente<?= $suportePendentes > 1 ? 's' : '' ?>)<?php endif; ?>
              </div>
            </a>
            <?php endif; ?>
            
            <!-- Ícone de Notificações -->
            <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200" title="Notificações">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              <!-- Badge de notificações -->
              <!-- <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span> -->
            </button>
            
            <!-- User Menu -->
            <div class="flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
              <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-sm">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
              </div>
              <span class="text-sm font-medium text-gray-700"><?= $_SESSION['user_name'] ?? 'Usuário' ?></span>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
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

  <!-- Chat virtual global -->
  <?php if (isset($_SESSION['user_id'])): ?>
  <div id="chat-widget" class="chat-widget">
    <button id="chat-toggle" class="chat-toggle" type="button">
      <span>Chat</span>
      <span id="chat-unread-badge" class="chat-unread-badge hidden">0</span>
    </button>

    <div id="chat-panel" class="chat-panel hidden">
      <div class="chat-header">
        <strong>Chat interno</strong>
        <span class="chat-subtitle">Usuários online/offline • chat em tempo real</span>
      </div>

      <div class="chat-retention-warning">
        As conversas deste chat são armazenadas em JSON e apagadas automaticamente a cada 30 dias.
      </div>

      <div class="chat-body">
        <aside class="chat-contacts">
          <input id="chat-search" type="text" placeholder="Buscar usuário..." class="chat-search-input">
          <div id="chat-contacts-list" class="chat-contacts-list"></div>
        </aside>

        <section class="chat-conversation">
          <div id="chat-empty" class="chat-empty">Selecione um usuário para conversar.</div>
          <div id="chat-conversation-header" class="chat-conversation-header hidden"></div>
          <div id="chat-messages" class="chat-messages hidden"></div>
          <form id="chat-form" class="chat-form hidden">
            <button id="chat-emoji-toggle" class="chat-emoji-toggle" type="button" title="Emojis">😊</button>
            <div id="chat-emoji-picker" class="chat-emoji-picker hidden">
              <button type="button" data-emoji="😀">😀</button>
              <button type="button" data-emoji="😂">😂</button>
              <button type="button" data-emoji="😉">😉</button>
              <button type="button" data-emoji="😍">😍</button>
              <button type="button" data-emoji="👍">👍</button>
              <button type="button" data-emoji="🙏">🙏</button>
              <button type="button" data-emoji="🎉">🎉</button>
              <button type="button" data-emoji="✅">✅</button>
              <button type="button" data-emoji="⚠️">⚠️</button>
              <button type="button" data-emoji="❤️">❤️</button>
            </div>
            <input id="chat-message-input" type="text" maxlength="2000" placeholder="Digite sua mensagem..." autocomplete="off">
            <button type="submit">Enviar</button>
          </form>
        </section>
      </div>
    </div>
  </div>

  <style>
    .chat-widget { position: fixed; right: 20px; bottom: 20px; z-index: 1200; font-family: inherit; }
    .chat-toggle { display: inline-flex; align-items: center; gap: 8px; border: 0; border-radius: 999px; padding: 8px 13px; background: #0f172a; color: #fff; font-size: 13px; cursor: pointer; box-shadow: 0 8px 24px rgba(2, 6, 23, 0.28); }
    .chat-unread-badge { min-width: 18px; height: 18px; padding: 0 6px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #ef4444; display: inline-flex; align-items: center; justify-content: center; }
    .chat-unread-badge.hidden { display: none; }
    .chat-panel { width: min(760px, calc(100vw - 28px)); height: 480px; background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; margin-top: 10px; box-shadow: 0 20px 55px rgba(15, 23, 42, 0.2); overflow: hidden; }
    .chat-panel.hidden { display: none; }
    .chat-header { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; display: flex; flex-direction: column; background: linear-gradient(90deg, #f8fafc 0%, #eef2ff 100%); }
    .chat-subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }
    .chat-retention-warning { padding: 8px 12px; border-bottom: 1px solid #fde68a; background: #fffbeb; color: #92400e; font-size: 12px; }
    .chat-body { display: grid; grid-template-columns: 260px 1fr; height: calc(100% - 96px); }
    .chat-contacts { border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; background: #f8fafc; }
    .chat-search-input { margin: 10px; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 10px; font-size: 13px; }
    .chat-contacts-list { overflow: auto; padding: 0 8px 10px; }
    .chat-contact-item { width: 100%; border: 0; text-align: left; padding: 10px; border-radius: 10px; margin-bottom: 6px; background: transparent; cursor: pointer; }
    .chat-contact-item:hover, .chat-contact-item.active { background: #e2e8f0; }
    .chat-contact-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .chat-contact-person { display: inline-flex; align-items: center; gap: 8px; min-width: 0; }
    .chat-contact-avatar { width: 26px; height: 26px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; background: #334155; overflow: hidden; flex-shrink: 0; }
    .chat-contact-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .chat-contact-name { font-size: 13px; color: #111827; font-weight: 600; }
    .chat-contact-status { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b; }
    .chat-status-dot { width: 8px; height: 8px; border-radius: 999px; background: #94a3b8; }
    .chat-status-dot.online { background: #22c55e; }
    .chat-unread { background: #ef4444; color: #fff; border-radius: 999px; font-size: 10px; padding: 2px 6px; font-weight: 700; }
    .chat-conversation { display: flex; flex-direction: column; min-width: 0; min-height: 0; }
    .chat-empty { margin: auto; color: #6b7280; font-size: 13px; }
    .chat-conversation-header { padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; font-weight: 600; color: #1f2937; }
    .chat-conversation-header.hidden { display: none; }
    .chat-conversation-title { display: inline-flex; align-items: center; gap: 8px; }
    .chat-conversation-sub { color: #64748b; font-weight: 500; font-size: 11px; }
    .chat-messages { flex: 1; min-height: 0; overflow: auto; padding: 12px; background: #f8fafc; }
    .chat-messages.hidden { display: none; }
    .chat-message-row { display: flex; align-items: flex-end; gap: 6px; margin-bottom: 8px; }
    .chat-message-row.me { justify-content: flex-end; }
    .chat-message-row.other { justify-content: flex-start; }
    .chat-message-avatar { width: 24px; height: 24px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #fff; background: #64748b; overflow: hidden; flex-shrink: 0; }
    .chat-message-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .chat-message { width: fit-content; max-width: 62%; padding: 7px 9px; border-radius: 11px; font-size: 13px; line-height: 1.35; white-space: pre-wrap; word-break: break-word; }
    .chat-message.me { background: #dbeafe; color: #1e3a8a; }
    .chat-message.other { background: #e5e7eb; color: #111827; }
    .chat-message-meta { margin-top: 4px; font-size: 10px; opacity: 0.8; display: inline-flex; align-items: center; gap: 4px; }
    .chat-read-status { font-size: 11px; letter-spacing: -1px; }
    .chat-read-status.sent { color: #64748b; }
    .chat-read-status.read { color: #2563eb; }
    .chat-form { position: relative; display: flex; flex-shrink: 0; gap: 8px; padding: 10px; border-top: 1px solid #e5e7eb; background: #fff; }
    .chat-form.hidden { display: none; }
    .chat-emoji-toggle { border: 1px solid #d1d5db; background: #fff; color: #374151; border-radius: 10px; width: 36px; min-width: 36px; height: 36px; cursor: pointer; }
    .chat-emoji-picker { position: absolute; bottom: 54px; left: 10px; z-index: 10; background: #fff; border: 1px solid #d1d5db; border-radius: 10px; padding: 6px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; box-shadow: 0 8px 18px rgba(2, 6, 23, 0.15); }
    .chat-emoji-picker.hidden { display: none; }
    .chat-emoji-picker button { border: 0; background: #fff; font-size: 16px; line-height: 1; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; }
    .chat-emoji-picker button:hover { background: #f1f5f9; }
    .chat-form input { flex: 1; border: 1px solid #d1d5db; border-radius: 10px; padding: 8px 10px; font-size: 13px; }
    .chat-form button { border: 0; background: #2563eb; color: #fff; border-radius: 10px; padding: 8px 14px; font-size: 13px; cursor: pointer; }
    @media (max-width: 900px) {
      .chat-panel { width: min(96vw, 560px); height: 72vh; }
      .chat-body { grid-template-columns: 1fr; }
      .chat-contacts { max-height: 40%; border-right: 0; border-bottom: 1px solid #e5e7eb; }
    }
  </style>

  <script>
    (function() {
      const meId = <?= (int)($_SESSION['user_id'] ?? 0) ?>;
      const meName = <?= json_encode((string)($_SESSION['user_name'] ?? 'Você')) ?>;
      if (!meId) return;

      let contacts = [];
      let activeMode = 'global';
      let activeContactId = null;
      let lastGlobalSeenId = 0;
      const lastDirectSeenByUser = {};
      let pollTimer = null;
      let heartbeatTimer = null;

      const ui = {};

      function q(id) { return document.getElementById(id); }
      function getInitial(name) {
        return (String(name || '?').trim().charAt(0) || '?').toUpperCase();
      }

      function avatarHtml(userId, name, hasPhoto, className, avatarUrl) {
        const cls = className || 'chat-contact-avatar';
        if (avatarUrl) {
          return `<span class="${cls}"><img src="${escapeHtml(avatarUrl)}" alt="${escapeHtml(name)}"></span>`;
        }
        if (Number(hasPhoto) === 1) {
          return `<span class="${cls}"><img src="/profile/photo/${userId}" alt="${escapeHtml(name)}"></span>`;
        }
        return `<span class="${cls}">${escapeHtml(getInitial(name))}</span>`;
      }

      function playMessageSound(kind) {
        try {
          const AudioCtx = window.AudioContext || window.webkitAudioContext;
          if (!AudioCtx) return;
          const ctx = new AudioCtx();
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.type = 'sine';
          osc.frequency.value = kind === 'send' ? 760 : 560;
          gain.gain.setValueAtTime(0.0001, ctx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.06, ctx.currentTime + 0.01);
          gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.13);
          osc.start();
          osc.stop(ctx.currentTime + 0.14);
        } catch (_) {}
      }

      function escapeHtml(value) {
        return String(value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/\"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function fmtDate(iso) {
        const d = new Date(iso.replace(' ', 'T'));
        if (Number.isNaN(d.getTime())) return '';
        return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
      }

      async function fetchJson(url, options) {
        const response = await fetch(url, options || {});
        return response.json();
      }

      async function heartbeat() {
        try {
          await fetchJson('/api/chat/heartbeat', { method: 'POST' });
        } catch (_) {}
      }

      async function loadContacts() {
        const searchValue = (ui.search.value || '').trim().toLowerCase();
        try {
          const data = await fetchJson('/api/chat/contacts');
          if (!data.success) return;
          contacts = data.contacts || [];

          const filtered = contacts.filter(c =>
            c.name.toLowerCase().includes(searchValue) ||
            c.email.toLowerCase().includes(searchValue)
          );

          const globalButton = `
            <button class="chat-contact-item ${activeMode === 'global' ? 'active' : ''}" data-mode="global">
              <div class="chat-contact-top">
                <span class="chat-contact-person">
                  <span class="chat-contact-avatar">#</span>
                  <span class="chat-contact-name">Sala Geral</span>
                </span>
              </div>
              <div class="chat-contact-status">
                <span class="chat-status-dot online"></span>
                Todos os usuários
              </div>
            </button>
          `;

          ui.contactsList.innerHTML = globalButton + (filtered.map(c => {
            const isActive = String(c.id) === String(activeContactId);
            const online = Number(c.is_online) === 1;
            const unread = Number(c.unread_count || 0);
            return `
              <button class="chat-contact-item ${(activeMode === 'direct' && isActive) ? 'active' : ''}" data-mode="direct" data-user-id="${c.id}">
                <div class="chat-contact-top">
                  <span class="chat-contact-person">
                    ${avatarHtml(c.id, c.name, c.has_photo, 'chat-contact-avatar', c.avatar_url)}
                    <span class="chat-contact-name">${escapeHtml(c.name)}</span>
                  </span>
                  ${unread > 0 ? `<span class="chat-unread">${unread}</span>` : ''}
                </div>
                <div class="chat-contact-status">
                  <span class="chat-status-dot ${online ? 'online' : ''}"></span>
                  ${online ? 'Online' : 'Offline'}
                </div>
              </button>
            `;
          }).join('') || '<div style="padding:10px;color:#64748b;font-size:12px;">Nenhum usuário encontrado.</div>');

          const totalUnread = contacts.reduce((sum, c) => sum + Number(c.unread_count || 0), 0);
          if (totalUnread > 0) {
            ui.badge.textContent = totalUnread > 99 ? '99+' : String(totalUnread);
            ui.badge.classList.remove('hidden');
          } else {
            ui.badge.classList.add('hidden');
          }
        } catch (_) {}
      }

      async function loadGlobalMessages() {
        try {
          const data = await fetchJson('/api/chat/messages/global');
          if (!data.success) return;
          const messages = data.messages || [];

          const maxId = messages.reduce((max, item) => Math.max(max, Number(item.id || 0)), 0);
          if (lastGlobalSeenId > 0) {
            const hasIncomingNew = messages.some(m => Number(m.id) > lastGlobalSeenId && Number(m.sender_id) !== meId);
            if (hasIncomingNew) playMessageSound('receive');
          }
          if (maxId > 0) lastGlobalSeenId = Math.max(lastGlobalSeenId, maxId);

          ui.empty.classList.add('hidden');
          ui.convHeader.classList.remove('hidden');
          ui.convHeader.innerHTML = `<span class="chat-conversation-title"><span class="chat-contact-avatar">#</span><span>Sala Geral</span></span> <span class="chat-conversation-sub">(todos com todos)</span>`;
          ui.messages.classList.remove('hidden');
          ui.form.classList.remove('hidden');

          ui.messages.innerHTML = messages.map(m => {
            const mine = Number(m.sender_id) === meId;
            const author = mine ? meName : (m.sender_name || 'Usuário');
            return `
              <div class="chat-message-row ${mine ? 'me' : 'other'}">
                ${mine ? '' : avatarHtml(m.sender_id, author, m.sender_has_photo, 'chat-message-avatar')}
                <div class="chat-message ${mine ? 'me' : 'other'}">
                  <div style="font-size:11px;font-weight:700;opacity:.85;margin-bottom:3px;">${escapeHtml(author)}</div>
                  <div>${escapeHtml(m.message)}</div>
                  <div class="chat-message-meta">${fmtDate(m.created_at)}</div>
                </div>
              </div>
            `;
          }).join('');

          ui.messages.scrollTop = ui.messages.scrollHeight;
        } catch (_) {}
      }

      async function loadMessages() {
        if (!activeContactId) return;
        try {
          const data = await fetchJson(`/api/chat/messages/${activeContactId}`);
          if (!data.success) return;
          const messages = data.messages || [];

          const prevSeen = Number(lastDirectSeenByUser[activeContactId] || 0);
          const maxId = messages.reduce((max, item) => Math.max(max, Number(item.id || 0)), 0);
          if (prevSeen > 0) {
            const hasIncomingNew = messages.some(m => Number(m.id) > prevSeen && Number(m.sender_id) !== meId);
            if (hasIncomingNew) playMessageSound('receive');
          }
          if (maxId > 0) lastDirectSeenByUser[activeContactId] = Math.max(prevSeen, maxId);

          const selectedContact = contacts.find(c => String(c.id) === String(activeContactId));
          ui.empty.classList.add('hidden');
          ui.convHeader.classList.remove('hidden');
          if (selectedContact) {
            ui.convHeader.innerHTML = `<span class="chat-conversation-title">${avatarHtml(selectedContact.id, selectedContact.name, selectedContact.has_photo, 'chat-contact-avatar', selectedContact.avatar_url)}<span>${escapeHtml(selectedContact.name)}</span></span> <span class="chat-conversation-sub">(${Number(selectedContact.is_online) === 1 ? 'Online' : 'Offline'})</span>`;
          } else {
            ui.convHeader.textContent = 'Conversa';
          }
          ui.messages.classList.remove('hidden');
          ui.form.classList.remove('hidden');

          ui.messages.innerHTML = messages.map(m => {
            const mine = Number(m.sender_id) === meId;
            const readClass = m.read_at ? 'read' : 'sent';
            const readLabel = mine ? `<span class="chat-read-status ${readClass}">${m.read_at ? '✓✓' : '✓'}</span>` : '';
            return `
              <div class="chat-message-row ${mine ? 'me' : 'other'}">
                ${mine ? '' : (selectedContact ? avatarHtml(selectedContact.id, selectedContact.name, selectedContact.has_photo, 'chat-message-avatar', selectedContact.avatar_url) : '')}
                <div class="chat-message ${mine ? 'me' : 'other'}">
                  <div>${escapeHtml(m.message)}</div>
                  <div class="chat-message-meta">${fmtDate(m.created_at)} ${readLabel}</div>
                </div>
              </div>
            `;
          }).join('');

          ui.messages.scrollTop = ui.messages.scrollHeight;
        } catch (_) {}
      }

      async function sendMessage(event) {
        event.preventDefault();
        const text = ui.messageInput.value.trim();
        if (!text) return;

        const payload = new URLSearchParams();
        let endpoint = '/api/chat/send';
        if (activeMode === 'global') {
          endpoint = '/api/chat/send-global';
          payload.set('message', text);
        } else {
          if (!activeContactId) return;
          payload.set('receiver_id', activeContactId);
          payload.set('message', text);
        }

        try {
          const data = await fetchJson(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: payload.toString()
          });

          if (!data.success) {
            alert(data.message || 'Erro ao enviar mensagem');
            return;
          }

          ui.messageInput.value = '';
          playMessageSound('send');
          if (activeMode === 'global') {
            await loadGlobalMessages();
          } else {
            await loadMessages();
          }
          await loadContacts();
        } catch (_) {
          alert('Erro ao enviar mensagem');
        }
      }

      function selectGlobal() {
        activeMode = 'global';
        activeContactId = null;
        loadContacts().then(loadGlobalMessages);
      }

      function selectContact(contactId) {
        activeMode = 'direct';
        activeContactId = String(contactId);
        loadContacts().then(loadMessages);
      }

      function bindEvents() {
        ui.toggle.addEventListener('click', async function() {
          ui.panel.classList.toggle('hidden');
          if (!ui.panel.classList.contains('hidden')) {
            await loadContacts();
            if (activeMode === 'global') {
              await loadGlobalMessages();
            } else if (activeContactId) {
              await loadMessages();
            }
          }
        });

        ui.search.addEventListener('input', loadContacts);
        ui.emojiToggle.addEventListener('click', function() {
          ui.emojiPicker.classList.toggle('hidden');
        });

        ui.emojiPicker.addEventListener('click', function(event) {
          const btn = event.target.closest('button[data-emoji]');
          if (!btn) return;
          ui.messageInput.value += btn.getAttribute('data-emoji');
          ui.messageInput.focus();
        });

        document.addEventListener('click', function(event) {
          if (!ui.form.contains(event.target) || event.target === ui.messageInput) {
            if (event.target !== ui.emojiToggle && !ui.emojiPicker.contains(event.target)) {
              ui.emojiPicker.classList.add('hidden');
            }
          }
        });

        ui.contactsList.addEventListener('click', function(event) {
          const btn = event.target.closest('.chat-contact-item');
          if (!btn) return;
          const mode = btn.getAttribute('data-mode');
          if (mode === 'global') {
            selectGlobal();
            return;
          }
          selectContact(btn.getAttribute('data-user-id'));
        });

        ui.form.addEventListener('submit', sendMessage);
      }

      async function init() {
        ui.toggle = q('chat-toggle');
        ui.panel = q('chat-panel');
        ui.badge = q('chat-unread-badge');
        ui.search = q('chat-search');
        ui.contactsList = q('chat-contacts-list');
        ui.empty = q('chat-empty');
        ui.convHeader = q('chat-conversation-header');
        ui.messages = q('chat-messages');
        ui.form = q('chat-form');
        ui.messageInput = q('chat-message-input');
        ui.emojiToggle = q('chat-emoji-toggle');
        ui.emojiPicker = q('chat-emoji-picker');

        if (!ui.toggle || !ui.panel) return;

        bindEvents();
        await heartbeat();
        await loadContacts();
        await loadGlobalMessages();

        pollTimer = setInterval(async function() {
          await loadContacts();
          if (activeMode === 'global') {
            await loadGlobalMessages();
          } else if (activeContactId) {
            await loadMessages();
          }
        }, 5000);

        heartbeatTimer = setInterval(heartbeat, 30000);

        window.addEventListener('beforeunload', function() {
          if (pollTimer) clearInterval(pollTimer);
          if (heartbeatTimer) clearInterval(heartbeatTimer);
        });
      }

      document.addEventListener('DOMContentLoaded', init);
    })();
  </script>
  <?php endif; ?>

  <!-- Loading overlay removido - causava problemas em todos os módulos -->

  <script>
    // Page transition and smooth navigation
    document.addEventListener('DOMContentLoaded', function() {
      // Add loaded class for initial page load
      const pageContent = document.querySelector('.page-transition');
      if (pageContent) {
        setTimeout(() => pageContent.classList.add('loaded'), 100);
      }

      // Navegação simples sem loading global (removido para evitar problemas)
      // Cada módulo pode implementar seu próprio loading se necessário
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
