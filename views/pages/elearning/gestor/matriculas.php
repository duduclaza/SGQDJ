<?php
// views/pages/elearning/gestor/matriculas.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .el-form-panel { max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.4,0,.2,1), opacity .3s; opacity: 0; }
  .el-form-panel.open { max-height: 400px; opacity: 1; }
</style>

<div class="space-y-6 el-fade-in">

  <!-- Header -->
  <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
    <a href="/elearning/gestor/cursos" class="text-green-200 hover:text-white text-sm transition">← Voltar aos Cursos</a>
    <h1 class="text-2xl font-bold mt-2 flex items-center gap-2">
      <span>👥</span> Matrículas — <?= htmlspecialchars($curso['titulo'] ?? '') ?>
    </h1>
    <p class="text-green-100 text-sm mt-1"><?= count($matriculas) ?> colaborador(es) matriculado(s)</p>
  </div>

  <!-- Inline Form: Matricular -->
  <?php if ($canEdit): ?>
  <div>
    <button onclick="toggleMatForm()" id="btnMat"
      class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-md">
      <svg class="w-4 h-4 transition-transform" id="iconMat" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span id="btnMatText">Matricular Colaborador</span>
    </button>
    <div id="formMatPanel" class="el-form-panel mt-3">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Matricular Colaborador</h3>
        <form id="formMatricula" class="space-y-4">
          <input type="hidden" name="id_curso" value="<?= (int)($curso['id'] ?? 0) ?>">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Colaborador *</label>
            <select name="id_usuario" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50/50 focus:ring-2 focus:ring-green-500 transition">
              <option value="">Selecione...</option>
              <?php foreach ($usuarios as $u): ?>
              <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name']) ?> — <?= htmlspecialchars($u['email']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" onclick="toggleMatForm()" class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
            <button type="button" onclick="matricular()" class="px-5 py-2 text-sm font-bold bg-green-600 text-white rounded-xl hover:bg-green-700 shadow transition">✅ Matricular</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Matrículas Table -->
  <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gradient-to-r from-gray-50 to-white">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data Matrícula</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Progresso</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Conclusão</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (empty($matriculas)): ?>
          <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">
            <div class="text-4xl mb-2">👥</div>
            Nenhuma matrícula registrada
          </td></tr>
          <?php else: ?>
          <?php foreach ($matriculas as $m): ?>
          <tr class="hover:bg-gray-50/50 transition">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-xs">
                  <?= mb_strtoupper(mb_substr($m['usuario_nome'] ?? '', 0, 1)) ?>
                </div>
                <div>
                  <div class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($m['usuario_nome']) ?></div>
                  <div class="text-xs text-gray-400"><?= htmlspecialchars($m['usuario_email'] ?? '') ?></div>
                </div>
              </div>
            </td>
            <td class="px-5 py-3 text-gray-600 text-xs"><?= date('d/m/Y H:i', strtotime($m['data_matricula'])) ?></td>
            <td class="px-5 py-3">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[120px]">
                  <div class="bg-green-500 h-2 rounded-full transition-all" style="width: <?= (float)$m['progresso_pct'] ?>%"></div>
                </div>
                <span class="text-xs text-gray-500 font-medium"><?= number_format((float)$m['progresso_pct'], 1) ?>%</span>
              </div>
            </td>
            <td class="px-5 py-3">
              <?php $sc = ['em_andamento'=>'blue','concluido'=>'green','reprovado'=>'red'][$m['status']] ?? 'gray'; ?>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-<?= $sc ?>-100 text-<?= $sc ?>-700">
                <?= strtoupper(str_replace('_',' ',$m['status'])) ?>
              </span>
            </td>
            <td class="px-5 py-3 text-gray-600 text-xs"><?= $m['concluido_em'] ? date('d/m/Y', strtotime($m['concluido_em'])) : '—' ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function toggleMatForm() {
  const p = document.getElementById('formMatPanel');
  const i = document.getElementById('iconMat');
  const t = document.getElementById('btnMatText');
  if (p.classList.contains('open')) { p.classList.remove('open'); i.style.transform='rotate(0)'; t.textContent='Matricular Colaborador'; }
  else { p.classList.add('open'); i.style.transform='rotate(45deg)'; t.textContent='Fechar'; }
}

function showToast(msg, type) {
  const e = document.getElementById('elToast'); if (e) e.remove();
  const c = { success:'bg-green-600', error:'bg-red-600' };
  const d = document.createElement('div'); d.id='elToast';
  d.className = `fixed bottom-6 right-6 z-[100] ${c[type]||'bg-green-600'} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium`;
  d.style.animation = 'elFadeIn .3s ease'; d.textContent = msg;
  document.body.appendChild(d); setTimeout(()=>d.remove(), 3500);
}

async function matricular() {
  const fd = new FormData(document.getElementById('formMatricula'));
  try {
    const res = await fetch('/elearning/gestor/matriculas/store', { method:'POST', body:fd });
    const d = await res.json();
    if (d.success) { showToast(d.message,'success'); setTimeout(()=>location.reload(),800); }
    else showToast('Erro: '+d.message,'error');
  } catch(e) { showToast('Erro de conexão','error'); }
}
</script>
