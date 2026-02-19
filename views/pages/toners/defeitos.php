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

    <form id="formDefeito" class="p-8 grid grid-cols-1 md:grid-cols-12 gap-6" novalidate>
      
      <!-- Linha 1: Pedido (3), Modelo (6), Qtd (3) -->
      
      <!-- Número do Pedido -->
      <div class="md:col-span-3 flex flex-col">
        <label for="numeroPedido" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Número do Pedido <span class="text-red-500">*</span></label>
        <div class="relative group">
          <input type="text" id="numeroPedido" name="numero_pedido"
            placeholder="Ex: 54321"
            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none placeholder-gray-400 group-hover:bg-white group-hover:border-gray-300">
        </div>
      </div>

      <!-- Modelo do Toner (Busca) -->
      <div class="md:col-span-6 flex flex-col">
        <label for="buscaToner" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Modelo do Toner <span class="text-red-500">*</span></label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input type="text" id="buscaToner"
            placeholder="Pesquise o modelo..."
            class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none placeholder-gray-400 group-hover:bg-white group-hover:border-gray-300"
            autocomplete="off">
            
          <!-- Select Oculto / Dropdown simulado -->
          <div id="dropdownToner" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
             <select id="selectToner" name="toner_id" size="5" class="w-full text-sm border-none focus:ring-0 p-1">
                <?php foreach ($toners_lista as $t): ?>
                  <option value="<?php echo (int)$t['id']; ?>" data-label="<?php echo htmlspecialchars($t['modelo']); ?>" class="p-2 hover:bg-red-50 rounded cursor-pointer">
                    <?php echo htmlspecialchars($t['modelo']); ?>
                  </option>
                <?php
endforeach; ?>
                <?php if (empty($toners_lista)): ?>
                  <option value="" disabled class="p-2 text-gray-400">Nenhum toner cadastrado</option>
                <?php
endif; ?>
             </select>
          </div>
        </div>
        <input type="hidden" id="modeloTonerHidden" name="modelo_toner">
      </div>

      <!-- Quantidade -->
      <div class="md:col-span-3 flex flex-col">
        <label for="quantidadeDefeito" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Qtd. <span class="text-red-500">*</span></label>
        <div class="relative group">
           <input type="number" id="quantidadeDefeito" name="quantidade" value="1" min="1"
            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-800 text-center focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none group-hover:bg-white group-hover:border-gray-300">
        </div>
      </div>

      <!-- Linha 2: Cliente (12) -->
      <div class="md:col-span-12 flex flex-col">
        <label for="buscaCliente" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Cliente <span class="text-red-500">*</span></label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
             <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </div>
          <input type="text" id="buscaCliente"
            placeholder="Pesquise por nome ou código do cliente..."
            class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm font-medium text-gray-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none placeholder-gray-400 group-hover:bg-white group-hover:border-gray-300"
            autocomplete="off">

           <!-- Select Oculto / Dropdown -->
           <div id="dropdownCliente" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
              <select id="selectCliente" name="cliente_id" size="5" class="w-full text-sm border-none focus:ring-0 p-1">
                <?php foreach ($clientes_lista as $c): ?>
                  <option value="<?php echo (int)$c['id']; ?>"
                    data-label="<?php echo htmlspecialchars($c['codigo'] . ' – ' . $c['nome']); ?>"
                    data-nome="<?php echo htmlspecialchars($c['nome']); ?>"
                    class="p-2 hover:bg-red-50 rounded cursor-pointer">
                    <?php echo htmlspecialchars($c['codigo'] . ' – ' . $c['nome']); ?>
                  </option>
                <?php
endforeach; ?>
                <?php if (empty($clientes_lista)): ?>
                  <option value="" disabled class="p-2 text-gray-400">Nenhum cliente cadastrado</option>
                <?php
endif; ?>
              </select>
           </div>
        </div>
        <input type="hidden" id="clienteNomeHidden" name="cliente_nome">
      </div>

      <!-- Linha 3: Descrição (12) -->
      <div class="md:col-span-12 flex flex-col">
        <label for="descricaoDefeito" class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Descrição do Defeito <span class="text-red-500">*</span></label>
        <textarea id="descricaoDefeito" name="descricao" rows="4"
          placeholder="Descreva detalhadamente o problema (ex: Manchas na lateral, ruído ao imprimir, etc)..."
          class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none resize-y placeholder-gray-400 hover:bg-white hover:border-gray-300"></textarea>
      </div>

      <!-- Linha 4: Fotos (12) -->
      <div class="md:col-span-12">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 block">Evidências Fotográficas <span class="text-gray-400 font-normal lowercase ml-1">(opcional, máx 3)</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="relative group">
            <label for="foto<?php echo $i; ?>"
              class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 bg-gray-50 rounded-xl h-32 cursor-pointer hover:border-red-400 hover:bg-red-50/50 transition-all duration-200" id="labelFoto<?php echo $i; ?>">
              
              <span id="previewFoto<?php echo $i; ?>" class="w-full h-full hidden rounded-xl overflow-hidden relative">
                <img id="imgPreview<?php echo $i; ?>" src="" alt="Prévia" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-white text-xs font-medium bg-black/50 px-2 py-1 rounded">Trocar</span>
                </div>
              </span>
              
              <span id="placeholderFoto<?php echo $i; ?>" class="flex flex-col items-center gap-1 text-gray-400 group-hover:text-red-400 transition-colors">
                <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-xs font-medium">Foto <?php echo $i; ?></span>
              </span>
            </label>
            <input type="file" id="foto<?php echo $i; ?>" name="foto<?php echo $i; ?>" accept="image/*" class="hidden" data-index="<?php echo $i; ?>">
            <button type="button" id="removerFoto<?php echo $i; ?>"
              class="hidden absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-md hover:bg-red-200 transition-colors"
                title="Remover foto"
              onclick="removerFoto(<?php echo $i; ?>)">
               <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <?php
endfor; ?>
        </div>
      </div>

      <!-- Botão -->
      <div class="md:col-span-12 flex justify-end pt-4 border-t border-gray-100 mt-2">
        <button type="submit" id="btnRegistrar"
          class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-bold rounded-lg shadow-lg shadow-red-500/20 transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Registrar Defeito
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
            <th class="px-4 py-3 text-center">Qtd</th>
            <th class="px-4 py-3 text-center">Evidências</th>
            <th class="px-4 py-3 text-left">Registrado por</th>
            <th class="px-4 py-3 text-center">Ações</th>
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
            <td class="px-4 py-3 text-center font-medium text-gray-700">
               <?php echo (int)($d['quantidade'] ?? 1); ?>
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
            <td class="px-4 py-3 text-center whitespace-nowrap">
              <button type="button" onclick="excluirDefeito(<?php echo $d['id']; ?>)"
                 class="text-red-500 hover:text-red-700 transition-colors" title="Excluir Registro">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
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
// Excluir Defeito
// =====================================================
async function excluirDefeito(id) {
  if (!confirm('Tem certeza que deseja excluir este registro? A ação não poderá ser desfeita.')) return;

  try {
    const resp = await fetch('/toners/defeitos/delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    });
    const data = await resp.json();

    if (data.success) {
      showToast('success', 'Excluído', data.message);
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast('error', 'Erro', data.message);
    }
  } catch (err) {
    showToast('error', 'Erro', 'Falha na conexão ao tentar excluir.');
  }
}

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
