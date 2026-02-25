<?php
if (!function_exists('e')) {
    function e($value) { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">&#x1F9EE; Calculadora de Envio de Toners</h1>
                <p class="mt-2 text-gray-600">Verifique se e necessario enviar toner para o cliente</p>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Botao Info -->
                <button onclick="abrirModalInfo()" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Como funciona">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
                <?php if ($isAdmin): ?>
                <!-- Botao Engrenagem (Config) -->
                <button onclick="abrirModalConfig()" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors" title="Configuracoes">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card principal -->
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
                            <span class="text-sm text-blue-600 font-medium">Capacidade:</span>
                            <p id="toner-capacidade" class="text-2xl font-bold text-blue-700"></p>
                            <span class="text-xs text-gray-500">paginas</span>
                        </div>
                        <button onclick="limparToner()" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors" title="Remover">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Contadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">2. Ultimo Contador</label>
                    <input type="number" id="ultimo-contador" min="0" placeholder="Ex: 10000"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-lg transition-all">
                    <p class="text-xs text-gray-500 mt-1">Leitura anterior do contador da impressora</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">3. Contador Atual</label>
                    <input type="number" id="contador-atual" min="0" placeholder="Ex: 15000"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-lg transition-all">
                    <p class="text-xs text-gray-500 mt-1">Leitura atual do contador da impressora</p>
                </div>
            </div>

            <!-- Botao Calcular -->
            <button onclick="calcular()" id="btn-calcular"
                class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:transform-none disabled:cursor-not-allowed"
                disabled>
                Calcular Necessidade de Envio
            </button>

            <!-- Resultado -->
            <div id="resultado-container" class="hidden"></div>
        </div>
    </div>

    <!-- Limiar badge -->
    <div class="mt-4 text-center">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600">
            Limiar configurado: <strong class="ml-1" id="limiar-display"><?= e($limiar) ?></strong>&nbsp;paginas
        </span>
    </div>
</div>

<!-- Modal Info -->
<div id="modal-info" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
    <div class="relative top-10 mx-auto p-0 border-0 w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white mb-10">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-2xl px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Como funciona a Calculadora</h3>
                </div>
                <button onclick="fecharModalInfo()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-blue-900 mb-2">Objetivo</h4>
                <p class="text-blue-800 text-sm">Esta calculadora ajuda a determinar se e necessario enviar um novo toner para o cliente, evitando envios desnecessarios e garantindo que o cliente nao fique sem suprimento.</p>
            </div>
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-purple-900 mb-2">Como calcular</h4>
                <ol class="list-decimal list-inside text-purple-800 text-sm space-y-2">
                    <li><strong>Selecione o modelo do toner</strong> - O sistema buscara automaticamente a capacidade de impressao.</li>
                    <li><strong>Informe o Ultimo Contador</strong> - O valor do contador da impressora na ultima leitura.</li>
                    <li><strong>Informe o Contador Atual</strong> - O valor atual do contador da impressora.</li>
                    <li><strong>Clique em Calcular</strong> - O sistema verificara a necessidade de envio.</li>
                </ol>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-green-900 mb-2">Formula</h4>
                <div class="text-green-800 text-sm space-y-1">
                    <p><code class="bg-green-100 px-2 py-0.5 rounded">Paginas impressas = Contador atual - Ultimo contador</code></p>
                    <p><code class="bg-green-100 px-2 py-0.5 rounded">Paginas restantes = Capacidade do toner - Paginas impressas</code></p>
                </div>
            </div>
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg">
                <h4 class="font-bold text-orange-900 mb-2">Regra de decisao</h4>
                <ul class="text-orange-800 text-sm space-y-2">
                    <li><strong>PODE enviar toner</strong> - Se as paginas restantes forem igual ou menor que o limiar configurado.</li>
                    <li><strong>NAO pode enviar</strong> - Se as paginas restantes forem maior que o limiar configurado.</li>
                </ul>
                <p class="text-orange-700 text-xs mt-2">
                    <strong>Exemplo:</strong> Se o limiar e 300, o toner tem 20.000 paginas de capacidade, o ultimo contador era 10.000 e o atual e 15.000:<br>
                    Paginas impressas = 15.000 - 10.000 = <strong>5.000</strong><br>
                    Paginas restantes = 20.000 - 5.000 = <strong>15.000</strong><br>
                    Como 15.000 > 300, o resultado e <strong>NAO pode enviar</strong> (o cliente ainda tem bastante toner).
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Configuracao (Admin Only) -->
<?php if ($isAdmin): ?>
<div id="modal-config" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-0 border-0 w-11/12 md:w-1/2 lg:w-1/3 shadow-2xl rounded-2xl bg-white">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-t-2xl px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Configuracao da Calculadora</h3>
                        <p class="text-gray-300 text-sm">Defina a regra de envio</p>
                    </div>
                </div>
                <button onclick="fecharModalConfig()" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-sm text-yellow-800">
                    <strong>O que e o limiar?</strong><br>
                    O limiar define a quantidade minima de paginas restantes abaixo da qual o sistema autoriza o envio de um novo toner. Se as paginas restantes do cliente forem <strong>iguais ou menores</strong> que este valor, o envio e autorizado.
                </p>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Limiar de Paginas Restantes</label>
                <div class="relative">
                    <input type="number" id="config-limiar" min="0" value="<?= e($limiar) ?>"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 text-lg transition-all pr-20">
                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">paginas</span>
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
    const query = this.value.trim();
    clearTimeout(debounceTimer);
    if (query.length < 1) { fecharDropdown(); return; }
    debounceTimer = setTimeout(() => buscarToners(query), 300);
});

document.addEventListener('click', function(e) {
    const busca = document.getElementById('busca-toner');
    const dropdown = document.getElementById('dropdown-toner');
    if (!busca.contains(e.target) && !dropdown.contains(e.target)) fecharDropdown();
});

function buscarToners(query) {
    fetch('/atendimento/calculadora-toners/buscar?q=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(data => {
            const dropdown = document.getElementById('dropdown-toner');
            if (!data.success || data.data.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Nenhum toner encontrado</div>';
                dropdown.classList.remove('hidden');
                return;
            }
            dropdown.innerHTML = data.data.map(toner =>
                '<button type="button" onclick=\'selecionarToner(' + JSON.stringify(toner).replace(/'/g, "\\'") + ')\'' +
                ' class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors border-b border-gray-100 last:border-0 flex items-center justify-between">' +
                '<div><span class="font-medium text-gray-900">' + escapeHtml(toner.modelo) + '</span>' +
                '<span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">' + escapeHtml(toner.cor || '') + '</span></div>' +
                '<span class="text-sm text-blue-600 font-medium">' + Number(toner.capacidade_folhas).toLocaleString('pt-BR') + ' pag.</span>' +
                '</button>'
            ).join('');
            dropdown.classList.remove('hidden');
        });
}

function selecionarToner(toner) {
    tonerSelecionado = toner;
    document.getElementById('busca-toner').value = toner.modelo;
    document.getElementById('toner-nome').textContent = toner.modelo + (toner.cor ? ' (' + toner.cor + ')' : '');
    document.getElementById('toner-capacidade').textContent = Number(toner.capacidade_folhas).toLocaleString('pt-BR');
    document.getElementById('toner-selecionado').classList.remove('hidden');
    fecharDropdown();
    verificarBotaoCalcular();
}

function limparToner() {
    tonerSelecionado = null;
    document.getElementById('busca-toner').value = '';
    document.getElementById('toner-selecionado').classList.add('hidden');
    document.getElementById('resultado-container').classList.add('hidden');
    verificarBotaoCalcular();
}

function fecharDropdown() { document.getElementById('dropdown-toner').classList.add('hidden'); }

document.getElementById('ultimo-contador').addEventListener('input', verificarBotaoCalcular);
document.getElementById('contador-atual').addEventListener('input', verificarBotaoCalcular);

function verificarBotaoCalcular() {
    const btn = document.getElementById('btn-calcular');
    const ultimo = document.getElementById('ultimo-contador').value;
    const atual = document.getElementById('contador-atual').value;
    btn.disabled = !(tonerSelecionado && ultimo !== '' && atual !== '');
}

function calcular() {
    if (!tonerSelecionado) return;
    const ultimoContador = parseInt(document.getElementById('ultimo-contador').value) || 0;
    const contadorAtual = parseInt(document.getElementById('contador-atual').value) || 0;
    const capacidade = parseInt(tonerSelecionado.capacidade_folhas) || 0;

    if (contadorAtual < ultimoContador) { mostrarResultado('erro', 'O Contador Atual nao pode ser menor que o Ultimo Contador.', {}); return; }
    if (capacidade <= 0) { mostrarResultado('erro', 'Este toner nao possui capacidade de folhas cadastrada.', {}); return; }

    const paginasImpressas = contadorAtual - ultimoContador;
    const paginasRestantes = capacidade - paginasImpressas;
    const podeEnviar = paginasRestantes <= limiarAtual;

    const dados = {
        capacidade, paginasImpressas,
        paginasRestantes: Math.max(0, paginasRestantes),
        percentualUsado: Math.min(100, Math.round((paginasImpressas / capacidade) * 100)),
        limiar: limiarAtual
    };

    if (paginasRestantes <= 0) mostrarResultado('enviar_urgente', null, dados);
    else if (podeEnviar) mostrarResultado('enviar', null, dados);
    else mostrarResultado('nao_enviar', null, dados);
}

function mostrarResultado(tipo, mensagemErro, dados) {
    const container = document.getElementById('resultado-container');
    container.classList.remove('hidden');

    if (tipo === 'erro') {
        container.innerHTML = '<div class="p-5 bg-yellow-50 border-2 border-yellow-300 rounded-xl"><div class="flex items-center space-x-3"><span class="text-3xl">&#9888;&#65039;</span><p class="text-yellow-800 font-medium">' + mensagemErro + '</p></div></div>';
        return;
    }

    const isEnviar = tipo === 'enviar' || tipo === 'enviar_urgente';
    const isUrgente = tipo === 'enviar_urgente';
    const bgGradient = isUrgente ? 'from-red-500 to-red-700' : isEnviar ? 'from-green-500 to-emerald-600' : 'from-orange-400 to-red-500';
    const bgCard = isUrgente ? 'bg-red-50 border-red-300' : isEnviar ? 'bg-green-50 border-green-300' : 'bg-orange-50 border-orange-300';
    const textoDecisao = isUrgente ? 'ENVIO URGENTE - Toner esgotado!' : isEnviar ? 'PODE ENVIAR TONER' : 'NAO PRECISA ENVIAR TONER';
    const textoExplicacao = isUrgente ? 'O toner esta esgotado ou com saldo negativo. Envie imediatamente!'
        : isEnviar ? 'O cliente esta com ' + dados.paginasRestantes.toLocaleString('pt-BR') + ' paginas restantes, igual ou menor que o limiar de ' + dados.limiar.toLocaleString('pt-BR') + ' paginas.'
        : 'O cliente ainda possui ' + dados.paginasRestantes.toLocaleString('pt-BR') + ' paginas restantes, acima do limiar de ' + dados.limiar.toLocaleString('pt-BR') + ' paginas.';

    const barColor = dados.percentualUsado >= 90 ? 'bg-red-500' : dados.percentualUsado >= 70 ? 'bg-orange-400' : dados.percentualUsado >= 50 ? 'bg-yellow-400' : 'bg-green-500';

    container.innerHTML =
        '<div class="border-2 ' + bgCard + ' rounded-xl overflow-hidden">' +
        '<div class="bg-gradient-to-r ' + bgGradient + ' p-5 text-center"><p class="text-white text-2xl font-bold">' + textoDecisao + '</p></div>' +
        '<div class="p-5 space-y-4"><p class="text-gray-700 text-center">' + textoExplicacao + '</p>' +
        '<div class="mb-2"><div class="flex justify-between text-sm text-gray-600 mb-1"><span>Uso do toner</span><span class="font-bold">' + dados.percentualUsado + '%</span></div>' +
        '<div class="w-full bg-gray-200 rounded-full h-4"><div class="h-4 rounded-full transition-all duration-700 ' + barColor + '" style="width: ' + Math.min(100, dados.percentualUsado) + '%"></div></div></div>' +
        '<div class="grid grid-cols-3 gap-3">' +
        '<div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Capacidade</p><p class="text-lg font-bold text-gray-900">' + dados.capacidade.toLocaleString('pt-BR') + '</p></div>' +
        '<div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Impressas</p><p class="text-lg font-bold text-blue-600">' + dados.paginasImpressas.toLocaleString('pt-BR') + '</p></div>' +
        '<div class="text-center p-3 bg-white rounded-xl border border-gray-200"><p class="text-xs text-gray-500 mb-1">Restantes</p><p class="text-lg font-bold ' + (isEnviar ? 'text-red-600' : 'text-green-600') + '">' + dados.paginasRestantes.toLocaleString('pt-BR') + '</p></div>' +
        '</div></div></div>';

    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function abrirModalInfo() { document.getElementById('modal-info').classList.remove('hidden'); }
function fecharModalInfo() { document.getElementById('modal-info').classList.add('hidden'); }

<?php if ($isAdmin): ?>
function abrirModalConfig() {
    document.getElementById('config-limiar').value = limiarAtual;
    document.getElementById('modal-config').classList.remove('hidden');
}
function fecharModalConfig() { document.getElementById('modal-config').classList.add('hidden'); }

function salvarConfig() {
    const novoLimiar = parseInt(document.getElementById('config-limiar').value);
    if (isNaN(novoLimiar) || novoLimiar < 0) { alert('Informe um valor valido para o limiar.'); return; }

    const formData = new FormData();
    formData.append('limiar_paginas', novoLimiar);

    fetch('/atendimento/calculadora-toners/config', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                limiarAtual = data.limiar_paginas;
                document.getElementById('limiar-display').textContent = limiarAtual;
                fecharModalConfig();
                const notif = document.createElement('div');
                notif.className = 'fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg bg-green-500 text-white transition-all duration-300';
                notif.textContent = data.message;
                document.body.appendChild(notif);
                setTimeout(() => { notif.classList.add('opacity-0'); setTimeout(() => notif.remove(), 300); }, 3000);
            } else { alert('Erro: ' + data.message); }
        })
        .catch(() => alert('Erro ao salvar configuracao.'));
}
<?php endif; ?>

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>