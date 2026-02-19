<?php
/**
 * Toners com Defeito — View completa
 * Variáveis disponíveis: $toners_lista, $clientes_lista, $defeitos_historico
 */
?>

<section class="space-y-6">

  <!-- Cabeçalho -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900">Toners com Defeito</h1>
      <p class="text-sm text-gray-500 mt-0.5">Registre toners com defeito identificados e notifique os administradores automaticamente.</p>
    </div>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
      <?php echo count($defeitos_historico ?? []); ?> registros
    </span>
  </div>

  <!-- ======================= FORMULÁRIO ======================= -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-2">
      <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
      <h2 class="text-base font-semibold text-red-800">Registrar Toner com Defeito</h2>
    </div>

    <form id="formDefeito" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-5" novalidate>

      <!-- Modelo do Toner -->
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-medium text-gray-700">Modelo do Toner <span class="text-red-500">*</span></label>
        <!-- Campo de busca -->
        <div class="relative">
          <input type="text" id="buscaToner"
            placeholder="🔍  Digite para filtrar..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none mb-1"
            autocomplete="off">
          <select id="selectToner" name="toner_id" size="5"
            class="w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none appearance-none"
            style="padding: 4px;">
            <?php foreach ($toners_lista as $t): ?>
              <option value="<?php echo (int)$t['id']; ?>" data-label="<?php echo htmlspecialchars($t['modelo']); ?>">
                <?php echo htmlspecialchars($t['modelo']); ?>
              </option>
            <?php
endforeach; ?>
            <?php if (empty($toners_lista)): ?>
              <option value="" disabled>Nenhum toner cadastrado</option>
            <?php
endif; ?>
          </select>
        </div>
        <!-- Campo oculto com o nome do modelo selecionado -->
        <input type="hidden" id="modeloTonerHidden" name="modelo_toner">
        <p class="text-xs text-gray-400 mt-0.5">Selecione o modelo na lista acima</p>
      </div>

      <!-- Número do Pedido -->
      <div class="flex flex-col gap-1.5">
        <label for="numeroPedido" class="text-sm font-medium text-gray-700">Número do Pedido <span class="text-red-500">*</span></label>
        <input type="text" id="numeroPedido" name="numero_pedido"
          placeholder="Ex: 2024-00123"
          class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none">
      </div>

      <!-- Cliente -->
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-medium text-gray-700">Cliente <span class="text-red-500">*</span></label>
        <div class="relative">
          <input type="text" id="buscaCliente"
            placeholder="🔍  Digite para filtrar..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none mb-1"
            autocomplete="off">
          <select id="selectCliente" name="cliente_id" size="5"
            class="w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none appearance-none"
            style="padding: 4px;">
            <?php foreach ($clientes_lista as $c): ?>
              <option value="<?php echo (int)$c['id']; ?>"
                data-label="<?php echo htmlspecialchars($c['codigo'] . ' – ' . $c['nome']); ?>"
                data-nome="<?php echo htmlspecialchars($c['nome']); ?>">
                <?php echo htmlspecialchars($c['codigo'] . ' – ' . $c['nome']); ?>
              </option>
            <?php
endforeach; ?>
            <?php if (empty($clientes_lista)): ?>
              <option value="" disabled>Nenhum cliente cadastrado</option>
            <?php
endif; ?>
          </select>
        </div>
        <input type="hidden" id="clienteNomeHidden" name="cliente_nome">
        <p class="text-xs text-gray-400 mt-0.5">Selecione o cliente na lista acima</p>
      </div>

      <!-- Descrição do Defeito -->
      <div class="flex flex-col gap-1.5">
        <label for="descricaoDefeito" class="text-sm font-medium text-gray-700">Descrição do Defeito <span class="text-red-500">*</span></label>
        <textarea id="descricaoDefeito" name="descricao" rows="5"
          placeholder="Descreva o defeito identificado no toner (sintomas, quando foi detectado, impacto, etc.)..."
          class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent outline-none resize-none"></textarea>
      </div>

      <!-- Fotos de Evidência (span full em telas grandes) -->
      <div class="lg:col-span-2 flex flex-col gap-2">
        <label class="text-sm font-medium text-gray-700">Fotos de Evidência <span class="text-gray-400 font-normal">(máximo 3 imagens)</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="flex flex-col gap-2">
            <label for="foto<?php echo $i; ?>"
              class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl p-5 cursor-pointer hover:border-red-400 hover:bg-red-50 transition-colors" id="labelFoto<?php echo $i; ?>">
              <span id="previewFoto<?php echo $i; ?>" class="w-full hidden">
                <img id="imgPreview<?php echo $i; ?>" src="" alt="Prévia" class="max-h-28 mx-auto rounded-lg object-cover">
              </span>
              <span id="placeholderFoto<?php echo $i; ?>" class="flex flex-col items-center gap-1 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-xs text-center">Foto <?php echo $i; ?><br><span class="text-gray-300">Clique para selecionar</span></span>
              </span>
            </label>
            <input type="file" id="foto<?php echo $i; ?>" name="foto<?php echo $i; ?>"
              accept="image/*" class="hidden" data-index="<?php echo $i; ?>">
            <button type="button" id="removerFoto<?php echo $i; ?>"
              class="hidden text-xs text-red-500 hover:text-red-700 underline self-center"
              onclick="removerFoto(<?php echo $i; ?>)">Remover foto <?php echo $i; ?></button>
          </div>
          <?php
endfor; ?>
        </div>
        <p class="text-xs text-gray-400">Aceitamos: JPG, PNG, WEBP, GIF ‒ máximo 16 MB por foto</p>
      </div>

      <!-- Botão -->
      <div class="lg:col-span-2 pt-2">
        <button type="submit" id="btnRegistrar"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Registrar Toner com Defeito
        </button>
      </div>

    </form>
  </div>

  <!-- ======================= HISTÓRICO ======================= -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h2 class="text-base font-semibold text-gray-800">Histórico de Registros</h2>
      <span class="text-sm text-gray-400"><?php echo count($defeitos_historico ?? []); ?> registro(s)</span>
    </div>

    <?php if (empty($defeitos_historico)): ?>
    <div class="py-14 text-center">
      <p class="text-gray-400 text-sm">Nenhum toner com defeito registrado ainda.</p>
    </div>
    <?php
else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
            <th class="px-4 py-3 text-left">Data</th>
            <th class="px-4 py-3 text-left">Modelo</th>
            <th class="px-4 py-3 text-left">Nº Pedido</th>
            <th class="px-4 py-3 text-left">Cliente</th>
            <th class="px-4 py-3 text-left">Descrição</th>
            <th class="px-4 py-3 text-center">Evidências</th>
            <th class="px-4 py-3 text-left">Registrado por</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($defeitos_historico as $d): ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
              <?php echo date('d/m/Y H:i', strtotime($d['created_at'])); ?>
            </td>
            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
              <?php echo htmlspecialchars($d['modelo_toner']); ?>
            </td>
            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
              <?php echo htmlspecialchars($d['numero_pedido']); ?>
            </td>
            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
              <?php echo htmlspecialchars($d['cliente_nome']); ?>
            </td>
            <td class="px-4 py-3 text-gray-600 max-w-xs">
              <span title="<?php echo htmlspecialchars($d['descricao']); ?>">
                <?php echo htmlspecialchars(mb_strimwidth($d['descricao'], 0, 80, '…')); ?>
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center gap-1.5">
                <?php for ($n = 1; $n <= 3; $n++): ?>
                  <?php $fotoNome = $d["foto{$n}_nome"]; ?>
                  <?php if (!empty($fotoNome)): ?>
                  <a href="/toners/defeitos/<?php echo (int)$d['id']; ?>/foto/<?php echo $n; ?>"
                     target="_blank"
                     title="<?php echo htmlspecialchars($fotoNome); ?>"
                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  </a>
                  <?php
      else: ?>
                  <span class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                  </span>
                  <?php
      endif; ?>
                <?php
    endfor; ?>
              </div>
            </td>
            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
              <?php echo htmlspecialchars($d['registrado_por_nome'] ?? '—'); ?>
            </td>
          </tr>
          <?php
  endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
endif; ?>
  </div>

</section>

<!-- Toast de feedback -->
<div id="toastDefeito"
  class="fixed bottom-6 right-6 z-50 hidden max-w-sm bg-white border rounded-xl shadow-lg px-5 py-4 flex items-start gap-3 transition-all duration-300">
  <span id="toastIconDefeito" class="mt-0.5 shrink-0 w-5 h-5"></span>
  <div>
    <p id="toastTituloDefeito" class="text-sm font-semibold text-gray-800"></p>
    <p id="toastMsgDefeito" class="text-sm text-gray-500 mt-0.5"></p>
  </div>
</div>

<script>
// =====================================================
// Listbox com busca — Toner
// =====================================================
const buscaToner   = document.getElementById('buscaToner');
const selectToner  = document.getElementById('selectToner');
const modeloHidden = document.getElementById('modeloTonerHidden');

// Pré-selecionar primeiro item
if (selectToner.options.length > 0) {
  selectToner.selectedIndex = 0;
  modeloHidden.value = selectToner.options[0]?.dataset.label ?? '';
}

buscaToner.addEventListener('input', () => {
  const q = buscaToner.value.toLowerCase();
  Array.from(selectToner.options).forEach(opt => {
    opt.style.display = opt.dataset.label?.toLowerCase().includes(q) ? '' : 'none';
  });
});

selectToner.addEventListener('change', () => {
  const opt = selectToner.options[selectToner.selectedIndex];
  modeloHidden.value = opt?.dataset.label ?? '';
});

// =====================================================
// Listbox com busca — Cliente
// =====================================================
const buscaCliente  = document.getElementById('buscaCliente');
const selectCliente = document.getElementById('selectCliente');
const clienteNomeH  = document.getElementById('clienteNomeHidden');

if (selectCliente.options.length > 0) {
  selectCliente.selectedIndex = 0;
  clienteNomeH.value = selectCliente.options[0]?.dataset.nome ?? '';
}

buscaCliente.addEventListener('input', () => {
  const q = buscaCliente.value.toLowerCase();
  Array.from(selectCliente.options).forEach(opt => {
    opt.style.display = opt.dataset.label?.toLowerCase().includes(q) ? '' : 'none';
  });
});

selectCliente.addEventListener('change', () => {
  const opt = selectCliente.options[selectCliente.selectedIndex];
  clienteNomeH.value = opt?.dataset.nome ?? '';
});

// =====================================================
// Preview de fotos
// =====================================================
[1, 2, 3].forEach(i => {
  const input   = document.getElementById('foto' + i);
  const preview = document.getElementById('previewFoto' + i);
  const img     = document.getElementById('imgPreview' + i);
  const holder  = document.getElementById('placeholderFoto' + i);
  const btnRem  = document.getElementById('removerFoto' + i);

  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      showToast('error', 'Arquivo inválido', 'Apenas imagens são permitidas.');
      input.value = '';
      return;
    }
    if (file.size > 16 * 1024 * 1024) {
      showToast('error', 'Arquivo muito grande', 'Máximo 16 MB por foto.');
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      preview.classList.remove('hidden');
      holder.classList.add('hidden');
      btnRem.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
  });
});

function removerFoto(i) {
  const input   = document.getElementById('foto' + i);
  const preview = document.getElementById('previewFoto' + i);
  const img     = document.getElementById('imgPreview' + i);
  const holder  = document.getElementById('placeholderFoto' + i);
  const btnRem  = document.getElementById('removerFoto' + i);
  input.value   = '';
  img.src       = '';
  preview.classList.add('hidden');
  holder.classList.remove('hidden');
  btnRem.classList.add('hidden');
}

// =====================================================
// Envio do formulário
// =====================================================
document.getElementById('formDefeito').addEventListener('submit', async (e) => {
  e.preventDefault();

  const btn = document.getElementById('btnRegistrar');

  // Validação cliente e toner selecionados
  modeloHidden.value = selectToner.options[selectToner.selectedIndex]?.dataset.label ?? modeloHidden.value;
  clienteNomeH.value = selectCliente.options[selectCliente.selectedIndex]?.dataset.nome ?? clienteNomeH.value;

  if (!modeloHidden.value) {
    showToast('error', 'Campo obrigatório', 'Selecione um modelo de toner na lista.'); return;
  }
  if (!document.getElementById('numeroPedido').value.trim()) {
    showToast('error', 'Campo obrigatório', 'Informe o número do pedido.'); return;
  }
  if (!clienteNomeH.value) {
    showToast('error', 'Campo obrigatório', 'Selecione um cliente na lista.'); return;
  }
  if (!document.getElementById('descricaoDefeito').value.trim()) {
    showToast('error', 'Campo obrigatório', 'Informe a descrição do defeito.'); return;
  }

  btn.disabled = true;
  btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Registrando...';

  try {
    const fd = new FormData(e.target);
    // Garantir que os campos hidden estejam no FormData
    fd.set('modelo_toner', modeloHidden.value);
    fd.set('cliente_nome', clienteNomeH.value);

    const resp = await fetch('/toners/defeitos/store', { method: 'POST', body: fd });
    const data = await resp.json();

    if (data.success) {
      showToast('success', 'Registrado!', data.message ?? 'Toner com defeito registrado com sucesso.');
      e.target.reset();
      // Resetar previews
      [1, 2, 3].forEach(i => removerFoto(i));
      // Resetar listboxes
      selectToner.selectedIndex  = 0;
      selectCliente.selectedIndex = 0;
      modeloHidden.value  = selectToner.options[0]?.dataset.label ?? '';
      clienteNomeH.value  = selectCliente.options[0]?.dataset.nome ?? '';
      // Recarregar histórico após 1s
      setTimeout(() => location.reload(), 2000);
    } else {
      showToast('error', 'Erro ao registrar', data.message ?? 'Tente novamente.');
    }
  } catch (err) {
    showToast('error', 'Erro de conexão', 'Não foi possível enviar o formulário.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Registrar Toner com Defeito';
  }
});

// =====================================================
// Toast helper
// =====================================================
function showToast(type, titulo, msg) {
  const toast = document.getElementById('toastDefeito');
  const icon  = document.getElementById('toastIconDefeito');
  const tit   = document.getElementById('toastTituloDefeito');
  const txt   = document.getElementById('toastMsgDefeito');

  tit.textContent = titulo;
  txt.textContent = msg;

  if (type === 'success') {
    toast.className = toast.className.replace(/border-\S+/, '');
    toast.classList.add('border-green-200');
    icon.innerHTML  = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
  } else {
    toast.classList.remove('border-green-200');
    toast.classList.add('border-red-200');
    icon.innerHTML  = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>';
  }

  toast.classList.remove('hidden');
  setTimeout(() => toast.classList.add('hidden'), 5000);
}
</script>
