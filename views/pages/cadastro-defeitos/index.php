<?php
$defeitos = $defeitos ?? [];
?>

<section class="mb-8">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">🧩 Cadastro de Defeitos</h1>
      <p class="text-gray-600 mt-1">Cadastre defeitos gerais com imagem e nome simples</p>
    </div>
    <button onclick="openFormModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg">
      + Novo Defeito
    </button>
  </div>

  <div id="formContainer" class="hidden bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-lg font-semibold text-gray-900" id="formTitle">Novo Defeito</h2>
      <button onclick="closeFormModal()" class="text-gray-400 hover:text-gray-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form id="defeitoForm" class="space-y-4" enctype="multipart/form-data">
      <input type="hidden" name="id" id="defeitoId">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Defeito *</label>
        <input type="text" name="nome_defeito" id="nomeDefeito" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Imagem do Defeito <span id="img-obrigatoria" class="text-red-500">*</span></label>
        <input type="file" name="imagem" id="imagemDefeito" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">No modo edição, envie uma nova imagem apenas se quiser substituir.</p>
      </div>

      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeFormModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvar</button>
      </div>
    </form>
  </div>

  <div class="bg-white border rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagem</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome do Defeito</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Criado por</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php if (empty($defeitos)): ?>
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-gray-400">Nenhum defeito cadastrado.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($defeitos as $d): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?= (int)$d['id'] ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <?php if (!empty($d['imagem_nome'])): ?>
                    <a href="/cadastro-defeitos/<?= (int)$d['id'] ?>/imagem" target="_blank">
                      <img src="/cadastro-defeitos/<?= (int)$d['id'] ?>/imagem" alt="Imagem defeito" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                    </a>
                  <?php else: ?>
                    <span class="text-gray-400">Sem imagem</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($d['nome_defeito'] ?? '') ?></td>
                <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($d['criado_por_nome'] ?? 'N/A') ?></td>
                <td class="px-6 py-4 text-sm text-gray-500"><?= !empty($d['created_at']) ? date('d/m/Y', strtotime($d['created_at'])) : '—' ?></td>
                <td class="px-6 py-4 text-sm space-x-2">
                  <button onclick='editDefeito(<?= json_encode($d) ?>)' class="text-blue-600 hover:text-blue-800">✏️ Editar</button>
                  <button onclick="deleteDefeito(<?= (int)$d['id'] ?>)" class="text-red-600 hover:text-red-800">🗑️ Excluir</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
let isEditingDefeito = false;

function openFormModal() {
  document.getElementById('formContainer').classList.remove('hidden');
  document.getElementById('formTitle').textContent = 'Novo Defeito';
  document.getElementById('img-obrigatoria').classList.remove('hidden');
  document.getElementById('defeitoForm').reset();
  document.getElementById('defeitoId').value = '';
  isEditingDefeito = false;
}

function closeFormModal() {
  document.getElementById('formContainer').classList.add('hidden');
  document.getElementById('defeitoForm').reset();
}

function editDefeito(defeito) {
  document.getElementById('formContainer').classList.remove('hidden');
  document.getElementById('formTitle').textContent = 'Editar Defeito';
  document.getElementById('img-obrigatoria').classList.add('hidden');
  document.getElementById('defeitoId').value = defeito.id;
  document.getElementById('nomeDefeito').value = defeito.nome_defeito || '';
  isEditingDefeito = true;
}

async function deleteDefeito(id) {
  if (!confirm('Tem certeza que deseja excluir este defeito?')) return;

  try {
    const response = await fetch('/cadastro-defeitos/delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: `id=${id}`
    });

    const raw = await response.text();
    let result;
    try {
      result = JSON.parse(raw);
    } catch (_) {
      throw new Error(raw || `HTTP ${response.status}`);
    }

    alert(result.message);
    if (result.success) window.location.reload();
  } catch (error) {
    alert('Erro ao excluir defeito: ' + (error.message || 'erro desconhecido'));
  }
}

document.getElementById('defeitoForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  const url = isEditingDefeito ? '/cadastro-defeitos/update' : '/cadastro-defeitos/store';

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const raw = await response.text();
    let result;
    try {
      result = JSON.parse(raw);
    } catch (_) {
      throw new Error(raw || `HTTP ${response.status}`);
    }

    alert(result.message);
    if (result.success) window.location.reload();
  } catch (error) {
    alert('Erro ao salvar defeito: ' + (error.message || 'erro desconhecido'));
  }
});
</script>
