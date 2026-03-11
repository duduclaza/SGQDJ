<?php
// views/pages/elearning/gestor/provas.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .el-form-panel { max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.4,0,.2,1), opacity .3s; opacity: 0; }
  .el-form-panel.open { max-height: 800px; opacity: 1; }
  .el-card { transition: transform .2s, box-shadow .2s; }
  .el-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
</style>

<div class="space-y-6 el-fade-in">

  <!-- Header -->
  <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
    <a href="/elearning/gestor/cursos" class="text-purple-200 hover:text-white text-sm transition">← Voltar aos Cursos</a>
    <h1 class="text-2xl font-bold mt-2 flex items-center gap-2">
      <span>📝</span> Provas — <?= htmlspecialchars($curso['titulo'] ?? '') ?>
    </h1>
    <p class="text-purple-100 text-sm mt-1"><?= count($provas) ?> prova(s) cadastrada(s)</p>
  </div>

  <!-- Inline Form: Nova Prova -->
  <?php if ($canEdit): ?>
  <div>
    <button onclick="toggleProvaForm()" id="btnNovaProva"
      class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-md">
      <svg class="w-4 h-4 transition-transform" id="iconProva" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span id="btnProvaText">Nova Prova</span>
    </button>
    <div id="formProvaPanel" class="el-form-panel mt-3">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Criar Nova Prova</h3>
        <form id="formProva" class="space-y-4">
          <input type="hidden" name="id_curso" value="<?= (int)($curso['id'] ?? 0) ?>">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Título *</label>
            <input type="text" name="titulo" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-purple-500 transition">
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nota Mín. %</label>
              <input type="number" name="nota_minima" value="70" min="0" max="100" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-purple-500 transition">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tentativas</label>
              <input type="number" name="tentativas_max" value="3" min="1" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-purple-500 transition">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tempo (min)</label>
              <input type="number" name="tempo_min" value="0" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-purple-500 transition">
            </div>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" onclick="toggleProvaForm()" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
            <button type="button" onclick="salvarProva()" class="px-5 py-2 text-sm font-bold bg-purple-600 text-white rounded-xl hover:bg-purple-700 shadow transition">💾 Criar Prova</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Provas List -->
  <?php if (empty($provas)): ?>
  <div class="bg-white rounded-2xl shadow-md p-10 text-center">
    <div class="text-5xl mb-3">📝</div>
    <h3 class="text-lg font-semibold text-gray-600">Nenhuma prova cadastrada</h3>
    <p class="text-sm text-gray-400 mt-1">Crie provas para avaliar o aprendizado</p>
  </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach ($provas as $p): ?>
    <div class="el-card bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-5">
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 text-lg flex-shrink-0">📝</div>
            <div>
              <h3 class="font-bold text-gray-900"><?= htmlspecialchars($p['titulo']) ?></h3>
              <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                <span class="bg-gray-100 px-2 py-0.5 rounded-md">🎯 Nota mín: <strong><?= $p['nota_minima'] ?>%</strong></span>
                <span class="bg-gray-100 px-2 py-0.5 rounded-md">🔄 Tentativas: <strong><?= $p['tentativas_max'] ?></strong></span>
                <span class="bg-gray-100 px-2 py-0.5 rounded-md">⏱ <?= $p['tempo_min'] > 0 ? $p['tempo_min'].' min' : 'Sem limite' ?></span>
                <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-md font-semibold">❓ <?= (int)($p['total_questoes'] ?? 0) ?> questões</span>
              </div>
            </div>
          </div>
          <?php if ($canEdit): ?>
          <button onclick="toggleQuestaoForm(<?= (int)$p['id'] ?>)"
            class="text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition ml-4 flex-shrink-0">
            ➕ Questão
          </button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Inline Add Question -->
      <div id="questao_<?= (int)$p['id'] ?>" class="el-form-panel">
        <div class="border-t border-gray-100 bg-purple-50/50 p-5">
          <h4 class="text-sm font-bold text-gray-800 mb-3">➕ Nova Questão — <?= htmlspecialchars($p['titulo']) ?></h4>
          <form id="formQuestao_<?= (int)$p['id'] ?>" class="space-y-3">
            <input type="hidden" name="id_prova" value="<?= (int)$p['id'] ?>">
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-1">Enunciado *</label>
              <textarea name="enunciado" required rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tipo</label>
                <select name="tipo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition">
                  <option value="multipla">Múltipla escolha</option>
                  <option value="verdadeiro_falso">Verdadeiro/Falso</option>
                  <option value="dissertativa">Dissertativa</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Pontos</label>
                <input type="number" name="pontos" value="1" min="0.5" step="0.5" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition">
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-500 mb-2">Alternativas (marque a correta)</label>
              <?php for ($i = 0; $i < 4; $i++): ?>
              <div class="flex items-center gap-2 mb-2">
                <input type="radio" name="correta" value="<?= $i ?>" class="text-purple-600 focus:ring-purple-500">
                <input type="text" name="alternativas[]" placeholder="Alternativa <?= chr(65+$i) ?>" class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-purple-500 transition">
              </div>
              <?php endfor; ?>
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" onclick="toggleQuestaoForm(<?= (int)$p['id'] ?>)" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
              <button type="button" onclick="salvarQuestao(<?= (int)$p['id'] ?>)" class="px-4 py-1.5 text-sm font-bold bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow transition">💾 Salvar</button>
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
function toggleProvaForm() {
  const p = document.getElementById('formProvaPanel');
  const i = document.getElementById('iconProva');
  const t = document.getElementById('btnProvaText');
  if (p.classList.contains('open')) { p.classList.remove('open'); i.style.transform='rotate(0)'; t.textContent='Nova Prova'; }
  else { p.classList.add('open'); i.style.transform='rotate(45deg)'; t.textContent='Fechar'; }
}

function toggleQuestaoForm(provaId) {
  document.getElementById('questao_' + provaId).classList.toggle('open');
}

function showToast(msg, type) {
  const e = document.getElementById('elToast'); if (e) e.remove();
  const c = { success:'bg-green-600', error:'bg-red-600' };
  const d = document.createElement('div'); d.id='elToast';
  d.className = `fixed bottom-6 right-6 z-[100] ${c[type]||'bg-purple-600'} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium`;
  d.style.animation = 'elFadeIn .3s ease'; d.textContent = msg;
  document.body.appendChild(d); setTimeout(()=>d.remove(), 3500);
}

async function salvarProva() {
  const fd = new FormData(document.getElementById('formProva'));
  try {
    const res = await fetch('/elearning/gestor/provas/store', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast(d.message,'success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}

async function salvarQuestao(provaId) {
  const fd = new FormData(document.getElementById('formQuestao_' + provaId));
  try {
    const res = await fetch('/elearning/gestor/questoes/store', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast('Questão salva!','success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}
</script>
