<!-- Modal Nova NC - REDESIGN PREMIUM -->
<div id="modalNovaNC" class="modal-overlay hidden">
  <div class="modal-content-modern">
    <!-- Header Premium com Gradiente -->
    <div class="modal-header-gradient">
      <div class="flex items-center gap-4">
        <div class="icon-badge-large">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-white">Nova Não Conformidade</h2>
          <p class="text-red-100 text-sm mt-0.5">Registre um novo apontamento de qualidade</p>
        </div>
      </div>
      <button type="button" onclick="fecharModalNovaNC()" class="modal-close-btn">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <!-- Form com Cards -->
    <form id="formNovaNC" enctype="multipart/form-data" class="modal-body-modern">
      
      <!-- Card: Informações Básicas -->
      <div class="form-card">
        <div class="form-card-header">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-semibold text-gray-900">Informações Básicas</span>
        </div>
        
        <div class="space-y-4">
          <!-- Título -->
          <div class="form-group">
            <label class="form-label">
              <span class="label-icon">📝</span>
              <span>Título da NC</span>
              <span class="label-required">*</span>
            </label>
            <input type="text" name="titulo" required 
                   class="form-input-modern"
                   placeholder="Ex: Produto com defeito de fabricação">
            <p class="form-hint">Seja claro e objetivo no título</p>
          </div>
          
          <!-- Descrição -->
          <div class="form-group">
            <label class="form-label">
              <span class="label-icon">🗒️</span>
              <span>Descrição Detalhada</span>
              <span class="label-required">*</span>
            </label>
            <textarea name="descricao" required rows="5" 
                      class="form-textarea-modern"
                      placeholder="Descreva o problema encontrado com o máximo de detalhes possível...

• O que aconteceu?
• Quando foi detectado?
• Qual o impacto?
• Alguma observação adicional?"></textarea>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-xs text-gray-500">💡 Quanto mais detalhes, melhor!</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Card: Responsável -->
      <div class="form-card">
        <div class="form-card-header">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span class="font-semibold text-gray-900">Atribuição</span>
        </div>
        
        <div class="form-group">
          <label class="form-label">
            <span class="label-icon">👤</span>
            <span>Responsável pela Correção</span>
            <span class="label-required">*</span>
          </label>
          <div class="relative">
            <select name="responsavel_id" required class="form-select-modern">
              <option value="">Selecione o responsável...</option>
              <?php 
              if (empty($usuarios)) {
                  echo '<option value="" disabled>⚠️ Nenhum usuário encontrado</option>';
              } else {
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
          <p class="form-hint">Pessoa que irá resolver esta não conformidade</p>
        </div>
      </div>
      
      <!-- Card: Evidências -->
      <div class="form-card">
        <div class="form-card-header">
          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
          </svg>
          <span class="font-semibold text-gray-900">Evidências</span>
          <span class="text-xs text-gray-500 ml-auto">(Opcional)</span>
        </div>
        
        <div class="form-group">
          <label class="form-label">
            <span class="label-icon">📎</span>
            <span>Anexos</span>
          </label>
          <div class="upload-zone" onclick="document.getElementById('inputAnexosNC').click()">
            <div class="upload-zone-content">
              <div class="upload-icon-wrapper">
                <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
              </div>
              <div>
                <p class="upload-title">Clique para anexar ou arraste arquivos</p>
                <p class="upload-subtitle">Fotos, PDFs ou vídeos • Máx. 10MB por arquivo</p>
              </div>
              <div class="upload-formats">
                <span class="format-badge">📷 JPG</span>
                <span class="format-badge">📄 PDF</span>
                <span class="format-badge">🎥 MP4</span>
              </div>
            </div>
          </div>
          <input type="file" id="inputAnexosNC" name="anexos[]" multiple 
                 accept="image/*,application/pdf,video/mp4" 
                 class="hidden"
                 onchange="mostrarArquivosSelecionados(this)">
          <div id="listaArquivos" class="mt-3 space-y-2 hidden"></div>
        </div>
      </div>
      
      <!-- Botões Modernos -->
      <div class="modal-footer-modern">
        <button type="button" onclick="fecharModalNovaNC()" 
                class="btn-secondary-modern">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          Cancelar
        </button>
        <button type="submit" class="btn-primary-modern">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Criar Não Conformidade
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* ===== MODAL PREMIUM STYLES ===== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-center;
  z-index: 999999;
  padding: 1.5rem;
  overflow-y: auto;
}

.modal-overlay.hidden {
  display: none;
}

.modal-content-modern {
  background: white;
  border-radius: 24px;
  width: 100%;
  max-width: 800px;
  max-height: 95vh;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: flex;
  flex-direction: column;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-40px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Header com Gradiente */
.modal-header-gradient {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 3px solid rgba(255, 255, 255, 0.2);
}

.icon-badge-large {
  width: 56px;
  height: 56px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-center;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.modal-close-btn {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-center;
  color: white;
  transition: all 0.2s;
}

.modal-close-btn:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: rotate(90deg);
}

/* Body do Modal */
.modal-body-modern {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
}

/* Cards de Formulário */
.form-card {
  background: #f8fafc;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  transition: all 0.2s;
}

.form-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.form-card-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-bottom: 1rem;
  margin-bottom: 1.25rem;
  border-bottom: 2px solid #e2e8f0;
}

/* Form Groups */
.form-group {
  margin-bottom: 0;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.label-icon {
  font-size: 1.125rem;
}

.label-required {
  color: #ef4444;
  font-weight: 700;
}

/* Inputs Modernos */
.form-input-modern,
.form-textarea-modern,
.form-select-modern {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.9375rem;
  transition: all 0.2s;
  background: white;
}

.form-input-modern:focus,
.form-textarea-modern:focus,
.form-select-modern:focus {
  outline: none;
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.form-textarea-modern {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
  line-height: 1.6;
}

/* Select Custom */
.select-arrow {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.form-select-modern {
  appearance: none;
  padding-right: 3rem;
  cursor: pointer;
}

/* Form Hints */
.form-hint {
  font-size: 0.8125rem;
  color: #64748b;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

/* Upload Zone Premium */
.upload-zone {
  border: 3px dashed #cbd5e1;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
}

.upload-zone:hover {
  border-color: #ef4444;
  background: #fef2f2;
  transform: scale(1.01);
}

.upload-zone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.upload-icon-wrapper {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-center;
}

.upload-icon {
  width: 32px;
  height: 32px;
  color: #ef4444;
}

.upload-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

.upload-subtitle {
  font-size: 0.875rem;
  color: #64748b;
}

.upload-formats {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.format-badge {
  background: white;
  border: 1px solid #e2e8f0;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 500;
  color: #475569;
}

/* Footer com Botões */
.modal-footer-modern {
  padding: 1.5rem 2rem;
  border-top: 2px solid #f1f5f9;
  display: flex;
  gap: 1rem;
  background: #fafafa;
}

.btn-primary-modern,
.btn-secondary-modern {
  flex: 1;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9375rem;
  display: flex;
  align-items: center;
  justify-center;
  gap: 0.5rem;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
}

.btn-primary-modern {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-primary-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
}

.btn-secondary-modern {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary-modern:hover {
  background: #e2e8f0;
}

/* Scrollbar Custom */
.modal-body-modern::-webkit-scrollbar {
  width: 10px;
}

.modal-body-modern::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 10px;
}

.modal-body-modern::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.modal-body-modern::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Responsive */
@media (max-width: 640px) {
  .modal-content-modern {
    max-width: 100%;
    border-radius: 16px;
    max-height: 100vh;
  }
  
  .modal-header-gradient {
    padding: 1.5rem;
  }
  
  .modal-body-modern {
    padding: 1.5rem;
  }
  
  .form-card {
    padding: 1rem;
  }
  
  .modal-footer-modern {
    flex-direction: column-reverse;
    padding: 1rem 1.5rem;
  }
}
</style>

<script>
function mostrarArquivosSelecionados(input) {
  const lista = document.getElementById('listaArquivos');
  lista.innerHTML = '';
  
  if (input.files.length > 0) {
    lista.classList.remove('hidden');
    
    Array.from(input.files).forEach((file, index) => {
      const fileItem = document.createElement('div');
      fileItem.className = 'flex items-center gap-3 p-3 bg-white border-2 border-gray-200 rounded-lg';
      fileItem.innerHTML = `
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
          <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
        </div>
        <span class="text-green-600 text-sm font-medium">✓</span>
      `;
      lista.appendChild(fileItem);
    });
  } else {
    lista.classList.add('hidden');
  }
}
</script>
