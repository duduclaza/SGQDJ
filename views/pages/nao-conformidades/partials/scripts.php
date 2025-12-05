<script>
// Mudar aba
function mudarAba(nome) {
  document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
  document.getElementById('tab-' + nome).classList.add('active');
  document.getElementById('aba-' + nome).classList.remove('hidden');
}

// Mover modais para o body principal (funciona dentro de iframe)
document.addEventListener('DOMContentLoaded', function() {
  const modais = ['modalNovaNC', 'modalDetalhes', 'modalAcao'];
  modais.forEach(id => {
    const modal = document.getElementById(id);
    if (modal && modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
  });
});

// Modal Nova NC - Abrir o modal original (sem duplicação)
function abrirModalNovaNC() {
  console.log('🔴 Abrindo modal NovaNC');
  
  const modal = document.getElementById('modalNovaNC');
  if (!modal) {
    console.error('❌ Modal não encontrado!');
    alert('Erro: Modal não encontrado');
    return;
  }
  
  console.log('📦 Modal encontrado:', modal);
  console.log('📏 Classes antes:', modal.className);
  
  // Mostrar o modal - remover hidden E adicionar active
  modal.classList.remove('hidden');
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
  
  console.log('📏 Classes depois:', modal.className);
  
  // Verificar estilos computados
  const styles = window.getComputedStyle(modal);
  console.log('🎨 Display:', styles.display);
  console.log('🎨 Visibility:', styles.visibility);
  console.log('🎨 Opacity:', styles.opacity);
  
  console.log('✅ Modal aberto com sucesso!');
}

function fecharModalNovaNC() {
  const modal = document.getElementById('modalNovaNC');
  if (modal) {
    modal.classList.remove('active');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    // Resetar o formulário
    const form = document.getElementById('formNovaNC');
    if (form) form.reset();
    // Resetar o contador de anexos
    const textoAnexos = document.getElementById('textoAnexos');
    if (textoAnexos) {
      textoAnexos.textContent = 'Anexar Arquivos';
      textoAnexos.classList.remove('text-red-600', 'font-medium');
    }
  }
}

// Aguardar DOM carregar
document.addEventListener('DOMContentLoaded', function() {
  // Modal Nova NC - Fechar ao clicar fora
  document.getElementById('modalNovaNC')?.addEventListener('click', function(e) {
    if (e.target === this) fecharModalNovaNC();
  });
  
  // Modal Detalhes - Fechar ao clicar fora
  document.getElementById('modalDetalhes')?.addEventListener('click', function(e) {
    if (e.target === this) fecharModalDetalhes();
  });
  
  // Modal Ação - Fechar ao clicar fora
  document.getElementById('modalAcao')?.addEventListener('click', function(e) {
    if (e.target === this) fecharModalAcao();
  });
  
  // ESC para fechar qualquer modal
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      fecharModalNovaNC();
      fecharModalDetalhes();
      fecharModalAcao();
    }
  });
  
  // Criar NC - Submit do formulário
  const formNovaNC = document.getElementById('formNovaNC');
  if (formNovaNC) {
    formNovaNC.addEventListener('submit', async (e) => {
      e.preventDefault();
      console.log('📤 Enviando formulário NC...');
      
      // Botão pode estar fora do form (no footer), então busca pelo atributo form ou dentro do form
      let submitBtn = formNovaNC.querySelector('button[type="submit"]');
      if (!submitBtn) {
        submitBtn = document.querySelector('button[type="submit"][form="formNovaNC"]');
      }
      
      // Se ainda não achou (fallback), pega o último botão do footer
      if (!submitBtn) {
         submitBtn = document.querySelector('#modalNovaNC .modal-footer button[type="submit"]');
      }

      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Salvar';
      
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Salvando...';
      }
      
      try {
        const formData = new FormData(e.target);
        const res = await fetch('/nao-conformidades/criar', { method: 'POST', body: formData });
        
        console.log('📥 Resposta recebida:', res.status);
        
        const data = await res.json();
        console.log('📊 Dados:', data);
        
        if (data.success) {
          alert(data.message);
          fecharModalNovaNC();
          location.reload();
        } else {
          alert('Erro: ' + data.message);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (error) {
        console.error('❌ Erro ao criar NC:', error);
        alert('Erro ao criar NC: ' + error.message);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }
  
  // Registrar Ação - Submit do formulário
  const formAcao = document.getElementById('formAcao');
  if (formAcao) {
    formAcao.addEventListener('submit', async (e) => {
      e.preventDefault();
      const ncId = document.getElementById('acaoNcId').value;
      const formData = new FormData(e.target);
      const res = await fetch(`/nao-conformidades/registrar-acao/${ncId}`, { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert('Erro: ' + data.message);
      }
    });
  }
});

// Ver detalhes
async function verDetalhes(id) {
  console.log('🔍 Iniciando verDetalhes dinâmico para ID:', id);
  
  // Remover modal anterior se existir
  const modalAnterior = document.getElementById('modalDetalhesDinamico');
  if (modalAnterior) modalAnterior.remove();

  try {
    const res = await fetch(`/nao-conformidades/detalhes/${id}`);
    const data = await res.json();
    console.log('📦 Dados recebidos:', data);

    if (data.success) {
      const nc = data.nc;
      const anexos = data.anexos || [];
      const isAdmin = <?= json_encode($isAdmin || $isSuperAdmin) ?>;
      const userId = <?= $_SESSION['user_id'] ?>;
      const podeRegistrarAcao = (nc.usuario_responsavel_id == userId || isAdmin) && nc.status !== 'solucionada';
      const podeSolucionar = (nc.usuario_criador_id == userId || nc.usuario_responsavel_id == userId || isAdmin) && nc.status === 'em_andamento';
      
      let htmlContent = `
        <div class="space-y-4">
          <div class="flex items-center gap-2">
            <h3 class="text-2xl font-bold">${nc.titulo}</h3>
            <span class="px-3 py-1 rounded-full text-sm ${nc.status === 'pendente' ? 'bg-red-100 text-red-700' : (nc.status === 'em_andamento' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')}">
              ${nc.status === 'pendente' ? 'Pendente' : (nc.status === 'em_andamento' ? 'Em Andamento' : 'Solucionada')}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>ID:</strong> #${nc.id}</div>
            <div><strong>Criado em:</strong> ${new Date(nc.created_at).toLocaleString('pt-BR')}</div>
            <div><strong>Apontado por:</strong> ${nc.criador_nome}</div>
            <div><strong>Responsável:</strong> ${nc.responsavel_nome}</div>
          </div>
          <div class="border-t pt-4">
            <h4 class="font-semibold mb-2">Descrição:</h4>
            <p class="text-gray-700 whitespace-pre-wrap">${nc.descricao}</p>
          </div>`;
      
      if (nc.acao_corretiva) {
        htmlContent += `<div class="border-t pt-4 bg-green-50 p-4 rounded">
          <h4 class="font-semibold mb-2 text-green-800">✅ Ação Corretiva Registrada:</h4>
          <p class="text-gray-700 whitespace-pre-wrap">${nc.acao_corretiva}</p>
          <p class="text-sm text-gray-500 mt-2">Por: ${nc.acao_nome} em ${new Date(nc.data_acao).toLocaleString('pt-BR')}</p>
        </div>`;
      }
      
      if (anexos.length > 0) {
        htmlContent += '<div class="border-t pt-4"><h4 class="font-semibold mb-2">📎 Anexos:</h4><div class="space-y-2">';
        anexos.forEach(a => {
          htmlContent += `<div class="flex items-center gap-2 p-2 bg-gray-50 rounded"><span>${a.nome_arquivo}</span><a href="/nao-conformidades/anexo/${a.id}" class="text-blue-600 hover:underline text-sm">Download</a></div>`;
        });
        htmlContent += '</div></div>';
      }
      
      htmlContent += '<div class="flex gap-2 pt-4 border-t">';
      if (podeRegistrarAcao) {
        htmlContent += `<button onclick="abrirModalAcao(${nc.id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">✍️ Registrar Ação</button>`;
      }
      if (podeSolucionar) {
        htmlContent += `<button onclick="marcarSolucionada(${nc.id})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">✅ Marcar como Solucionada</button>`;
      }
      htmlContent += '</div></div>';

      // CRIAR MODAL DINAMICAMENTE
      const modalDiv = document.createElement('div');
      modalDiv.id = 'modalDetalhesDinamico';
      modalDiv.className = 'modal-overlay';
      
      // FORÇAR ESTILOS COM !IMPORTANT VIA setAttribute
      modalDiv.setAttribute('style', `
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background-color: rgba(0, 0, 0, 0.75) !important;
        z-index: 999999999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        pointer-events: auto !important;
        visibility: visible !important;
        opacity: 1 !important;
      `.trim());
      
      modalDiv.innerHTML = `
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 24px; width: 100%; max-width: 800px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 16px;">
              <h2 style="font-size: 1.5rem; font-weight: bold; color: #1f2937; margin: 0;">Detalhes da NC</h2>
              <button onclick="document.getElementById('modalDetalhesDinamico').remove(); document.body.style.overflow = '';" style="background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px;">
                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
            <div style="overflow-y: auto; max-height: 80vh;">
                ${htmlContent}
            </div>
        </div>
      `;

      // Fechar ao clicar fora
      modalDiv.onclick = function() {
          this.remove();
          document.body.style.overflow = '';
      };

      document.body.appendChild(modalDiv);
      document.body.style.overflow = 'hidden';
      
      // DEBUG: Verificar estilos computados
      const computedStyles = window.getComputedStyle(modalDiv);
      console.log('✅ Modal dinâmico inserido no DOM');
      console.log('📊 Estilos computados do modal:', {
        display: computedStyles.display,
        visibility: computedStyles.visibility,
        opacity: computedStyles.opacity,
        zIndex: computedStyles.zIndex,
        position: computedStyles.position,
        width: computedStyles.width,
        height: computedStyles.height,
        backgroundColor: computedStyles.backgroundColor
      });
      console.log('📍 Posição no DOM:', modalDiv.parentElement?.tagName, 'ID do modal:', modalDiv.id);

    } else {
      alert('Erro ao carregar detalhes: ' + (data.message || 'Erro desconhecido'));
    }
  } catch (error) {
    console.error('❌ Erro fatal ao ver detalhes:', error);
    alert('Erro ao carregar detalhes. Verifique o console.');
  }
}

// Função placeholder para compatibilidade caso algo chame fecharModalDetalhes
function fecharModalDetalhes() {
  const modal = document.getElementById('modalDetalhesDinamico');
  if(modal) {
    modal.remove();
    document.body.style.overflow = '';
  }
  // Tentar fechar o modal antigo também por precaução
  const modalAntigo = document.getElementById('modalDetalhes');
  if(modalAntigo && modalAntigo.classList.contains('hidden') === false) {
      modalAntigo.classList.add('hidden');
      modalAntigo.style.display = 'none';
  }
}

// Modal Ação
function abrirModalAcao(ncId) {
  document.getElementById('acaoNcId').value = ncId;
  fecharModalDetalhes();
  const modal = document.getElementById('modalAcao');
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}
function fecharModalAcao() {
  const modal = document.getElementById('modalAcao');
  modal.classList.add('hidden');
  document.getElementById('formAcao').reset();
  document.body.style.overflow = '';
}

// Mover para Em Andamento
async function moverParaEmAndamento(ncId) {
  if (!confirm('Deseja iniciar o tratamento desta NC? Ela será movida para "Em Andamento".')) return;
  
  try {
    const res = await fetch(`/nao-conformidades/mover-em-andamento/${ncId}`, { method: 'POST' });
    const data = await res.json();
    
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Erro: ' + data.message);
    }
  } catch (error) {
    console.error('Erro ao mover NC:', error);
    alert('Erro ao processar solicitação.');
  }
}

// Marcar como solucionada
async function marcarSolucionada(ncId) {
  // Se não for confirmado, cancela
  if (!confirm('Confirma que esta NC foi solucionada?')) return;
  
  try {
    const res = await fetch(`/nao-conformidades/marcar-solucionada/${ncId}`, { method: 'POST' });
    const data = await res.json();
    
    if (data.success) {
      alert(data.message);
      location.reload();
    } 
    // Se precisar de ação corretiva antes
    else if (data.needs_action) {
      alert(data.message);
      // Abrir modal de ação automaticamente
      abrirModalAcao(ncId);
    } 
    else {
      alert('Erro: ' + data.message);
    }
  } catch (error) {
    console.error('Erro ao marcar como solucionada:', error);
    alert('Erro ao processar solicitação.');
  }
}

// Excluir NC (apenas admins)
async function excluirNC(ncId, titulo) {
  if (!confirm(`Tem certeza que deseja excluir a NC #${ncId}?\n\n"${titulo}"\n\nEsta ação não pode ser desfeita e excluirá também todos os anexos.`)) {
    return;
  }
  
  try {
    const res = await fetch(`/nao-conformidades/excluir/${ncId}`, { method: 'POST' });
    const data = await res.json();
    
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Erro: ' + data.message);
    }
  } catch (error) {
    console.error('Erro ao excluir NC:', error);
    alert('Erro ao excluir NC: ' + error.message);
  }
}
</script>
