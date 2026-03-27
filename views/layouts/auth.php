<?php
$title = $title ?? 'SGQ - Login';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title><?= e($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * { font-family: 'Inter', sans-serif; }

    /* ─── Background Premium ─── */
    body {
      background-color: #0f1117;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    /* Grade geométrica sutil */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
      background-size: 48px 48px;
      pointer-events: none;
      z-index: 0;
    }

    /* Glow orbs decorativos */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      pointer-events: none;
      animation: orb-float 10s ease-in-out infinite;
    }
    .orb-1 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%);
      top: -100px; left: -150px;
      animation-delay: 0s;
    }
    .orb-2 {
      width: 400px; height: 400px;
      background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%);
      bottom: -80px; right: -120px;
      animation-delay: 5s;
    }
    .orb-3 {
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(20,184,166,0.04) 0%, transparent 70%);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      animation-delay: 3s;
    }

    @keyframes orb-float {
      0%, 100% { transform: scale(1) translate(0, 0); }
      33% { transform: scale(1.05) translate(10px, -15px); }
      66% { transform: scale(0.97) translate(-8px, 10px); }
    }

    /* ─── Card ─── */
    .auth-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 400px;
      background: rgba(255,255,255,0.04);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 40px 36px;
      box-shadow:
        0 0 0 1px rgba(255,255,255,0.04) inset,
        0 32px 64px rgba(0,0,0,0.4),
        0 2px 4px rgba(0,0,0,0.3);
    }

    /* Linha de brilho no topo do card */
    .auth-card::before {
      content: '';
      position: absolute;
      top: 0; left: 20%; right: 20%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
      border-radius: 50%;
    }

    /* ─── Inputs ─── */
    .auth-input {
      width: 100%;
      padding: 11px 14px 11px 42px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 12px;
      color: #e2e8f0;
      font-size: 14px;
      font-weight: 500;
      outline: none;
      transition: all 0.2s ease;
    }
    .auth-input::placeholder { color: rgba(148,163,184,0.5); }
    .auth-input:focus {
      background: rgba(255,255,255,0.08);
      border-color: rgba(99,120,255,0.5);
      box-shadow: 0 0 0 3px rgba(99,120,255,0.1);
    }

    /* ─── Botão ─── */
    .btn-auth {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.01em;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 4px 16px rgba(99,102,241,0.3);
      position: relative;
      overflow: hidden;
    }
    .btn-auth::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
      transition: left 0.5s ease;
    }
    .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(99,102,241,0.4); }
    .btn-auth:hover::before { left: 100%; }
    .btn-auth:active { transform: translateY(0); }

    /* ─── Label ─── */
    .auth-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: rgba(148,163,184,0.8);
      text-transform: uppercase;
      letter-spacing: 0.08em;
      margin-bottom: 6px;
    }

    /* ─── Logo badge ─── */
    .logo-badge {
      width: 48px; height: 48px;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 8px 24px rgba(59,130,246,0.3);
      margin: 0 auto 20px;
    }

    /* Badge de status online */
    .status-dot {
      display: inline-block;
      width: 6px; height: 6px;
      background: #22c55e;
      border-radius: 50%;
      animation: pulse-green 2s ease-in-out infinite;
      box-shadow: 0 0 0 0 rgba(34,197,94,0.4);
    }
    @keyframes pulse-green {
      0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); transform: scale(1); }
      50% { box-shadow: 0 0 0 4px rgba(34,197,94,0); transform: scale(1.1); }
    }

    /* Loading state */
    .btn-auth.loading { opacity: 0.7; cursor: not-allowed; transform: none !important; }
  </style>
</head>
<body>

  <!-- Orbs de fundo -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <!-- Noise texture overlay -->
  <div style="position:fixed;inset:0;z-index:1;opacity:0.025;background-image:url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');background-repeat:repeat;background-size:128px 128px;pointer-events:none;"></div>

  <!-- Auth Card -->
  <div class="auth-card mx-4">
    <?php include $viewFile; ?>
  </div>

  <?php include __DIR__ . '/../partials/ui-feedback.php'; ?>
  <?php include __DIR__ . '/../partials/ui-scripts.php'; ?>

</body>
</html>
