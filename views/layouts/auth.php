<?php
$title = $title ?? 'OTI - Login';
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
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      margin: 0;
      padding: 0;
      overflow-y: auto;
    }
    .btn-primary {
      background: #2563eb;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background: #1d4ed8;
      transform: scale(1.02);
    }
    .typing-effect {
      border-right: 2px solid #6b7280;
      animation: blink 0.7s step-end infinite;
      display: inline-block;
      min-height: 1.5rem;
    }
    @keyframes blink {
      from, to { border-color: transparent; }
      50% { border-color: #6b7280; }
    }
    @media (max-width: 768px) {
      .right-panel {
        display: none;
      }
      .left-panel {
        min-height: 100vh;
      }
    }
    
    /* ===== TEMA DE NATAL ===== */
    
    /* Container da neve */
    .snow-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 9999;
      overflow: hidden;
    }
    
    /* Flocos de neve */
    .snowflake {
      position: absolute;
      top: -10px;
      color: white;
      font-size: 1em;
      text-shadow: 0 0 5px rgba(255,255,255,0.7);
      animation: fall linear infinite;
      opacity: 0.8;
    }
    
    @keyframes fall {
      0% {
        transform: translateY(-10px) rotate(0deg);
        opacity: 1;
      }
      100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0.3;
      }
    }
    
    /* Papai Noel voando */
    .santa-container {
      position: fixed;
      top: 12%;
      z-index: 100;
      pointer-events: none;
      animation: fly-santa 18s linear infinite;
    }
    
    @keyframes fly-santa {
      0% {
        left: -200px;
        top: 12%;
      }
      50% {
        top: 8%;
      }
      100% {
        left: 110%;
        top: 15%;
      }
    }
    
    .santa-sleigh {
      width: 150px;
      height: auto;
      filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
    }
    
    /* Montanhas de fundo */
    .mountains-container {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 30%;
      z-index: 1;
      pointer-events: none;
    }
    
    .mountain {
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 100%;
    }
    
    .mountain-back {
      fill: rgba(100, 116, 139, 0.3);
    }
    
    .mountain-front {
      fill: rgba(71, 85, 105, 0.4);
    }
    
    .mountain-snow {
      fill: rgba(255, 255, 255, 0.6);
    }
  </style>
</head>
<body class="min-h-screen">
  <div class="flex min-h-screen">
    <!-- Painel Esquerdo - Azul com efeito roxo/lilás -->
    <div class="left-panel w-full md:w-1/2 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-800 flex items-center justify-center p-4 md:p-8" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #6366f1 100%);">
      <div class="w-full max-w-md my-8">
        <?php include $viewFile; ?>
      </div>
    </div>
    
    <!-- Painel Direito - Clean Design -->
    <div class="right-panel hidden md:flex md:w-1/2 items-center justify-center p-12" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);">
      <div class="text-center">
        <h1 class="text-8xl font-bold mb-4 text-blue-800">OTI</h1>
        <p class="text-lg text-gray-500 font-light tracking-wide typing-effect" id="typingText"></p>
        
        <!-- Elemento decorativo -->
        <div class="mt-12 flex justify-center gap-2">
          <div class="w-2 h-2 rounded-full bg-blue-400 opacity-60"></div>
          <div class="w-2 h-2 rounded-full bg-blue-500 opacity-70"></div>
          <div class="w-2 h-2 rounded-full bg-blue-600 opacity-80"></div>
        </div>
      </div>
    </div>
  </div>
  
  <?php 
  // ===== TEMA DE NATAL - Apenas em Dezembro =====
  $isDecember = (int)date('n') === 12;
  if ($isDecember): 
  ?>
  
  <!-- Neve caindo -->
  <div class="snow-container" id="snowContainer"></div>
  
  <!-- Papai Noel voando -->
  <div class="santa-container">
    <svg class="santa-sleigh" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg">
      <!-- Trenó -->
      <path d="M20,60 Q30,70 60,65 L140,55 Q160,52 170,45 L175,40 Q178,35 175,32 L160,35 L140,38 L60,48 Q40,52 20,60" fill="#8B4513"/>
      <path d="M25,58 Q35,65 60,62 L135,53 Q155,50 165,44" stroke="#5D3A1A" stroke-width="2" fill="none"/>
      
      <!-- Patins do trenó -->
      <path d="M15,68 Q10,70 8,68 Q5,65 10,62 L50,58 Q60,56 70,58 L150,48 Q165,45 175,40" stroke="#C0C0C0" stroke-width="3" fill="none"/>
      
      <!-- Corpo Papai Noel -->
      <ellipse cx="100" cy="35" rx="20" ry="18" fill="#CC0000"/>
      
      <!-- Cabeça -->
      <circle cx="105" cy="18" r="12" fill="#FFE4C4"/>
      
      <!-- Gorro -->
      <path d="M95,12 Q100,0 120,8 Q125,10 122,15 L95,15 Z" fill="#CC0000"/>
      <circle cx="122" cy="8" r="4" fill="white"/>
      <path d="M93,14 Q105,18 120,14" stroke="white" stroke-width="4" fill="none"/>
      
      <!-- Barba -->
      <path d="M97,22 Q105,32 113,22" fill="white"/>
      
      <!-- Braços -->
      <path d="M85,30 L70,25 L65,28" stroke="#CC0000" stroke-width="6" fill="none"/>
      <circle cx="65" cy="27" r="4" fill="#FFE4C4"/>
      
      <!-- Renas (silhueta simplificada) -->
      <g transform="translate(-30, 10)">
        <ellipse cx="45" cy="35" rx="12" ry="8" fill="#8B4513"/>
        <circle cx="52" cy="30" r="5" fill="#8B4513"/>
        <path d="M54,26 L58,18 M56,26 L62,20" stroke="#5D3A1A" stroke-width="2"/>
        <circle cx="54" cy="31" r="1" fill="red"/>
      </g>
      
      <!-- Cordas -->
      <path d="M65,28 L35,38" stroke="#8B5A2B" stroke-width="2"/>
    </svg>
  </div>
  
  <!-- Montanhas de fundo -->
  <div class="mountains-container">
    <svg class="mountain" viewBox="0 0 1440 320" preserveAspectRatio="none">
      <!-- Montanha traseira -->
      <path class="mountain-back" d="M0,320 L0,200 L200,100 L350,180 L500,80 L650,160 L800,60 L950,150 L1100,50 L1250,140 L1440,70 L1440,320 Z"/>
      
      <!-- Neve no topo traseira -->
      <path class="mountain-snow" d="M200,100 L180,115 L200,110 L220,115 Z M500,80 L475,100 L500,92 L525,100 Z M800,60 L770,85 L800,75 L830,85 Z M1100,50 L1065,80 L1100,68 L1135,80 Z"/>
      
      <!-- Montanha frontal -->
      <path class="mountain-front" d="M0,320 L0,220 L150,140 L300,200 L450,120 L600,190 L750,100 L900,180 L1050,90 L1200,170 L1350,110 L1440,160 L1440,320 Z"/>
      
      <!-- Neve no topo frontal -->
      <path class="mountain-snow" d="M150,140 L125,160 L150,150 L175,160 Z M450,120 L420,145 L450,132 L480,145 Z M750,100 L715,130 L750,115 L785,130 Z M1050,90 L1010,125 L1050,108 L1090,125 Z M1350,110 L1315,140 L1350,125 L1385,140 Z"/>
    </svg>
  </div>
  
  <script>
    // Criar flocos de neve
    function createSnowflakes() {
      const container = document.getElementById('snowContainer');
      const snowflakes = ['❄', '❅', '❆', '•'];
      
      for (let i = 0; i < 50; i++) {
        const snowflake = document.createElement('div');
        snowflake.className = 'snowflake';
        snowflake.innerHTML = snowflakes[Math.floor(Math.random() * snowflakes.length)];
        snowflake.style.left = Math.random() * 100 + '%';
        snowflake.style.fontSize = (Math.random() * 10 + 8) + 'px';
        snowflake.style.opacity = Math.random() * 0.6 + 0.4;
        snowflake.style.animationDuration = (Math.random() * 5 + 8) + 's';
        snowflake.style.animationDelay = (Math.random() * 10) + 's';
        container.appendChild(snowflake);
      }
    }
    createSnowflakes();
  </script>
  
  <?php endif; // Fim do tema de Natal ?>
  
  <script>
    // Efeito de digitação
    const text = 'Organização Tecnológica Integrada';
    const typingElement = document.getElementById('typingText');
    let index = 0;
    let isDeleting = false;
    
    function typeWriter() {
      if (!isDeleting && index <= text.length) {
        typingElement.textContent = text.substring(0, index);
        index++;
        setTimeout(typeWriter, 100);
      } else if (isDeleting && index >= 0) {
        typingElement.textContent = text.substring(0, index);
        index--;
        setTimeout(typeWriter, 50);
      } else if (index > text.length) {
        setTimeout(() => {
          isDeleting = true;
          typeWriter();
        }, 2000);
      } else {
        isDeleting = false;
        index = 0;
        setTimeout(typeWriter, 500);
      }
    }
    
    typeWriter();
  </script>
</body>
</html>
