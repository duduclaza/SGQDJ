<!-- Modal Nova NC - REDESIGN WIDESCREEN -->
<div id="modalNovaNC" class="modal-overlay hidden">
  <div class="modal-content-widescreen">
    <!-- Header Premium -->
    <div class="modal-header-gradient">
      <div class="flex items-center gap-4">
        <div class="icon-badge-large">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-white">Nova Não Conformidade</h2>
          <p class="text-red-100 text-sm mt-0.5">Preencha os dados abaixo para registrar o apontamento</p>
        </div>
      </div>
      <button type="button" onclick="fecharModalNovaNC()" class="modal-close-btn">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <!-- Form Layout Grid -->
    <form id="formNovaNC" enctype="multipart/form-data" class="modal-body-grid">
      
      <!-- COLUNA ESQUERDA: Detalhes Principais -->
      <div class="grid-column-main">
        <!-- Card: Informações -->
        <div class="form-card h-full flex flex-col">
          <div class="form-card-header">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-semibold text-gray-900">O que aconteceu?</span>
          </div>
          
          <div class="space-y-5 flex-1 flex flex-col">
            <!-- Título -->
            <div class="form-group">
              <label class="form-label">
                <span>Título da Ocorrência</span>
                <span class="label-required">*</span>
              </label>
              <input type="text" name="titulo" required 
                     class="form-input-modern"
                     placeholder="Resuma o problema em poucas palavras">
            </div>
            
            <!-- Descrição -->
            <div class="form-group flex-1 flex flex-col">
              <label class="form-label">
                <span>Descrição Detalhada</span>
                <span class="label-required">*</span>
              </label>
              <textarea name="descricao" required 
                        class="form-textarea-modern flex-1"
                        placeholder="Descreva o problema detalhadamente..."></textarea>
            </div>
          </div>
        </div>
      </div>
      
      <!-- COLUNA DIREITA: Responsável e Evidências -->
      <div class="grid-column-sidebar space-y-4">
        
        <!-- Card: Responsável -->
        <div class="form-card">
          <div class="form-card-header">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-semibold text-gray-900">Quem vai resolver?</span>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <span>Responsável</span>
              <span class="label-required">*</span>
            </label>
            <div class="relative">
              <select name="responsavel_id" required class="form-select-modern">
                <option value="">Selecione...</option>
                <?php 
                if (!empty($usuarios)) {
                    foreach ($usuarios as $u): 
                ?>
                  <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                <?php 
                    endforeach;
                }
                ?>
              </select>
              <div class="select-arrow">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Card: Evidências -->
        <div class="form-card flex-1">
          <div class="form-card-header">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
            <span class="font-semibold text-gray-900">Evidências</span>
          </div>
          
          <div class="form-group h-full flex flex-col">
            <div class="upload-zone flex-1 flex flex-col justify-center" onclick="document.getElementById('inputAnexosNC').click()">
              <div class="upload-zone-content">
                <div class="upload-icon-wrapper">
                  <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                  </svg>
                </div>
                <div>
                  <p class="upload-title">Adicionar Anexos</p>
                  <p class="upload-subtitle">Fotos ou Documentos</p>
                </div>
              </div>
            </div>
            <input type="file" id="inputAnexosNC" name="anexos[]" multiple 
                   accept="image/*,application/pdf,video/mp4" 
                   class="hidden"
                   onchange="mostrarArquivosSelecionados(this)">
            <div id="listaArquivos" class="mt-3 space-y-2 hidden max-h-32 overflow-y-auto"></div>
          </div>
        </div>
        
      </div>
    </form>
    
    <!-- Footer -->
    <div class="modal-footer-modern">
      <button type="button" onclick="fecharModalNovaNC()" class="btn-secondary-modern">
        Cancelar
      </button>
      <button type="submit" form="formNovaNC" class="btn-primary-modern">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Confirmar Registro
      </button>
    </div>
  </div>
</div>

<!-- Modal Detalhes (Mantido) -->
<div id="modalDetalhes" class="modal-overlay hidden">
  <div class="modal-content modal-content-large bg-white rounded-xl shadow-2xl p-6 w-full max-w-4xl mx-auto my-auto relative">
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
/* ===== MODAL WIDESCREEN STYLES ===== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(8px);
  display: flex !important; /* Força display flex */
  align-items: center;
  justify-content: center; /* CORRIGIDO: Centralização horizontal */
  z-index: 999999;
  padding: 1rem;
}

.modal-overlay.hidden {
  display: none !important;
}

.modal-content-widescreen {
  background: #f3f4f6; /* Fundo cinza claro para contraste com cards brancos */
  border-radius: 20px;
  width: 95vw; /* Ocupa 95% da largura */
  max-width: 1400px;
  height: 90vh; /* Ocupa 90% da altura */
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  position: relative;
  animation: modalZoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalZoomIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

/* Header */
.modal-header-gradient {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  padding: 1.5rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.icon-badge-large {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-center;
}

.modal-close-btn {
  color: rgba(255, 255, 255, 0.8);
  transition: color 0.2s;
}
.modal-close-btn:hover { color: white; }

/* Grid Layout */
.modal-body-grid {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
  display: grid;
  grid-template-columns: 2fr 1fr; /* 2/3 para esquerda, 1/3 para direita */
  gap: 1.5rem;
}

.grid-column-main {
  display: flex;
  flex-direction: column;
}

.grid-column-sidebar {
  display: flex;
  flex-direction: column;
}

/* Cards */
.form-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.form-card-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

/* Inputs */
.form-group { margin-bottom: 0; }

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.label-required { color: #dc2626; margin-left: 2px; }

.form-input-modern,
.form-select-modern,
.form-textarea-modern {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.form-input-modern:focus,
.form-select-modern:focus,
.form-textarea-modern:focus {
  border-color: #dc2626;
  ring: 2px solid rgba(220, 38, 38, 0.1);
  outline: none;
}

.form-textarea-modern {
  resize: none;
  height: 100%;
  min-height: 200px;
}

/* Upload */
.upload-zone {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #f9fafb;
}

.upload-zone:hover {
  border-color: #dc2626;
  background: #fef2f2;
}

.upload-icon-wrapper {
  width: 48px;
  height: 48px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-center;
  margin: 0 auto 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.upload-icon { width: 24px; height: 24px; color: #dc2626; }

/* Footer */
.modal-footer-modern {
  background: white;
  padding: 1.25rem 2rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  flex-shrink: 0;
}

.btn-primary-modern {
  background: #dc2626;
  color: white;
  padding: 0.75rem 2rem;
  border-radius: 10px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s;
}
.btn-primary-modern:hover { background: #b91c1c; }

.btn-secondary-modern {
  background: white;
  color: #4b5563;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  border: 1px solid #d1d5db;
  transition: background 0.2s;
}
.btn-secondary-modern:hover { background: #f3f4f6; }

/* Mobile */
@media (max-width: 1024px) {
  .modal-body-grid {
    grid-template-columns: 1fr;
  }
  .modal-content-widescreen {
    height: auto;
    max-height: 95vh;
  }
}
</style>

<script>
function mostrarArquivosSelecionados(input) {
  const lista = document.getElementById('listaArquivos');
  lista.innerHTML = '';
  
  if (input.files.length > 0) {
    lista.classList.remove('hidden');
    Array.from(input.files).forEach((file) => {
      const fileItem = document.createElement('div');
      fileItem.className = 'flex items-center gap-2 p-2 bg-white border rounded-lg text-sm';
      fileItem.innerHTML = `
        <span class="text-gray-500">📎</span>
        <span class="truncate flex-1">${file.name}</span>
        <span class="text-xs text-gray-400">${(file.size/1024).toFixed(0)}KB</span>
      `;
      lista.appendChild(fileItem);
    });
  } else {
    lista.classList.add('hidden');
  }
}
</script>
