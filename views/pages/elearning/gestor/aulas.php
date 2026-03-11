<?php
// views/pages/elearning/gestor/aulas.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .el-form-panel { max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.4,0,.2,1), opacity .3s; opacity: 0; }
  .el-form-panel.open { max-height: 600px; opacity: 1; }
  .el-upload-panel { max-height: 0; overflow: hidden; transition: max-height .4s ease, opacity .3s; opacity: 0; }
  .el-upload-panel.open { max-height: 500px; opacity: 1; }
  .el-card { transition: transform .2s, box-shadow .2s; }
  .el-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
</style>

<div class="space-y-6 el-fade-in">

  <!-- Header -->
  <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
    <a href="/elearning/gestor/cursos" class="text-blue-200 hover:text-white text-sm transition">← Voltar aos Cursos</a>
    <h1 class="text-2xl font-bold mt-2 flex items-center gap-2">
      <span class="text-2xl">📖</span> Aulas — <?= htmlspecialchars($curso['titulo'] ?? '') ?>
    </h1>
    <p class="text-blue-100 text-sm mt-1"><?= count($aulas) ?> aula(s) cadastrada(s)</p>
  </div>

  <!-- Inline Form: Nova Aula -->
  <?php if ($canEdit): ?>
  <div>
    <button onclick="toggleAulaForm()" id="btnNovaAula"
      class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-md">
      <svg class="w-4 h-4 transition-transform" id="iconAula" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span id="btnAulaText">Nova Aula</span>
    </button>
    <div id="formAulaPanel" class="el-form-panel mt-3">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Criar Nova Aula</h3>
        <form id="formAula" class="space-y-4">
          <input type="hidden" name="id_curso" value="<?= (int)($curso['id'] ?? 0) ?>">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Título *</label>
              <input type="text" name="titulo" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ordem</label>
              <input type="number" name="ordem" value="<?= count($aulas) ?>" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Descrição</label>
            <textarea name="descricao" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" onclick="toggleAulaForm()" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
            <button type="button" onclick="salvarAula()" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow transition">💾 Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Aulas List -->
  <?php if (empty($aulas)): ?>
  <div class="bg-white rounded-2xl shadow-md p-10 text-center">
    <div class="text-5xl mb-3">📖</div>
    <h3 class="text-lg font-semibold text-gray-600">Nenhuma aula cadastrada</h3>
    <p class="text-sm text-gray-400 mt-1">Adicione aulas para compor este curso</p>
  </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach ($aulas as $idx => $a): ?>
    <div class="el-card bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-5">
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
              <?= $idx + 1 ?>
            </div>
            <div>
              <h3 class="font-bold text-gray-900"><?= htmlspecialchars($a['titulo']) ?></h3>
              <?php if ($a['descricao']): ?>
              <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($a['descricao']) ?></p>
              <?php endif; ?>
              <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                <span>📎 <?= (int)($a['total_materiais'] ?? 0) ?> material(is)</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2 ml-4 flex-shrink-0">
            <?php if ($canEdit): ?>
            <button onclick="toggleUpload(<?= (int)$a['id'] ?>)"
              class="text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition">
              📎 Material
            </button>
            <?php endif; ?>
            <?php if ($canDelete): ?>
            <button onclick="excluirAula(<?= (int)$a['id'] ?>, '<?= htmlspecialchars(addslashes($a['titulo'])) ?>')"
              class="text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
              🗑 Excluir
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Inline Upload Panel -->
      <div id="upload_<?= (int)$a['id'] ?>" class="el-upload-panel">
        <div class="border-t border-gray-100 bg-purple-50/50 p-5">
          <h4 class="text-sm font-bold text-gray-800 mb-3">📎 Enviar Material — <?= htmlspecialchars($a['titulo']) ?></h4>
          <form id="formUpload_<?= (int)$a['id'] ?>" class="space-y-3">
            <input type="hidden" name="id_aula" value="<?= (int)$a['id'] ?>">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Título *</label>
                <input type="text" name="titulo" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition">
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tipo *</label>
                <select name="tipo" onchange="toggleTipoMaterial(<?= (int)$a['id'] ?>, this.value)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition">
                  <option value="video">🎬 Vídeo (20MB)</option>
                  <option value="pdf">📄 PDF (20MB)</option>
                  <option value="imagem">🖼️ Imagem (10MB)</option>
                  <option value="slide">📊 Slide (20MB)</option>
                  <option value="texto">📝 Texto</option>
                </select>
              </div>
              <div id="fileField_<?= (int)$a['id'] ?>">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Arquivo *</label>
                <input type="file" name="arquivo" class="w-full text-sm file:mr-2 file:rounded-lg file:border-0 file:bg-purple-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-purple-700 file:cursor-pointer">
              </div>
            </div>
            <!-- Textarea para tipo texto (hidden por padrão) -->
            <div id="textoField_<?= (int)$a['id'] ?>" style="display:none;" class="mt-3">
              <label class="block text-xs font-semibold text-gray-500 mb-1">Conteúdo do Texto *</label>
              <textarea name="conteudo_texto" rows="6" placeholder="Digite o conteúdo do material aqui... Suporta texto longo, instruções, orientações, etc."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition resize-y"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-3">
              <button type="button" onclick="toggleUpload(<?= (int)$a['id'] ?>)" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
              <button type="button" onclick="enviarMaterial(<?= (int)$a['id'] ?>)" class="px-4 py-1.5 text-sm font-bold bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow transition">📤 Enviar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleAulaForm() {
  const p = document.getElementById('formAulaPanel');
  const i = document.getElementById('iconAula');
  const t = document.getElementById('btnAulaText');
  if (p.classList.contains('open')) {
    p.classList.remove('open'); i.style.transform='rotate(0)'; t.textContent='Nova Aula';
  } else {
    p.classList.add('open'); i.style.transform='rotate(45deg)'; t.textContent='Fechar';
  }
}

function toggleUpload(id) {
  const p = document.getElementById('upload_' + id);
  p.classList.toggle('open');
}

function showToast(msg, type) {
  const e = document.getElementById('elToast'); if (e) e.remove();
  const c = { success:'bg-green-600', error:'bg-red-600' };
  const d = document.createElement('div'); d.id='elToast';
  d.className = `fixed bottom-6 right-6 z-[100] ${c[type]||'bg-indigo-600'} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium`;
  d.style.animation = 'elFadeIn .3s ease'; d.textContent = msg;
  document.body.appendChild(d); setTimeout(()=>d.remove(), 3500);
}

async function salvarAula() {
  const fd = new FormData(document.getElementById('formAula'));
  try {
    const res = await fetch('/elearning/gestor/aulas/store', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast(d.message,'success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}

async function excluirAula(id, titulo) {
  if (!confirm(`Excluir a aula "${titulo}"?`)) return;
  const fd = new FormData(); fd.append('id', id);
  try {
    const res = await fetch('/elearning/gestor/aulas/delete', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast(d.message,'success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}

async function enviarMaterial(aulaId) {
  const fd = new FormData(document.getElementById('formUpload_' + aulaId));
  try {
    const res = await fetch('/elearning/gestor/materiais/upload', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast('Material enviado!','success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}

function toggleTipoMaterial(aulaId, tipo) {
  const fileField = document.getElementById('fileField_' + aulaId);
  const textoField = document.getElementById('textoField_' + aulaId);
  if (tipo === 'texto') {
    fileField.style.display = 'none';
    textoField.style.display = '';
    // Limpar file input para não enviar arquivo junto com texto
    const fi = fileField.querySelector('input[type="file"]');
    if (fi) fi.value = '';
  } else {
    fileField.style.display = '';
    textoField.style.display = 'none';
    // Limpar textarea
    const ta = textoField.querySelector('textarea');
    if (ta) ta.value = '';
  }
}
</script>
