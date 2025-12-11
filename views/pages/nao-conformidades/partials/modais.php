<!-- Modal Nova NC - COMPACTO & MODERNO -->
<div id="modalNovaNC" class="modal-overlay hidden">
  <div class="modal-content-compact">
    
    <!-- Header Compacto -->
    <div class="modal-header">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-lg font-bold text-gray-900">Nova Não Conformidade</h2>
          <p class="text-xs text-gray-500">Preencha os dados da ocorrência</p>
        </div>
      </div>
      
      <!-- Botão Fechar (X) -->
      <button type="button" onclick="fecharModalNovaNC()" class="close-button" title="Fechar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <!-- Form Compacto -->
    <form id="formNovaNC" enctype="multipart/form-data" class="modal-body">
      <div class="space-y-4">
        
        <!-- Título -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Título da Ocorrência <span class="text-red-500">*</span></label>
          <input type="text" name="titulo" required 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm"
                 placeholder="Ex: Produto com defeito na embalagem">
        </div>

        <!-- Grid: Notificar, Responsável, Departamento e Anexos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Notificar (antigo Responsável - select de usuários) -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notificar <span class="text-red-500">*</span></label>
            <select name="responsavel_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm bg-white">
              <option value="">Selecione quem notificar...</option>
              <?php if (!empty($usuarios)): foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
              <?php endforeach; endif; ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Usuário que será notificado por e-mail</p>
          </div>

          <!-- Responsável (novo campo texto livre) -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Responsável <span class="text-red-500">*</span></label>
            <input type="text" name="responsavel_nome" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm"
                   placeholder="Nome do responsável pela ação">
            <p class="text-xs text-gray-500 mt-1">Quem é responsável por resolver</p>
          </div>

          <!-- Departamento -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Departamento <span class="text-red-500">*</span></label>
            <select name="departamento_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm bg-white">
              <option value="">Selecione...</option>
              <?php if (!empty($departamentos)): foreach ($departamentos as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nome']) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <!-- Botão Upload Compacto -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Evidências</label>
            <div class="relative">
              <input type="file" id="inputAnexosNC" name="anexos[]" multiple accept="image/*,application/pdf" class="hidden" onchange="atualizarContadorArquivos(this)">
              <button type="button" onclick="document.getElementById('inputAnexosNC').click()" 
                      class="w-full px-3 py-2 border border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-red-500 hover:text-red-600 transition-all flex items-center justify-center gap-2 bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span id="textoAnexos">Anexar Arquivos</span>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Descrição -->
        <div class="flex-1 flex flex-col">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição Detalhada <span class="text-red-500">*</span></label>
          <textarea name="descricao" required rows="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm resize-none"
                    placeholder="Descreva o problema..."></textarea>
        </div>

      </div>
    </form>
    
    <!-- Footer -->
    <div class="modal-footer">
      <button type="button" onclick="fecharModalNovaNC()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
        Cancelar
      </button>
      <button type="submit" form="formNovaNC" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2">
        <span>Salvar NC</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </button>
    </div>
  </div>
</div>

<!-- Modal Detalhes (Mantido) -->
<div id="modalDetalhes" class="modal-overlay hidden">
  <div class="modal-content bg-white rounded-xl shadow-2xl p-6 w-full max-w-4xl mx-auto my-auto relative">
    <div class="flex justify-between items-start mb-4 border-b pb-4">
      <h2 class="text-2xl font-bold text-gray-800">Detalhes da NC</h2>
      <button onclick="fecharModalDetalhes()" class="text-gray-400 hover:text-red-500 transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>
    <div id="conteudoDetalhes" class="overflow-y-auto max-h-[80vh]"></div>
  </div>
</div>

<!-- Modal Ação (Mantido) -->
<div id="modalAcao" class="modal-overlay hidden">
  <div class="modal-content bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg mx-auto my-auto">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <span class="text-green-600">✍️</span> Registrar Ação Corretiva
    </h2>
    <form id="formAcao" enctype="multipart/form-data">
      <input type="hidden" name="nc_id" id="acaoNcId">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Ação Corretiva *</label>
          <textarea name="acao_corretiva" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Descreva a ação que será tomada..."></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Evidências da Ação</label>
          <input type="file" name="anexos[]" multiple accept="image/*,application/pdf" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
        </div>
      </div>
      <div class="flex gap-3 mt-6">
        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium shadow-lg shadow-green-500/30">Registrar Ação</button>
        <button type="button" onclick="fecharModalAcao()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<style>
/* ===== ESTILOS COMPACTOS ===== */
.modal-overlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  background: rgba(0, 0, 0, 0.5) !important;
  backdrop-filter: blur(4px);
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  z-index: 9999999 !important;
  padding: 1rem;
}

.modal-overlay.hidden {
  display: none !important;
}

.modal-content-compact {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 650px;
  max-height: 90vh;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  overflow: hidden;
  margin: auto;
  position: relative;
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

/* Header */
.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
}

.close-button {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: all 0.2s;
  background: transparent;
  border: none;
  cursor: pointer;
}

.close-button:hover {
  background: #f3f4f6;
  color: #ef4444;
}

/* Body */
.modal-body {
  padding: 1rem;
  overflow-y: auto;
  max-height: calc(90vh - 140px);
  flex: 1;
}

/* Footer - sempre visível */
.modal-footer {
  padding: 1rem 1.25rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  flex-shrink: 0;
}

/* Garantir que modais no container global fiquem acima de tudo */
#global-modals-container .modal-overlay {
  position: fixed !important;
  z-index: 9999999 !important;
}
</style>

<script>
function atualizarContadorArquivos(input) {
  const span = document.getElementById('textoAnexos');
  if (input.files.length > 0) {
    span.textContent = `${input.files.length} arquivo(s) selecionado(s)`;
    span.classList.add('text-red-600', 'font-medium');
  } else {
    span.textContent = 'Anexar Arquivos';
    span.classList.remove('text-red-600', 'font-medium');
  }
}
</script>
