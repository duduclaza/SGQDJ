<?php
// views/pages/elearning/gestor/cursos.php
?>
<style>
  .el-fade-in { animation: elFadeIn .4s ease; }
  @keyframes elFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .el-card { transition: transform .2s, box-shadow .2s; }
  .el-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,.12); }
  .el-gradient-header { background: linear-gradient(135deg, #1e40af 0%, #6366f1 50%, #8b5cf6 100%); }
  .el-form-panel { max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.4,0,.2,1), opacity .3s; opacity: 0; }
  .el-form-panel.open { max-height: 900px; opacity: 1; }
  .el-badge { font-size: .65rem; letter-spacing: .05em; }
  .el-stat-card { backdrop-filter: blur(10px); background: rgba(255,255,255,.85); border: 1px solid rgba(255,255,255,.5); }
  .el-thumb { background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); }
  .el-search:focus { box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
</style>

<div class="space-y-6 el-fade-in">

  <!-- Header -->
  <div class="el-gradient-header rounded-2xl p-6 sm:p-8 text-white shadow-lg">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight flex items-center gap-2">
          <span class="text-3xl">📚</span> Cursos — eLearning
        </h1>
        <p class="text-blue-100 text-sm mt-1">Gerencie todos os cursos da plataforma</p>
      </div>
      <?php if ($canEdit): ?>
      <button onclick="toggleFormPanel()" id="btnNovoCurso" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition border border-white/30 shadow-sm">
        <svg class="w-5 h-5 transition-transform" id="iconPlus" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span id="btnNovoCursoText">Novo Curso</span>
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Inline Form Panel -->
  <?php if ($canEdit): ?>
  <div id="formPanel" class="el-form-panel">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-100">
        <h2 id="formPanelTitle" class="text-lg font-bold text-gray-900">Criar Novo Curso</h2>
        <p class="text-xs text-gray-500 mt-0.5">Preencha as informações do curso abaixo</p>
      </div>
      <form id="formCurso" class="p-6 space-y-5">
        <input type="hidden" name="id" id="cursoId">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <!-- Título -->
          <div class="lg:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Título do Curso *</label>
            <input type="text" name="titulo" id="cursoTitulo" required
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-50/50"
              placeholder="Ex.: Eletrocardiograma — Fundamentos">
          </div>

          <!-- Descrição -->
          <div class="lg:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Descrição</label>
            <textarea name="descricao" id="cursoDescricao" rows="3"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-50/50"
              placeholder="Resumo do conteúdo, público-alvo e resultado esperado."></textarea>
          </div>

          <!-- Carga Horária -->
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Carga Horária (min)</label>
            <input type="number" name="carga_horaria" id="cursoCarga" min="0"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-50/50"
              placeholder="0">
          </div>

          <!-- Status -->
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" id="cursoStatus"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-50/50">
              <option value="rascunho">📝 Rascunho</option>
              <option value="ativo">✅ Ativo</option>
              <option value="inativo">⏸ Inativo</option>
            </select>
          </div>
        </div>

        <!-- Thumbnail -->
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-4">
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Thumbnail (opcional, max 10MB)</label>
          <input type="file" name="thumbnail" accept="image/*"
            class="w-full text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700 file:cursor-pointer file:transition">
          <p class="text-xs text-gray-400 mt-1.5">Formato recomendado: 1280×720px • JPG, PNG ou WebP</p>
        </div>

        <!-- Ações -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
          <button type="button" onclick="cancelarForm()"
            class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            Cancelar
          </button>
          <button type="button" onclick="salvarCurso()" id="btnSalvar"
            class="w-full sm:w-auto px-6 py-2.5 text-sm font-bold bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl hover:from-indigo-700 hover:to-blue-700 shadow-md hover:shadow-lg transition">
            💾 Salvar Curso
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Search Bar -->
  <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
    <div class="flex-1 relative">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input type="text" id="searchCursos" onkeyup="filtrarCursos()" placeholder="Buscar cursos..."
        class="el-search w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
    </div>
    <div class="flex gap-2">
      <select id="filterStatus" onchange="filtrarCursos()" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-indigo-500 transition">
        <option value="">Todos os status</option>
        <option value="ativo">✅ Ativo</option>
        <option value="rascunho">📝 Rascunho</option>
        <option value="inativo">⏸ Inativo</option>
      </select>
    </div>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <?php
      $totalAtivos = 0; $totalRascunho = 0; $totalInativos = 0;
      foreach ($cursos as $c) {
        if ($c['status'] === 'ativo') $totalAtivos++;
        elseif ($c['status'] === 'rascunho') $totalRascunho++;
        else $totalInativos++;
      }
    ?>
    <div class="el-stat-card rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-indigo-600"><?= count($cursos) ?></div>
      <div class="text-xs text-gray-500 mt-0.5">Total</div>
    </div>
    <div class="el-stat-card rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-green-600"><?= $totalAtivos ?></div>
      <div class="text-xs text-gray-500 mt-0.5">Ativos</div>
    </div>
    <div class="el-stat-card rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-yellow-600"><?= $totalRascunho ?></div>
      <div class="text-xs text-gray-500 mt-0.5">Rascunhos</div>
    </div>
    <div class="el-stat-card rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-gray-500"><?= $totalInativos ?></div>
      <div class="text-xs text-gray-500 mt-0.5">Inativos</div>
    </div>
  </div>

  <!-- Course Grid -->
  <?php if (empty($cursos)): ?>
  <div class="bg-white rounded-2xl shadow p-12 text-center">
    <div class="text-5xl mb-4">📚</div>
    <h3 class="text-lg font-semibold text-gray-700 mb-1">Nenhum curso cadastrado</h3>
    <p class="text-sm text-gray-400">Clique em "Novo Curso" para começar!</p>
  </div>
  <?php else: ?>
  <div id="cursosGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($cursos as $c): ?>
    <?php
      $statusConfig = [
        'ativo'    => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => '✅'],
        'rascunho' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => '📝'],
        'inativo'  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => '⏸'],
      ];
      $sc = $statusConfig[$c['status']] ?? $statusConfig['inativo'];
    ?>
    <div class="el-card curso-card bg-white rounded-2xl shadow-md overflow-hidden flex flex-col border border-gray-100"
         data-titulo="<?= htmlspecialchars(mb_strtolower($c['titulo'])) ?>"
         data-status="<?= htmlspecialchars($c['status']) ?>">

      <!-- Thumbnail -->
      <?php if (!empty($c['thumbnail'])): ?>
      <div class="h-40 overflow-hidden">
        <img src="<?= htmlspecialchars($c['thumbnail']) ?>" alt="<?= htmlspecialchars($c['titulo']) ?>"
          class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
      </div>
      <?php else: ?>
      <div class="el-thumb h-40 flex items-center justify-center">
        <span class="text-5xl text-white/80">🎓</span>
      </div>
      <?php endif; ?>

      <!-- Content -->
      <div class="p-5 flex flex-col flex-1">
        <div class="flex items-start justify-between gap-2 mb-2">
          <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2"><?= htmlspecialchars($c['titulo']) ?></h3>
          <span class="el-badge flex-shrink-0 px-2 py-0.5 rounded-full font-bold <?= $sc['bg'] ?> <?= $sc['text'] ?>">
            <?= $sc['icon'] ?> <?= strtoupper($c['status']) ?>
          </span>
        </div>

        <?php if ($c['descricao']): ?>
        <p class="text-xs text-gray-500 mb-3 line-clamp-2"><?= htmlspecialchars($c['descricao']) ?></p>
        <?php endif; ?>

        <!-- Stats row -->
        <div class="flex items-center gap-3 text-xs text-gray-400 mt-auto pt-3 border-t border-gray-100">
          <span class="flex items-center gap-1" title="Carga Horária">⏱ <?= (int)$c['carga_horaria'] ?> min</span>
          <span class="flex items-center gap-1" title="Aulas">📖 <?= (int)($c['total_aulas'] ?? 0) ?></span>
          <span class="flex items-center gap-1" title="Matrículas">👥 <?= (int)($c['total_matriculas'] ?? 0) ?></span>
        </div>

        <!-- Gestor -->
        <div class="text-xs text-gray-400 mt-2">
          por <span class="font-medium text-gray-500"><?= htmlspecialchars($c['gestor_nome'] ?? 'N/A') ?></span>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-gray-100">
          <a href="/elearning/gestor/cursos/<?= (int)$c['id'] ?>/aulas"
            class="text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition">
            📖 Aulas
          </a>
          <a href="/elearning/gestor/cursos/<?= (int)$c['id'] ?>/provas"
            class="text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 px-2.5 py-1 rounded-lg transition">
            📝 Provas
          </a>
          <a href="/elearning/gestor/cursos/<?= (int)$c['id'] ?>/matriculas"
            class="text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 px-2.5 py-1 rounded-lg transition">
            👥 Matrículas
          </a>
          <a href="/elearning/gestor/cursos/<?= (int)$c['id'] ?>/progresso"
            class="text-xs font-medium text-teal-600 bg-teal-50 hover:bg-teal-100 px-2.5 py-1 rounded-lg transition">
            📊 Progresso
          </a>
          <?php if ($canEdit): ?>
          <button onclick='editarCurso(<?= json_encode($c) ?>)'
            class="text-xs font-medium text-orange-600 bg-orange-50 hover:bg-orange-100 px-2.5 py-1 rounded-lg transition ml-auto">
            ✏️ Editar
          </button>
          <?php endif; ?>
          <?php if ($canDelete): ?>
          <button onclick='excluirCurso(<?= (int)$c["id"] ?>, "<?= htmlspecialchars(addslashes($c["titulo"])) ?>")'
            class="text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition">
            🗑 Excluir
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
// Inline form toggle
function toggleFormPanel() {
  const panel = document.getElementById('formPanel');
  const icon = document.getElementById('iconPlus');
  const txt = document.getElementById('btnNovoCursoText');
  if (panel.classList.contains('open')) {
    cancelarForm();
  } else {
    panel.classList.add('open');
    icon.style.transform = 'rotate(45deg)';
    txt.textContent = 'Fechar';
    document.getElementById('formPanelTitle').textContent = 'Criar Novo Curso';
    document.getElementById('formCurso').reset();
    document.getElementById('cursoId').value = '';
  }
}

function cancelarForm() {
  const panel = document.getElementById('formPanel');
  const icon = document.getElementById('iconPlus');
  const txt = document.getElementById('btnNovoCursoText');
  panel.classList.remove('open');
  icon.style.transform = 'rotate(0)';
  txt.textContent = 'Novo Curso';
  document.getElementById('formCurso').reset();
  document.getElementById('cursoId').value = '';
}

function editarCurso(c) {
  const panel = document.getElementById('formPanel');
  const icon = document.getElementById('iconPlus');
  const txt = document.getElementById('btnNovoCursoText');

  document.getElementById('formPanelTitle').textContent = 'Editar Curso: ' + c.titulo;
  document.getElementById('cursoId').value = c.id;
  document.getElementById('cursoTitulo').value = c.titulo;
  document.getElementById('cursoDescricao').value = c.descricao || '';
  document.getElementById('cursoCarga').value = c.carga_horaria || 0;
  document.getElementById('cursoStatus').value = c.status;

  panel.classList.add('open');
  icon.style.transform = 'rotate(45deg)';
  txt.textContent = 'Fechar';

  panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function salvarCurso() {
  const btn = document.getElementById('btnSalvar');
  btn.disabled = true; btn.innerHTML = '⏳ Salvando...';
  const form = document.getElementById('formCurso');
  const formData = new FormData(form);
  const id = document.getElementById('cursoId').value;
  const url = id ? '/elearning/gestor/cursos/update' : '/elearning/gestor/cursos/store';
  try {
    const res = await fetch(url, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
    else showToast('Erro: ' + data.message, 'error');
  } catch(e) { showToast('Erro de conexão', 'error'); }
  finally { btn.disabled = false; btn.innerHTML = '💾 Salvar Curso'; }
}

async function excluirCurso(id, titulo) {
  if (!confirm(`Excluir o curso "${titulo}"?\nEsta ação não pode ser desfeita.`)) return;
  const fd = new FormData(); fd.append('id', id);
  try {
    const res = await fetch('/elearning/gestor/cursos/delete', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 800); }
    else showToast('Erro: ' + d.message, 'error');
  } catch(e) { showToast('Erro de conexão', 'error'); }
}

// Search & Filter
function filtrarCursos() {
  const search = document.getElementById('searchCursos').value.toLowerCase();
  const status = document.getElementById('filterStatus').value;
  document.querySelectorAll('.curso-card').forEach(card => {
    const matchTitulo = card.dataset.titulo.includes(search);
    const matchStatus = !status || card.dataset.status === status;
    card.style.display = (matchTitulo && matchStatus) ? '' : 'none';
  });
}

// Toast notification
function showToast(msg, type) {
  const existing = document.getElementById('elToast');
  if (existing) existing.remove();
  const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-indigo-600' };
  const div = document.createElement('div');
  div.id = 'elToast';
  div.className = `fixed bottom-6 right-6 z-[100] ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium el-fade-in`;
  div.textContent = msg;
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3500);
}
</script>
