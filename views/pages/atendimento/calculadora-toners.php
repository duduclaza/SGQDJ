<?php
if (!function_exists('e')) {
    function e($value) { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">&#x1F9EE; Calculadora de Envio de Toners</h1>
                <p class="mt-2 text-gray-600">Verifique se &eacute; necess&aacute;rio enviar toner para o cliente</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="abrirModalInfo()" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Como funciona">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
                <?php if ($isAdmin): ?>
                <button onclick="abrirModalConfig()" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors" title="Configuracoes">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600"></div>
        <div class="p-6 space-y-6">
            <!-- 1. Selecao do Toner -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">1. Modelo do Toner</label>
                <div class="relative">
                    <input type="text" id="busca-toner" placeholder="Digite para buscar o modelo do toner..." autocomplete="off"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-lg transition-all">
                    <div id="dropdown-toner" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto hidden"></div>
                </div>
                <div id="toner-selecionado" class="hidden mt-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-blue-600 font-medium">Toner selecionado:</span>
                            <p id="toner-nome" class="text-lg font-bold text-gray-900"></p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-blue-600 font-medium">Capacidade nominal:</span>
                            <p id="toner-capacidade" class="text-2xl font-bold text-blue-700"></p>
                            <span class="text-xs text-gray-500">p&aacute;ginas (a 5% cobertura)</span>
                        </div>
                        <button onclick="limparToner()" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors" title="Remover">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Area Impressa -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">2. &Aacute;rea Impressa do Cliente (%) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" id="area-impressa" min="0.1" max="100" step="0.1" placeholder="Ex: 5"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 text-lg transition-all pr-12">
                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold text-lg">%</span>
                </div>
                <div class="mt-2 flex items-start space-x-2">
                    <svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-gray-500">Pela <strong>ISO 9001</strong>, a capacidade nominal &eacute; baseada em <strong>5% de cobertura</strong>. Cobertura maior = rendimento menor. Cobertura menor = rendimento maior.</p>
                </div>
                <div id="area-indicador" class="hidden mt-3 p-3 rounded-xl border text-sm"></div>
            </div>

            <!-- 3. Contadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">3. &Uacute;ltimo Contador</label>
                    <input type="number" id="ultimo-contador" min="0" placeholder="Ex: 10000"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-lg transition-all">
                    <p class="text-xs text-gray-500 mt-1">Leitura anterior do contador da impressora</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">4. Contador Atual</label>
                    <input type="number" id="contador-atual" min="0" placeholder="Ex: 15000"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-lg transition-all">
                    <p class="text-xs text-gray-500 mt-1">Leitura atual do contador da impressora</p>
                </div>
            </div>

            <button onclick="calcular()" id="btn-calcular"
                class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:transform-none disabled:cursor-not-allowed" disabled>
                Calcular Necessidade de Envio
            </button>

            <div id="resultado-container" class="hidden"></div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600">
            Limiar configurado: <strong class="ml-1" id="limiar-display"><?= e($limiar) ?></strong>&nbsp;p&aacute;ginas
        </span>
    </div>
</div>

<!-- Modal Info -->
<div id="modal-info" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
    <div class="relative top-10 mx-auto p-0 border-0 w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white mb-10">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-2xl px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <h3 class="text-xl font-semibold text-white">Como funciona a Calculadora</h3>
                </div>
                <button onclick="fecharModalInfo()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-blue-900 mb-2">Objetivo</h4>
                <p class="text-blue-800 text-sm">Determina se &eacute; necess&aacute;rio enviar toner, usando a <strong>&aacute;rea impressa real</strong> do cliente.</p>
            </div>
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-orange-900 mb-2">&#x1F4D0; &Aacute;rea Impressa &amp; ISO 9001</h4>
                <div class="text-orange-800 text-sm space-y-2">
                    <p>Capacidade nominal = <strong>5% cobertura</strong>.</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li><strong>= 5%</strong> &rarr; Rendimento nominal</li>
                        <li><strong>&gt; 5%</strong> &rarr; Rendimento <span class="text-red-600 font-bold">menor</span></li>
                        <li><strong>&lt; 5%</strong> &rarr; Rendimento <span class="text-green-600 font-bold">maior</span></li>
                    </ul>
                    <p class="font-medium">F&oacute;rmula: <code class="bg-orange-100 px-2 py-0.5 rounded">Cap. Ajustada = Nominal &times; (5 &divide; &Aacute;rea%)</code></p>
                </div>
            </div>
            <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-r-lg">
                <h4 class="font-bold text-gray-900 mb-2">&#x1F4A1; Exemplo</h4>
                <div class="text-gray-700 text-sm">
                    <p>Toner <strong>20.000 pag.</strong> | Cliente <strong>8%</strong> cobertura</p>
                    <p>Ajustada = 20.000 &times; (5&divide;8) = <strong>12.500 pag.</strong></p>
                    <p>Contadores: 10.000 &rarr; 20.000 = <strong>10.000</strong> impressas</p>
                    <p>Restantes = 12.500 - 10.000 = <strong>2.500</strong></p>
                    <p>Limiar 300 &rarr; <strong class="text-orange-600">N&atilde;o enviar</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Config Admin -->
<?php if ($isAdmin): ?>
<div id="modal-config" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-0 border-0 w-11/12 md:w-1/2 lg:w-1/3 shadow-2xl rounded-2xl bg-white">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-t-2xl px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
                    <div><h3 class="text-lg font-semibold text-white">Configura&ccedil;&atilde;o</h3><p class="text-gray-300 text-sm">Regra de envio</p></div>
                </div>
                <button onclick="fecharModalConfig()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-sm text-yellow-800"><strong>Limiar:</strong> paginas restantes (ajustadas) abaixo das quais o envio &eacute; autorizado.</p>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Limiar de P&aacute;ginas</label>
                <div class="relative">
                    <input type="number" id="config-limiar" min="0" value="<?= e($limiar) ?>" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 text-lg transition-all pr-20">
                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">p&aacute;ginas</span>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="fecharModalConfig()" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancelar</button>
                <button onclick="salvarConfig()" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl font-medium transition-colors">Salvar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
let tonerSelecionado = null;
let limiarAtual = <?= (int)$limiar ?>;
let debounceTimer = null;

document.getElementById('busca-toner').addEventListener('input', function() {
    const q = this.value.trim();
    clearTimeout(debounceTimer);
    if (q.length < 1) { fecharDropdown(); return; }
    debounceTimer = setTimeout(() => buscarToners(q), 300);
});

document.addEventListener('click', function(e) {
    if (!document.getElementById('busca-toner').contains(e.target) && !document.getElementById('dropdown-toner').contains(e.target)) fecharDropdown();
});

document.getElementById('area-impressa').addEventListener('input', function() {
    const area = parseFloat(this.value);
    const ind = document.getElementById('area-indicador');
    if (!area || area <= 0) { ind.classList.add('hidden'); verificarBotaoCalcular(); return; }
    ind.classList.remove('hidden');
    if (tonerSelecionado) {
        const cn = parseInt(tonerSelecionado.capacidade_folhas) || 0;
        const ca = Math.round(cn * (5 / area));
        const pct = Math.round((ca / cn) * 100);
        if (area < 5) {
            ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-green-50 border-green-200';
            ind.innerHTML = '<div class="flex items-center justify-between"><div><span class="font-bold text-green-700">&#9650; Rendimento ACIMA do nominal</span><p class="text-green-600 mt-1">Cobertura '+area+'% - Rendimento: <strong>'+pct+'%</strong></p></div><div class="text-right"><span class="text-xs text-green-500">Cap. ajustada</span><p class="text-xl font-bold text-green-700">'+ca.toLocaleString('pt-BR')+'</p><span class="text-xs text-green-500">paginas</span></div></div>';
        } else if (area > 5) {
            ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200';
            ind.innerHTML = '<div class="flex items-center justify-between"><div><span class="font-bold text-red-700">&#9660; Rendimento ABAIXO do nominal</span><p class="text-red-600 mt-1">Cobertura '+area+'% - Rendimento: <strong>'+pct+'%</strong></p></div><div class="text-right"><span class="text-xs text-red-500">Cap. ajustada</span><p class="text-xl font-bold text-red-700">'+ca.toLocaleString('pt-BR')+'</p><span class="text-xs text-red-500">paginas</span></div></div>';
        } else {
            ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-blue-50 border-blue-200';
            ind.innerHTML = '<div class="flex items-center"><span class="font-bold text-blue-700">&#9654; Rendimento nominal (5% padrao ISO)</span><span class="ml-auto text-xl font-bold text-blue-700">'+cn.toLocaleString('pt-BR')+' pag.</span></div>';
        }
    } else {
        if (area < 5) { ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-green-50 border-green-200'; ind.innerHTML = '<span class="font-bold text-green-700">&#9650; '+area+'% - Rendimento acima do nominal</span>'; }
        else if (area > 5) { ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200'; ind.innerHTML = '<span class="font-bold text-red-700">&#9660; '+area+'% - Rendimento abaixo do nominal</span>'; }
        else { ind.className = 'mt-3 p-3 rounded-xl border text-sm bg-blue-50 border-blue-200'; ind.innerHTML = '<span class="font-bold text-blue-700">&#9654; Padrao ISO 9001 (5%)</span>'; }
    }
    verificarBotaoCalcular();
});

function buscarToners(q) {
    fetch('/atendimento/calculadora-toners/buscar?q='+encodeURIComponent(q)).then(r=>r.json()).then(data=>{
        const dd = document.getElementById('dropdown-toner');
        if (!data.success||data.data.length===0) { dd.innerHTML='<div class="px-4 py-3 text-gray-500 text-sm">Nenhum toner encontrado</div>'; dd.classList.remove('hidden'); return; }
        dd.innerHTML = data.data.map(t=>'<button type="button" onclick=\'selecionarToner('+JSON.stringify(t).replace(/'/g,"\\'")+')\''+' class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors border-b border-gray-100 last:border-0 flex items-center justify-between"><div><span class="font-medium text-gray-900">'+escapeHtml(t.modelo)+'</span><span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">'+escapeHtml(t.cor||'')+'</span></div><span class="text-sm text-blue-600 font-medium">'+Number(t.capacidade_folhas).toLocaleString('pt-BR')+' pag.</span></button>').join('');
        dd.classList.remove('hidden');
    });
}

function selecionarToner(t) {
    tonerSelecionado = t;
    document.getElementById('busca-toner').value = t.modelo;
    document.getElementById('toner-nome').textContent = t.modelo+(t.cor?' ('+t.cor+')':'');
    document.getElementById('toner-capacidade').textContent = Number(t.capacidade_folhas).toLocaleString('pt-BR');
    document.getElementById('toner-selecionado').classList.remove('hidden');
    fecharDropdown();
    document.getElementById('area-impressa').dispatchEvent(new Event('input'));
    verificarBotaoCalcular();
}

function limparToner() {
    tonerSelecionado = null;
    document.getElementById('busca-toner').value = '';
    document.getElementById('toner-selecionado').classList.add('hidden');
    document.getElementById('resultado-container').classList.add('hidden');
    document.getElementById('area-indicador').classList.add('hidden');
    verificarBotaoCalcular();
}

function fecharDropdown() { document.getElementById('dropdown-toner').classList.add('hidden'); }

document.getElementById('ultimo-contador').addEventListener('input', verificarBotaoCalcular);
document.getElementById('contador-atual').addEventListener('input', verificarBotaoCalcular);

function verificarBotaoCalcular() {
    const btn = document.getElementById('btn-calcular');
    const u = document.getElementById('ultimo-contador').value;
    const a = document.getElementById('contador-atual').value;
    const ar = document.getElementById('area-impressa').value;
    btn.disabled = !(tonerSelecionado && u!=='' && a!=='' && ar!=='' && parseFloat(ar)>0);
}

function calcular() {
    if (!tonerSelecionado) return;
    const uc = parseInt(document.getElementById('ultimo-contador').value)||0;
    const ca_input = parseInt(document.getElementById('contador-atual').value)||0;
    const area = parseFloat(document.getElementById('area-impressa').value)||0;
    const cn = parseInt(tonerSelecionado.capacidade_folhas)||0;

    if (ca_input < uc) { mostrarResultado('erro','O Contador Atual nao pode ser menor que o Ultimo Contador.',{}); return; }
    if (cn <= 0) { mostrarResultado('erro','Este toner nao possui capacidade cadastrada.',{}); return; }
    if (area <= 0||area > 100) { mostrarResultado('erro','Informe area impressa valida (0.1% a 100%).',{}); return; }

    const capAjustada = Math.round(cn*(5/area));
    const impressas = ca_input - uc;
    const restantes = capAjustada - impressas;
    const enviar = restantes <= limiarAtual;

    const d = { capacidadeNominal:cn, capacidadeAjustada:capAjustada, areaImpressa:area, paginasImpressas:impressas, paginasRestantes:Math.max(0,restantes), percentualUsado:Math.min(100,Math.round((impressas/capAjustada)*100)), limiar:limiarAtual };

    if (restantes<=0) mostrarResultado('enviar_urgente',null,d);
    else if (enviar) mostrarResultado('enviar',null,d);
    else mostrarResultado('nao_enviar',null,d);
}

function mostrarResultado(tipo,msg,d) {
    const c = document.getElementById('resultado-container');
    c.classList.remove('hidden');
    if (tipo==='erro') { c.innerHTML='<div class="p-5 bg-yellow-50 border-2 border-yellow-300 rounded-xl"><div class="flex items-center space-x-3"><span class="text-3xl">&#9888;&#65039;</span><p class="text-yellow-800 font-medium">'+msg+'</p></div></div>'; return; }

    const isEnv = tipo==='enviar'||tipo==='enviar_urgente';
    const isUrg = tipo==='enviar_urgente';
    const bgG = isUrg?'from-red-500 to-red-700':isEnv?'from-green-500 to-emerald-600':'from-orange-400 to-red-500';
    const bgC = isUrg?'bg-red-50 border-red-300':isEnv?'bg-green-50 border-green-300':'bg-orange-50 border-orange-300';
    const txt = isUrg?'ENVIO URGENTE - Toner esgotado!':isEnv?'PODE ENVIAR TONER':'NAO PRECISA ENVIAR TONER';
    const exp = isUrg?'Toner esgotado ou com saldo negativo. Envie imediatamente!'
        :isEnv?'Cliente com '+d.paginasRestantes.toLocaleString('pt-BR')+' pag. restantes (ajustadas para '+d.areaImpressa+'%), igual/menor que limiar de '+d.limiar.toLocaleString('pt-BR')+' pag.'
        :'Cliente com '+d.paginasRestantes.toLocaleString('pt-BR')+' pag. restantes (ajustadas para '+d.areaImpressa+'%), acima do limiar de '+d.limiar.toLocaleString('pt-BR')+' pag.';

    const barC = d.percentualUsado>=90?'bg-red-500':d.percentualUsado>=70?'bg-orange-400':d.percentualUsado>=50?'bg-yellow-400':'bg-green-500';

    let aj='';
    if(d.areaImpressa!==5){
        const ac=d.areaImpressa>5?'bg-red-100 text-red-700':'bg-green-100 text-green-700';
        const ic=d.areaImpressa>5?'&#9660;':'&#9650;';
        aj='<div class="mb-3 p-2 rounded-lg text-center text-sm '+ac+'">'+ic+' <strong>Area impressa: '+d.areaImpressa+'%</strong> - Capacidade ajustada de '+d.capacidadeNominal.toLocaleString('pt-BR')+' para <strong>'+d.capacidadeAjustada.toLocaleString('pt-BR')+'</strong> paginas</div>';
    } else {
        aj='<div class="mb-3 p-2 rounded-lg text-center text-sm bg-blue-100 text-blue-700"><strong>Area impressa: 5%</strong> - Rendimento nominal (sem ajuste)</div>';
    }

    c.innerHTML='<div class="border-2 '+bgC+' rounded-xl overflow-hidden"><div class="bg-gradient-to-r '+bgG+' p-5 text-center"><p class="text-white text-2xl font-bold">'+txt+'</p></div><div class="p-5 space-y-4"><p class="text-gray-700 text-center">'+exp+'</p>'+aj+'<div class="mb-2"><div class="flex justify-between text-sm text-gray-600 mb-1"><span>Uso do toner (ajustado)</span><span class="font-bold">'+d.percentualUsado+'%</span></div><div class="w-full bg-gray-200 rounded-full h-4"><div class="h-4 rounded-full transition-all duration-700 '+barC+'" style="width:'+Math.min(100,d.percentualUsado)+'%"></div></div></div><div class="grid grid-cols-2 md:grid-cols-4 gap-3"><div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Cap. Nominal</p><p class="text-lg font-bold text-gray-400">'+d.capacidadeNominal.toLocaleString('pt-BR')+'</p></div><div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Cap. Ajustada</p><p class="text-lg font-bold text-gray-900">'+d.capacidadeAjustada.toLocaleString('pt-BR')+'</p></div><div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Impressas</p><p class="text-lg font-bold text-blue-600">'+d.paginasImpressas.toLocaleString('pt-BR')+'</p></div><div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Restantes</p><p class="text-lg font-bold '+(isEnv?'text-red-600':'text-green-600')+'">'+d.paginasRestantes.toLocaleString('pt-BR')+'</p></div></div></div></div>';

    c.scrollIntoView({behavior:'smooth',block:'nearest'});
}

function abrirModalInfo() { document.getElementById('modal-info').classList.remove('hidden'); }
function fecharModalInfo() { document.getElementById('modal-info').classList.add('hidden'); }

<?php if ($isAdmin): ?>
function abrirModalConfig() { document.getElementById('config-limiar').value=limiarAtual; document.getElementById('modal-config').classList.remove('hidden'); }
function fecharModalConfig() { document.getElementById('modal-config').classList.add('hidden'); }

function salvarConfig() {
    const nl = parseInt(document.getElementById('config-limiar').value);
    if (isNaN(nl)||nl<0) { alert('Valor invalido.'); return; }
    const fd = new FormData(); fd.append('limiar_paginas',nl);
    fetch('/atendimento/calculadora-toners/config',{method:'POST',body:fd,credentials:'same-origin'}).then(r=>r.json()).then(data=>{
        if (data.success) { limiarAtual=data.limiar_paginas; document.getElementById('limiar-display').textContent=limiarAtual; fecharModalConfig();
            const n=document.createElement('div'); n.className='fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg bg-green-500 text-white transition-all duration-300'; n.textContent=data.message; document.body.appendChild(n);
            setTimeout(()=>{n.classList.add('opacity-0');setTimeout(()=>n.remove(),300);},3000);
        } else { alert('Erro: '+data.message); }
    }).catch(()=>alert('Erro ao salvar.'));
}
<?php endif; ?>

function escapeHtml(t) { if(!t) return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
</script>