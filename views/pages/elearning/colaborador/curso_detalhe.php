<?php
// views/pages/elearning/colaborador/curso_detalhe.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .el-mat-panel { transition: max-height .4s ease, opacity .3s; max-height: 0; overflow: hidden; opacity: 0; }
  .el-mat-panel.open { max-height: 600px; opacity: 1; }
  .el-card { transition: transform .2s, box-shadow .2s; }
  .el-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.1); }
</style>

<div class="max-w-5xl mx-auto space-y-6 el-fade-in">

  <!-- Course Hero Header -->
  <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 rounded-2xl overflow-hidden shadow-xl relative">
    <div class="p-6 sm:p-8 relative z-10">
      <a href="/elearning/colaborador" class="text-blue-200 hover:text-white text-sm transition inline-flex items-center gap-1">
        ← Catálogo de Cursos
      </a>
      <h1 class="text-2xl sm:text-3xl font-bold text-white mt-3"><?= htmlspecialchars($curso['titulo'] ?? '') ?></h1>
      <?php if (!empty($curso['descricao'])): ?>
      <p class="text-blue-100 text-sm mt-2 max-w-2xl"><?= htmlspecialchars($curso['descricao']) ?></p>
      <?php endif; ?>
      
      <div class="flex flex-wrap gap-4 mt-4 text-sm text-blue-100">
        <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-lg">⏱ <?= (int)($curso['carga_horaria'] ?? 0) ?> min</span>
        <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-lg">📖 <?= count($aulas) ?> aulas</span>
        <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-lg">📝 <?= count($provas) ?> prova(s)</span>
      </div>
    </div>
    
    <!-- Progress Bar in Hero -->
    <?php $pct = (float)($matricula['progresso_pct'] ?? 0); ?>
    <div class="px-6 sm:px-8 pb-5">
      <div class="flex items-center justify-between text-sm text-white mb-2">
        <span class="font-medium">Seu Progresso</span>
        <span class="font-bold text-lg"><?= number_format($pct, 0) ?>%</span>
      </div>
      <div class="bg-white/20 rounded-full h-3">
        <div class="h-3 rounded-full bg-white/90 transition-all duration-500 shadow-sm" style="width: <?= $pct ?>%"></div>
      </div>
    </div>
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
  </div>

  <!-- Lesson Accordion -->
  <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
      <h2 class="font-bold text-gray-900 flex items-center gap-2">📖 Conteúdo do Curso</h2>
    </div>
    <?php if (empty($aulas)): ?>
    <div class="p-8 text-center text-gray-400">
      <div class="text-4xl mb-2">📖</div>
      Nenhuma aula cadastrada neste curso.
    </div>
    <?php else: ?>
    <?php foreach ($aulas as $idx => $a):
      $mats = $materiaisByAula[$a['id']] ?? [];
    ?>
    <div class="border-b border-gray-50 last:border-0">
      <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 transition cursor-pointer" onclick="toggleMateriais(<?= (int)$a['id'] ?>, this)">
        <div class="flex items-center gap-4">
          <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
            <?= $idx + 1 ?>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($a['titulo']) ?></h3>
            <?php if ($a['descricao']): ?>
            <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($a['descricao']) ?></p>
            <?php endif; ?>
            <div class="text-xs text-gray-400 mt-1">📎 <?= count($mats) ?> material(is)</div>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400 transition-transform mat-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>
      <div id="materiais_<?= (int)$a['id'] ?>" class="el-mat-panel">
        <div class="px-6 pb-4 pl-16 space-y-2">
          <?php if (empty($mats)): ?>
            <div class="text-xs text-gray-400 py-3 italic">Nenhum material nesta aula.</div>
          <?php else: ?>
            <?php foreach ($mats as $m): 
              $visto = !empty($progresso[$m['id']]);
              $icones = ['video' => '🎬', 'pdf' => '📄', 'imagem' => '🖼️', 'slide' => '📊', 'texto' => '📝'];
            ?>
              <div class="flex items-center gap-3 p-3 bg-white rounded-xl shadow-sm border border-gray-100 group hover:border-indigo-200 transition">
                <div class="w-8 h-8 <?= $visto ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' ?> rounded-lg flex items-center justify-center text-lg shadow-sm">
                  <?= $visto ? '✅' : ($icones[$m['tipo']] ?? '📁') ?>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($m['titulo']) ?></div>
                  <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider"><?= $m['tipo'] ?></div>
                </div>
                
                <?php if ($m['tipo'] === 'texto'): ?>
                <a href="/elearning/colaborador/materiais/<?= (int)$m['id'] ?>/assistir" target="_blank"
                  class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                  Ler Conteúdo →
                </a>
                <?php else: ?>
                <a href="/elearning/colaborador/materiais/<?= (int)$m['id'] ?>/assistir" target="_blank"
                  class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                  Abrir Material →
                </a>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Modal para Material de Texto -->
  <div id="modalTexto" class="fixed inset-0 z-[150] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="fecharTexto()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden el-fade-in">
        <div class="p-5 border-b flex items-center justify-between bg-gray-50">
          <h3 id="modalTextoTitulo" class="font-bold text-gray-900">Conteúdo do Material</h3>
          <button onclick="fecharTexto()" class="text-gray-400 hover:text-gray-600 transition">
             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div id="modalTextoCorpo" class="p-8 overflow-y-auto text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
        <div class="p-5 border-t bg-gray-50 text-right">
          <button onclick="fecharTexto()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl font-bold shadow-md transition">
            Entendido
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Exams -->
  <?php if (!empty($provas)): ?>
  <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
      <h2 class="font-bold text-gray-900 flex items-center gap-2">📝 Avaliações</h2>
    </div>
    <div class="p-4 space-y-3">
      <?php foreach ($provas as $p): ?>
      <div class="el-card flex items-center justify-between p-4 bg-gray-50/70 rounded-xl border border-gray-100">
        <div>
          <div class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($p['titulo']) ?></div>
          <div class="flex gap-3 text-xs text-gray-500 mt-1">
            <span>🎯 Nota mín: <?= $p['nota_minima'] ?>%</span>
            <span>🔄 <?= $p['tentativas_feitas'] ?>/<?= $p['tentativas_max'] ?> tentativas</span>
          </div>
        </div>
        <?php if ((int)$p['tentativas_feitas'] < (int)$p['tentativas_max']): ?>
        <a href="/elearning/colaborador/provas/<?= (int)$p['id'] ?>/fazer"
          class="text-sm font-bold bg-purple-600 text-white px-4 py-2 rounded-xl hover:bg-purple-700 shadow-md transition">
          ▶ Fazer Prova
        </a>
        <?php else: ?>
        <span class="text-xs text-gray-400 bg-gray-200 rounded-full px-3 py-1 font-medium">Tentativas esgotadas</span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Certificate -->
  <?php if ($certificado): ?>
  <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6 flex items-center justify-between shadow-sm">
    <div>
      <div class="text-green-800 font-bold text-lg flex items-center gap-2">🏆 Certificado Disponível!</div>
      <div class="text-green-600 text-sm mt-1">Código: <span class="font-mono bg-green-100 px-2 py-0.5 rounded"><?= htmlspecialchars($certificado['codigo_validacao']) ?></span></div>
    </div>
    <a href="/elearning/colaborador/certificados/<?= htmlspecialchars($certificado['codigo_validacao']) ?>" target="_blank"
      class="bg-green-600 text-white px-5 py-2.5 rounded-xl hover:bg-green-700 text-sm font-bold shadow-md transition">
      📄 Baixar Certificado
    </a>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleMateriais(aulaId, headerEl) {
  const panel = document.getElementById('materiais_' + aulaId);
  const chevron = headerEl.querySelector('.mat-chevron');
  
  if (panel.classList.contains('open')) {
    panel.classList.remove('open');
    chevron.style.transform = 'rotate(0)';
  } else {
    panel.classList.add('open');
    chevron.style.transform = 'rotate(180deg)';
  }
}

async function abrirTexto(materialId) {
  try {
    const res = await fetch('/elearning/colaborador/materiais/' + materialId + '/assistir');
    const d = await res.json();
    if (d.success) {
      document.getElementById('modalTextoTitulo').textContent = d.titulo;
      document.getElementById('modalTextoCorpo').textContent = d.texto;
      document.getElementById('modalTexto').classList.remove('hidden');
      marcarVisto(materialId);
    }
  } catch(e) { console.error(e); }
}

function fecharTexto() {
  document.getElementById('modalTexto').classList.add('hidden');
}

function marcarVisto(materialId) {
  const fd = new FormData();
  fd.append('id_material', materialId);
  fd.append('pct', 100);
  fetch('/elearning/colaborador/progresso/registrar', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
       if (d.success && d.pct >= 90) {
         // Optionally reload to update progress percentage in UI
         // location.reload(); 
       }
    });
}
</script>

