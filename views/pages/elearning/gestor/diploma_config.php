<?php
// views/pages/elearning/gestor/diploma_config.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  
  /* Layout Selector */
  .layout-card { cursor: pointer; border: 2px solid transparent; transition: all .3s; border-radius: 12px; overflow: hidden; background: white; }
  .layout-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,.1); }
  .layout-card.selected { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,.1); }
  .layout-preview-small { aspect-ratio: 1.414/1; background: #f8fafc; position: relative; overflow: hidden; }

  /* Diploma Layouts - Common */
  .diploma-container { aspect-ratio: 1.414/1; background: white; width: 100%; max-width: 800px; margin: 0 auto; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border-radius: 4px; position: relative; z-index: 1; overflow: hidden; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; }
  
  /* Template 1: Classic Professional */
  .tpl-1 { border: 15px double #1e293b; padding: 60px; }
  .tpl-1 .title { font-family: 'serif'; font-size: 42px; color: #1e293b; margin-bottom: 20px; }
  .tpl-1 .label { text-transform: uppercase; letter-spacing: 2px; color: #64748b; font-size: 14px; margin-bottom: 30px; }
  .tpl-1 .name { font-size: 32px; font-weight: 800; border-bottom: 2px solid #1e293b; padding-bottom: 5px; margin-bottom: 20px; color: #0f172a; }

  /* Template 2: Modern Minimal */
  .tpl-2 { border-left: 20px solid #6366f1; padding: 40px 60px; }
  .tpl-2 .bg-accent { position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: #6366f1; opacity: 0.05; border-radius: 50%; }
  .tpl-2 .title { font-size: 38px; font-weight: 900; color: #1e1b4b; background: linear-gradient(to right, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
  .tpl-2 .name { font-size: 36px; font-weight: 700; color: #1e1b4b; margin: 25px 0; }

  /* Template 3: Gold Premium */
  .tpl-3 { background: #fffdf5; border: 12px solid #d4af37; outline: 3px solid #d4af37; outline-offset: -20px; color: #45321d; }
  .tpl-3 .title { font-family: 'serif'; font-size: 46px; color: #856404; font-variant: small-caps; }
  .tpl-3 .seal { position: absolute; bottom: 40px; right: 40px; width: 80px; height: 80px; background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="%23d4af37" opacity="0.8"/><path d="M50 20 L55 40 L70 45 L55 50 L50 70 L45 50 L30 45 L45 40 Z" fill="white"/></svg>'); }

  /* Template 4: Corporate Blue */
  .tpl-4 { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white; border: 20px solid white; outline: 1px solid #e2e8f0; }
  .tpl-4 .title { font-size: 40px; font-weight: 900; text-transform: uppercase; margin-bottom: 10px; }
  .tpl-4 .line { height: 4px; width: 60px; background: #60a5fa; margin: 20px 0; }
  .tpl-4 .name { font-size: 34px; font-weight: 600; color: #eff6ff; }

  /* Template 5: Creative Soft */
  .tpl-5 { background: #faf5ff; border-radius: 30px; border: 1px solid #e9d5ff; }
  .tpl-5 .border-inner { position: absolute; inset: 20px; border: 2px dashed #d8b4fe; border-radius: 20px; pointer-events: none; }
  .tpl-5 .title { color: #581c87; font-size: 34px; font-weight: 800; }
  .tpl-5 .name { font-size: 32px; font-weight: 700; color: #7e22ce; background: #f3e8ff; padding: 5px 20px; border-radius: 12px; }

  /* Upload Zone */
  .upload-logo-zone { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; transition: all .2s; cursor: pointer; }
  .upload-logo-zone:hover { border-color: #6366f1; background: #f1f5f9; }
</style>

<div class="space-y-6 el-fade-in pb-20">

  <!-- Header -->
  <div class="bg-gradient-to-r from-gray-900 to-indigo-900 rounded-2xl p-6 text-white shadow-lg flex items-center justify-between">
    <div>
      <a href="/elearning/gestor/cursos" class="text-indigo-300 hover:text-white text-sm transition">← Voltar aos Cursos</a>
      <h1 class="text-2xl font-bold mt-2 flex items-center gap-2">
        <span class="text-2xl">⚙️</span> Configuração de Diploma
      </h1>
      <p class="text-indigo-100 text-sm mt-1">Configure a identidade visual dos certificados emitidos pela plataforma</p>
    </div>
    <button onclick="salvarConfig()" id="btnSalvarGeral" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg transition flex items-center gap-2">
      <span id="saveLabel">💾 Salvar Alterações</span>
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT: Settings -->
    <div class="space-y-6">
      
      <!-- Logo Settings -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
          <span class="bg-indigo-100 p-1.5 rounded-lg">🖼️</span> Logomarca do Diploma
        </h3>
        <input type="file" id="logoInput" class="hidden" onchange="handleLogoSelect(this)" accept="image/*">
        <div class="upload-logo-zone text-center" onclick="document.getElementById('logoInput').click()">
          <div id="logoPreviewWrapper" class="<?= ($config['logo_diploma'] ?? false) ? '' : 'hidden' ?> mb-3">
             <img id="logoPreviewImg" src="<?= ($config['logo_diploma'] ?? false) ? '/elearning/gestor/diploma/logo' : '' ?>" class="max-h-24 mx-auto object-contain">
          </div>
          <div id="logoSelectPrompt" class="<?= ($config['logo_diploma'] ?? false) ? 'hidden' : '' ?>">
            <div class="text-3xl mb-1 text-gray-400">🏢</div>
            <p class="text-xs font-bold text-gray-600">Clique para subir sua logo</p>
            <p class="text-[10px] text-gray-400 mt-1">PNG transparente recomendado</p>
          </div>
          <?php if($config['logo_diploma'] ?? false): ?>
            <p class="text-[10px] text-indigo-600 font-bold mt-2">Clique para alterar</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- General Settings -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
          <span class="bg-amber-100 p-1.5 rounded-lg">✍️</span> Assinatura e Textos
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Texto da Assinatura</label>
            <input type="text" id="assinaturaTexto" value="<?= htmlspecialchars($config['assinatura_texto'] ?? 'Diretoria SGQDJ') ?>" 
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition"
                   oninput="updatePreviewText()">
            <p class="text-[10px] text-gray-400 mt-1">Ex: Diretor Executivo, Nome do Responsável, etc.</p>
          </div>
        </div>
      </div>

      <!-- Help info -->
      <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
        <p class="text-xs text-indigo-700 leading-relaxed font-medium">
          💡 <b>Dica:</b> O diploma é gerado automaticamente quando o aluno atinge a nota mínima na prova final do curso. 
          O layout escolhido será aplicado a todos os certificados.
        </p>
      </div>

    </div>

    <!-- RIGHT: Templates -->
    <div class="lg:col-span-2 space-y-6">
      
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
         <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-6 flex items-center gap-2">
          <span class="bg-purple-100 p-1.5 rounded-lg">🎨</span> Escolha seu Modelo
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
          <?php for($i=1; $i<=5; $i++): ?>
          <div id="layoutBtn_<?= $i ?>" class="layout-card <?= ($config['layout_ativo'] ?? 1) == $i ? 'selected' : '' ?>" onclick="selectLayout(<?= $i ?>)">
            <div class="layout-preview-small flex items-center justify-center">
               <span class="text-xs font-bold text-gray-400">Modelo <?= $i ?></span>
               <div id="mini_preview_<?= $i ?>" class="absolute inset-2 border group-hover:scale-105 transition scale-90 opacity-60 pointer-events-none"></div>
            </div>
            <div class="p-3 bg-gray-50 border-t flex items-center justify-between">
              <span class="text-xs font-bold text-gray-700">LAYOUT <?= $i ?></span>
              <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center">
                <div class="w-2 h-2 rounded-full bg-indigo-600 hidden check-inner"></div>
              </div>
            </div>
          </div>
          <?php endfor; ?>
        </div>

        <h3 class="text-sm font-bold text-gray-400 text-center uppercase tracking-widest mb-4 italic">Pré-visualização do Layout Selecionado</h3>
        
        <!-- DIPLOMA PREVIEW CANVAS -->
        <div id="diplomaPreview" class="diploma-container tpl-<?= $config['layout_ativo'] ?? 1 ?>">
          
          <!-- Template Extras Area (dashed borders, shapes etc) -->
          <div id="tplExtras"></div>

          <div class="relative z-10 w-full flex flex-col items-center">
            <!-- Logo -->
            <img id="prevLogo" src="<?= ($config['logo_diploma'] ?? false) ? '/elearning/gestor/diploma/logo' : '' ?>" 
                 class="max-h-20 mb-8 max-w-[200px] object-contain <?= ($config['logo_diploma'] ?? false) ? '' : 'hidden' ?>">
            
            <div class="title leading-tight">Certificado de Conclusão</div>
            
            <div class="label mt-2">Certificamos que para os devidos fins</div>
            
            <div class="name mt-4 mb-4 font-bold tracking-tight">Nome Completo do Aluno</div>
            
            <p class="text-gray-600 max-w-lg mx-auto text-sm leading-relaxed mb-8 conclusion-text">
              concluiu com êxito o treinamento de capacitação profissional no curso de<br>
              <span class="font-bold text-gray-800">TÍTULO DO CURSO EXEMPLO</span><br>
              com carga horária total de 40 horas.
            </p>

            <div class="flex items-end justify-between w-full mt-10 px-10">
              <div class="text-left">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Emitido em:</p>
                <p class="text-sm font-bold text-gray-800"><?= date('d/m/Y') ?></p>
              </div>
              
              <div class="text-center">
                <div class="w-48 border-b-2 border-gray-900 mx-auto mb-1"></div>
                <p id="prevAssinatura" class="text-sm font-bold text-gray-800 tracking-tight"><?= htmlspecialchars($config['assinatura_texto'] ?? 'Diretoria SGQDJ') ?></p>
              </div>

              <div class="text-right">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Código:</p>
                <p class="text-[10px] font-mono font-bold text-gray-600">A1B2-C3D4-E5F6-G7H8</p>
              </div>
            </div>
          </div>

          <div id="tplDecorativeSeal"></div>
        </div>

      </div>

    </div>

  </div>

</div>

<script>
let currentLayout = <?= $config['layout_ativo'] ?? 1 ?>;

function selectLayout(id) {
  currentLayout = id;
  document.querySelectorAll('.layout-card').forEach(c => c.classList.remove('selected'));
  document.getElementById('layoutBtn_' + id).classList.add('selected');
  
  const prev = document.getElementById('diplomaPreview');
  prev.className = 'diploma-container tpl-' + id;
  
  // Decorative logic
  const extras = document.getElementById('tplExtras');
  extras.innerHTML = '';
  const seal = document.getElementById('tplDecorativeSeal');
  seal.className = '';
  
  if (id == 5) extras.innerHTML = '<div class="border-inner"></div>';
  if (id == 2) extras.innerHTML = '<div class="bg-accent"></div>';
  if (id == 3) seal.className = 'seal';
}

function handleLogoSelect(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('logoPreviewImg').src = e.target.result;
      document.getElementById('prevLogo').src = e.target.result;
      document.getElementById('logoPreviewWrapper').classList.remove('hidden');
      document.getElementById('prevLogo').classList.remove('hidden');
      document.getElementById('logoSelectPrompt').classList.add('hidden');
    };
    reader.readAsDataURL(file);
  }
}

function updatePreviewText() {
  const v = document.getElementById('assinaturaTexto').value;
  document.getElementById('prevAssinatura').textContent = v || 'Diretoria SGQDJ';
}

async function salvarConfig() {
  const btn = document.getElementById('btnSalvarGeral');
  const label = document.getElementById('saveLabel');
  btn.disabled = true; label.textContent = '⏳ Salvando...';

  const fd = new FormData();
  fd.append('layout_ativo', currentLayout);
  fd.append('assinatura_texto', document.getElementById('assinaturaTexto').value);
  
  const logoInput = document.getElementById('logoInput');
  if (logoInput.files && logoInput.files[0]) {
    fd.append('logo', logoInput.files[0]);
  }

  try {
    const res = await fetch('/elearning/gestor/diploma/save', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) {
      showToast('Configuração salva com sucesso! 🎉', 'success');
      setTimeout(() => location.href = '/elearning/gestor/cursos', 1500);
    } else {
      showToast('Erro ao salvar: ' + d.message, 'error');
    }
  } catch(e) {
    showToast('Erro de conexão', 'error');
  } finally {
    btn.disabled = false; label.textContent = '💾 Salvar Alterações';
  }
}

function showToast(msg, type) {
  const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-indigo-600' };
  const div = document.createElement('div');
  div.className = `fixed bottom-6 right-6 z-[100] ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium el-fade-in`;
  div.textContent = msg;
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3500);
}

// Init decorators
selectLayout(currentLayout);
</script>
