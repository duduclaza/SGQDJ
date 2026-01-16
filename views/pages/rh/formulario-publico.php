<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($formulario['titulo']); ?> - Avaliação RH</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-800 min-h-screen py-8 px-4 font-sans">
  <div class="max-w-2xl mx-auto">
    <!-- Card do Formulário -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden relative">
      <!-- Decorativo de fundo do card -->
      <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-500"></div>

      <!-- Logo Centralizado (Igual Login) -->
      <div class="text-center pt-8 pb-4">
        <img src="/assets/logodj.png" alt="DJ Logo" class="mx-auto h-16 object-contain">
      </div>

      <!-- Header com Título -->
      <div class="px-6 pb-2 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($formulario['titulo']); ?></h1>
        <p class="text-gray-500 text-sm">Formulário de Avaliação - RH</p>
      </div>

      <!-- Descrição -->
      <?php if (!empty($formulario['descricao'])): ?>
      <div class="px-6 py-2">
        <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm text-center">
          <?php echo htmlspecialchars($formulario['descricao']); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Formulário -->
      <form id="formAvaliacao" class="p-6 space-y-6">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($formulario['url_publica']); ?>">
        
        <!-- Informações do Avaliador -->
        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
          <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <span>👤</span> Identificação
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Colaborador Avaliado *</label>
              <input type="text" name="colaborador_nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Avaliador *</label>
              <input type="text" name="avaliador_nome" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
        </div>

        <!-- Perguntas -->
        <div class="space-y-4">
          <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <span>📝</span> Perguntas da Avaliação
          </h3>
          
          <?php foreach ($formulario['perguntas'] as $index => $pergunta): ?>
          <div class="border border-gray-200 rounded-lg p-4 bg-white">
            <label class="block text-sm font-medium text-gray-900 mb-3">
              <?php echo ($index + 1) . '. ' . htmlspecialchars($pergunta['texto']); ?>
              <?php if ($pergunta['obrigatoria']): ?><span class="text-red-500">*</span><?php endif; ?>
            </label>
            
            <?php if ($pergunta['tipo'] === 'texto'): ?>
            <textarea name="resposta_<?php echo $pergunta['id']; ?>" rows="2" 
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
              <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>></textarea>
            
            <?php elseif ($pergunta['tipo'] === 'numero' || $pergunta['tipo'] === 'escala_1_10'): ?>
            <div class="flex items-center gap-4">
              <input type="range" name="resposta_<?php echo $pergunta['id']; ?>" 
                min="0" max="10" value="5" 
                class="flex-1" 
                oninput="document.getElementById('valor_<?php echo $pergunta['id']; ?>').textContent = this.value"
                <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>>
              <span id="valor_<?php echo $pergunta['id']; ?>" class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">5</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
              <span>0 - Muito Ruim</span>
              <span>10 - Excelente</span>
            </div>
            
            <?php elseif ($pergunta['tipo'] === 'escala_1_5'): ?>
            <div class="flex items-center gap-2">
              <?php for ($i = 1; $i <= 5; $i++): ?>
              <label class="flex-1">
                <input type="radio" name="resposta_<?php echo $pergunta['id']; ?>" value="<?php echo $i; ?>" 
                  class="sr-only peer" <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>>
                <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition-colors">
                  <div class="text-lg font-bold"><?php echo $i; ?></div>
                </div>
              </label>
              <?php endfor; ?>
            </div>
            
            <?php elseif ($pergunta['tipo'] === 'sim_nao'): ?>
            <div class="flex gap-4">
              <label class="flex-1">
                <input type="radio" name="resposta_<?php echo $pergunta['id']; ?>" value="Sim" 
                  class="sr-only peer" <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>>
                <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-600 peer-checked:bg-green-50 hover:border-green-300 transition-colors">
                  <span class="text-lg">✅</span>
                  <div class="font-medium">Sim</div>
                </div>
              </label>
              <label class="flex-1">
                <input type="radio" name="resposta_<?php echo $pergunta['id']; ?>" value="Não" 
                  class="sr-only peer" <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>>
                <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-600 peer-checked:bg-red-50 hover:border-red-300 transition-colors">
                  <span class="text-lg">❌</span>
                  <div class="font-medium">Não</div>
                </div>
              </label>
            </div>
            
            <?php else: ?>
            <input type="text" name="resposta_<?php echo $pergunta['id']; ?>" 
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
              <?php echo $pergunta['obrigatoria'] ? 'required' : ''; ?>>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Submit -->
        <div class="pt-4 border-t border-gray-200">
          <button type="submit" id="btnEnviar" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
            Enviar Avaliação
          </button>
        </div>
      </form>

      <!-- Sucesso (oculto inicialmente) -->
      <div id="sucessoContainer" class="hidden p-8 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">✅</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Avaliação Enviada!</h2>
        <p class="text-gray-600">Obrigado por preencher esta avaliação.</p>
      </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-white/70 text-sm mt-6">
      Powered by SGQ OTI DJ - Módulo RH
    </p>
  </div>

  <script>
  document.getElementById('formAvaliacao').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnEnviar');
    btn.disabled = true;
    btn.textContent = 'Enviando...';
    
    const formData = new FormData(this);
    const token = formData.get('token');
    
    // Coletar respostas
    const respostas = [];
    <?php foreach ($formulario['perguntas'] as $pergunta): ?>
    (function() {
      const input = document.querySelector('[name="resposta_<?php echo $pergunta['id']; ?>"]');
      if (input) {
        let valor = '';
        let nota = null;
        
        if (input.type === 'radio') {
          const checked = document.querySelector('[name="resposta_<?php echo $pergunta['id']; ?>"]:checked');
          valor = checked ? checked.value : '';
          <?php if (in_array($pergunta['tipo'], ['escala_1_5', 'escala_1_10', 'numero'])): ?>
          nota = checked ? parseFloat(checked.value) : null;
          <?php endif; ?>
        } else if (input.type === 'range') {
          valor = input.value;
          nota = parseFloat(input.value);
        } else {
          valor = input.value;
        }
        
        respostas.push({
          pergunta_id: <?php echo $pergunta['id']; ?>,
          resposta: valor,
          nota: nota
        });
      }
    })();
    <?php endforeach; ?>
    
    const submitData = new FormData();
    submitData.append('token', token);
    submitData.append('colaborador_nome', formData.get('colaborador_nome'));
    submitData.append('avaliador_nome', formData.get('avaliador_nome'));
    submitData.append('respostas', JSON.stringify(respostas));
    
    fetch('/avaliacao/responder', { method: 'POST', body: submitData })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          document.getElementById('formAvaliacao').classList.add('hidden');
          document.getElementById('sucessoContainer').classList.remove('hidden');
        } else {
          alert('Erro: ' + data.message);
          btn.disabled = false;
          btn.textContent = 'Enviar Avaliação';
        }
      })
      .catch(err => {
        console.error(err);
        alert('Erro ao enviar avaliação');
        btn.disabled = false;
        btn.textContent = 'Enviar Avaliação';
      });
  });
  </script>
</body>
</html>
