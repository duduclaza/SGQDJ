<?php
// views/pages/elearning/colaborador/meus_cursos.php
?>
<div class="space-y-6">
  <h1 class="text-2xl font-bold text-gray-900">� Cursos por Departamento</h1>

  <?php if (empty($departamentos)): ?>
  <div class="bg-white rounded-xl shadow p-10 text-center">
    <div class="text-4xl mb-4">�</div>
    <p class="text-gray-500">Nenhum departamento com cursos ativos encontrado.</p>
  </div>
  <?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <?php foreach ($departamentos as $dep): ?>
    <a href="/elearning/colaborador/departamento/<?= (int)$dep['id'] ?>" class="group block bg-white rounded-xl shadow hover:shadow-lg transition p-5 border border-gray-100 hover:border-blue-200">
      <div class="flex items-center justify-between mb-3">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xl font-bold">
          <?= mb_strtoupper(mb_substr($dep['nome'] ?? '', 0, 1)) ?>
        </div>
        <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
          <?= (int)($dep['cursos_ativos'] ?? 0) ?> curso(s)
        </span>
      </div>
      <h3 class="font-bold text-gray-900 text-sm mb-1 group-hover:text-blue-700 transition">
        <?= htmlspecialchars($dep['nome'] ?? '') ?>
      </h3>
      <p class="text-xs text-gray-500">
        <?= (int)($dep['total_matriculas'] ?? 0) ?> matrícula(s)
      </p>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
