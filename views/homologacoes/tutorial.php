<?php
$pageTitle = 'Tutorial - Módulo de Homologações';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">📚 Tutorial Interativo - Módulo de Homologações</h1>
        <p class="text-gray-600">Aprenda passo a passo como usar o módulo de homologações</p>
    </div>

    <!-- Tabs para navegação -->
    <div class="mb-6 flex gap-2 border-b overflow-x-auto">
        <button data-tab="introducao" class="tab-button px-4 py-3 font-medium border-b-2 border-blue-600 text-blue-600 whitespace-nowrap">
            🎯 Introdução
        </button>
        <button data-tab="criacao" class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap">
            ✨ Criando Homologação
        </button>
        <button data-tab="detalhes" class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap">
            📝 Preenchendo Detalhes
        </button>
        <button data-tab="etapas" class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap">
            📊 Acompanhando Etapas
        </button>
        <button data-tab="kanban" class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap">
            🎯 Kanban Board
        </button>
        <button data-tab="dicas" class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap">
            💡 Dicas & Boas Práticas
        </button>
    </div>

    <!-- Conteúdo das Abas -->
    <div id="introducao" class="tab-content space-y-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">O que é o Módulo de Homologações?</h2>
            <p class="text-gray-700 mb-4">
                O módulo de homologações permite gerenciar o ciclo de vida completo de homologação de produtos e serviços. 
                Você pode criar solicitações, designar responsáveis, acompanhar o progresso através de um kanban interativo 
                e visualizar todos os detalhes em tempo real.
            </p>
            
            <h3 class="text-lg font-semibold text-gray-900 mb-3">🎯 Objetivos Principais:</h3>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start"><span class="text-blue-600 mr-3">✓</span>
                    <span><strong>Centralizar informações:</strong> Toda a documentação e histórico em um único lugar</span>
                </li>
                <li class="flex items-start"><span class="text-blue-600 mr-3">✓</span>
                    <span><strong>Designar responsáveis:</strong> Notificar apenas quem realmente precisa</span>
                </li>
                <li class="flex items-start"><span class="text-blue-600 mr-3">✓</span>
                    <span><strong>Acompanhar progresso:</strong> Visuais claros com Kanban Board</span>
                </li>
                <li class="flex items-start"><span class="text-blue-600 mr-3">✓</span>
                    <span><strong>Registrar decisões:</strong> Histórico completo de todas as ações</span>
                </li>
            </ul>

            <h3 class="text-lg font-semibold text-gray-900 mb-3 mt-6">📋 Fluxo de Status:</h3>
            <div class="bg-white p-4 rounded border flex items-center justify-between overflow-x-auto">
                <div class="text-center flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-800 flex items-center justify-center font-bold mx-auto mb-2">1</div>
                    <p class="text-sm font-medium">Aguardando<br/>Recebimento</p>
                </div>
                <div class="text-gray-300 flex-shrink-0">→</div>
                <div class="text-center flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold mx-auto mb-2">2</div>
                    <p class="text-sm font-medium">Recebido</p>
                </div>
                <div class="text-gray-300 flex-shrink-0">→</div>
                <div class="text-center flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold mx-auto mb-2">3</div>
                    <p class="text-sm font-medium">Em Análise</p>
                </div>
                <div class="text-gray-300 flex-shrink-0">→</div>
                <div class="text-center flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center font-bold mx-auto mb-2">4</div>
                    <p class="text-sm font-medium">Em Homologação</p>
                </div>
                <div class="text-gray-300 flex-shrink-0">→</div>
                <div class="text-center flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-green-100 text-green-800 flex items-center justify-center font-bold mx-auto mb-2">✓</div>
                    <p class="text-sm font-medium">Aprovado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Criando Homologação -->
    <div id="criacao" class="tab-content space-y-6" style="display: none;">
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">✨ Criando uma Nova Homologação</h2>
            
            <div class="space-y-6">
                <div class="bg-white p-4 rounded border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Passo 1: Clique em "Nova Solicitação de Homologação"</h3>
                    <p class="text-gray-700 mb-3">Localize o botão na seção superior e clique para expandir o formulário de criação.</p>
                    <div class="bg-gray-100 p-3 rounded text-sm font-mono text-gray-600">
                        💡 O formulário contém 3 seções principais: Informações Básicas, Responsáveis e Detalhes
                    </div>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Passo 2: Preencha a Seção "Informações Básicas"</h3>
                    <ul class="space-y-2 text-gray-700 mb-3">
                        <li><strong>Código de Referência *:</strong> Identificador único (ex: PROD-001)</li>
                        <li><strong>Data de Vencimento *:</strong> Prazo máximo da homologação</li>
                        <li><strong>Descrição *:</strong> O que está sendo homologado</li>
                        <li><strong>Observação:</strong> Notas gerais (opcional)</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Passo 3: Define Responsáveis e Departamento</h3>
                    <ul class="space-y-2 text-gray-700 mb-3">
                        <li><strong>Departamento (Funil) *:</strong> Determina a rota da homologação</li>
                        <li><strong>Pessoas Responsáveis *:</strong> Seleção múltipla de quem será notificado</li>
                        <li><strong>Notificar Logística:</strong> Enviar aviso ao departamento de logística</li>
                    </ul>
                    <div class="bg-yellow-50 p-3 rounded text-sm">
                        ⚠️ <strong>Importante:</strong> Apenas os responsáveis selecionados receberão notificações, não todo o departamento!
                    </div>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Passo 4: Preencha os Detalhes</h3>
                    <ul class="space-y-2 text-gray-700 mb-3">
                        <li><strong>Tipo de Homologação:</strong> "Interna" ou "Em Cliente"</li>
                        <li><strong>Nome do Cliente:</strong> Obrigatório se selecionado "Em Cliente"</li>
                        <li><strong>Data de Instalação:</strong> Quando foi/será instalado</li>
                        <li><strong>Observações Adicionais:</strong> Detalhes específicos</li>
                        <li><strong>Equipamento atendeu especificativas:</strong> Sim / Parcial / Não</li>
                        <li><strong>Motivo:</strong> Se não atendeu, explique por que</li>
                    </ul>
                </div>

                <div class="bg-blue-50 p-4 rounded italic text-gray-700">
                    ✅ Clique em "Criar Homologação" para salvar. Os responsáveis receberão uma notificação por email!
                </div>
            </div>
        </div>
    </div>

    <!-- Preenchendo Detalhes -->
    <div id="detalhes" class="tab-content space-y-6" style="display: none;">
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📝 Preenchendo Detalhes da Homologação</h2>
            
            <div class="space-y-4">
                <p class="text-gray-700">
                    Uma vez criada, você pode preencher ou atualizar os detalhes em qualquer etapa do processo.
                </p>

                <div class="bg-white p-4 rounded border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🔍 Tipo de Homologação</h3>
                    <p class="text-gray-700 mb-3">
                        Escolha se é uma homologação <strong>Interna</strong> (realizada dentro da empresa) 
                        ou <strong>Em Cliente</strong> (realizada nas instalações do cliente).
                    </p>
                    <p class="text-gray-700">
                        Se escolher "Em Cliente", será necessário informar o <strong>nome do cliente</strong>.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📅 Data de Instalação</h3>
                    <p class="text-gray-700">
                        Registre a data em que o equipamento/serviço foi instalado. Isso ajuda a rastrear o timing 
                        e validar se a homologação foi feita no período correto.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">💬 Observações Adicionais</h3>
                    <p class="text-gray-700">
                        Use este campo para adicionar observações que não se encaixam nas outras categorias. 
                        Podem ser notas técnicas, comportamentos observados, etc.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">✅ Equipamento Atendeu Especificativas?</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li><strong>Sim:</strong> O equipamento atendeu completamente aos requisitos</li>
                        <li><strong>Parcial:</strong> Atendeu parcialmente, pode haver pendências</li>
                        <li><strong>Não:</strong> Não atendeu aos requisitos</li>
                    </ul>
                    <p class="text-gray-700 mt-3">
                        Se escolher "Parcial" ou "Não", um campo obrigatório "Motivo" aparecerá 
                        para que você explique o que não funcionou.
                    </p>
                </div>

                <div class="bg-yellow-50 p-4 rounded border-l-4 border-yellow-500">
                    <strong>💡 Dica:</strong> Preencha os detalhes cuidadosamente! Essas informações serão 
                    visíveis em todas as etapas e no relatório final.
                </div>
            </div>
        </div>
    </div>

    <!-- Acompanhando Etapas -->
    <div id="etapas" class="tab-content space-y-6" style="display: none;">
        <div class="bg-purple-50 border-l-4 border-purple-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Acompanhando as Etapas</h2>
            
            <div class="space-y-4">
                <p class="text-gray-700">
                    O módulo está dividido em etapas. Em cada uma, você pode preencher informações específicas 
                    e visualizar o que já foi feito.
                </p>

                <div class="bg-white p-4 rounded border-l-4 border-yellow-500">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">1️⃣ Aguardando Recebimento</h3>
                    <p class="text-gray-700 text-sm">
                        A homologação foi criada e está aguardando recebimento da logística. 
                        Nessa etapa, informe se deseja notificar a logística.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-blue-500">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">2️⃣ Recebido</h3>
                    <p class="text-gray-700 text-sm">
                        O material foi recebido. Registre a data de recebimento e quem recebeu.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-indigo-500">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">3️⃣ Em Análise</h3>
                    <p class="text-gray-700 text-sm">
                        Departamento inicia a análise técnica. Registre testes, condições do material, 
                        observações técnicas e resultado preliminar.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-purple-500">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">4️⃣ Em Homologação</h3>
                    <p class="text-gray-700 text-sm">
                        Fase onde o equipamento é testado em condições reais. Registre dados do teste, 
                        resultado e recomendações.
                    </p>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-green-500">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">5️⃣ Aprovado / Reprovado</h3>
                    <p class="text-gray-700 text-sm">
                        Decisão final. Registre se foi aprovado ou reprovado, com justificativa completa.
                    </p>
                </div>

                <div class="bg-blue-50 p-4 rounded border-l-4 border-blue-500">
                    <strong>✅ Informações Visíveis:</strong> Em cada etapa, você pode visualizar 
                    <strong>todos os dados preenchidos anteriormente</strong>. Nada se perde!
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div id="kanban" class="tab-content space-y-6" style="display: none;">
        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">🎯 Usando o Kanban Board</h2>
            
            <div class="space-y-4">
                <p class="text-gray-700">
                    O Kanban Board oferece uma visão visual de todas as homologações, organizadas por status.
                </p>

                <div class="bg-white p-4 rounded border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📌 Estrutura do Board</h3>
                    <p class="text-gray-700 mb-3">
                        O board contém colunas, uma para cada status. Os cartões podem ser movidos 
                        entre as colunas para refletir o progresso.
                    </p>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li><strong>Aguardando Recebimento:</strong> Novas solicitações</li>
                        <li><strong>Recebido:</strong> Já chegou, aguardando análise</li>
                        <li><strong>Em Análise:</strong> Sendo analisado</li>
                        <li><strong>Em Homologação:</strong> Em testes finais</li>
                        <li><strong>Aprovado:</strong> Passou ✅</li>
                        <li><strong>Reprovado:</strong> Rejeitado ❌</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🖱️ Interações do Kanban</h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li><strong>Clicar no cartão:</strong> Abre detalhes completos da homologação</li>
                        <li><strong>Arrastar cartão:</strong> Move entre colunas (salva automaticamente)</li>
                        <li><strong>Botão de menu (⋮):</strong> Opções adicionais</li>
                        <li><strong>Contador no header:</strong> Mostra quantos cartões tem em cada coluna</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📍 Informações no Cartão</h3>
                    <p class="text-gray-700 mb-3">Cada cartão mostra:</p>
                    <ul class="space-y-1 text-gray-700 text-sm">
                        <li>✓ Código de referência</li>
                        <li>✓ Descrição do produto</li>
                        <li>✓ Número de responsáveis atribuídos</li>
                        <li>✓ Número de anexos (evidências)</li>
                        <li>✓ Ícone 🚚 se logística foi notificada</li>
                        <li>✓ Data da solicitação</li>
                    </ul>
                </div>

                <div class="bg-green-50 p-4 rounded border-l-4 border-green-500">
                    <strong>💡 Dica de Produtividade:</strong> Use o Kanban para ter uma visão geral rápida. 
                    Clique em um cartão quando precisar de detalhes específicos.
                </div>
            </div>
        </div>
    </div>

    <!-- Dicas & Boas Práticas -->
    <div id="dicas" class="tab-content space-y-6" style="display: none;">
        <div class="bg-violet-50 border-l-4 border-violet-500 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">💡 Dicas & Boas Práticas</h2>
            
            <div class="space-y-4">
                <div class="bg-white p-4 rounded border-l-4 border-violet-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🎯 Ao Criar a Homologação</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Use códigos de referência claros e únicos</li>
                        <li>✓ Selecione <strong>apenas os responsáveis que realmente precisam</strong></li>
                        <li>✓ Defina uma data de vencimento realista</li>
                        <li>✓ Preencha a descrição com detalhes suficientes para contexto</li>
                        <li>✓ Se houver dúvida sobre o cliente, defina como "Interna" primeiro</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-violet-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📝 Durante o Processo</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Registre observações de forma clara e profissional</li>
                        <li>✓ Sempre indique se o equipamento atendeu especificativas</li>
                        <li>✓ Se houver motivo de reprovação, seja específico</li>
                        <li>✓ Anexe evidências (fotos, relatórios, testes)</li>
                        <li>✓ Não deixe homologações "penduradas" por muito tempo</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-violet-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">⚡ Performance & Organização</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Revise o Kanban Board regularmente (diariamente é ideal)</li>
                        <li>✓ Atualize status assim que houver progresso</li>
                        <li>✓ Use o histórico para rastrear decisões</li>
                        <li>✓ Mantenha os detalhes atualizados em tempo real</li>
                        <li>✓ Use observações para comunicação entre responsáveis</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded border-l-4 border-violet-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🔒 Segurança & Integridade</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Todos os dados são salvos automaticamente</li>
                        <li>✓ O histórico completo fica registrado</li>
                        <li>✓ Não é possível deletar histórico (por design)</li>
                        <li>✓ Apenas responsáveis designados receberão notificações</li>
                        <li>✓ Use nomes legíveis nos campos de observação</li>
                    </ul>
                </div>

                <div class="bg-red-50 p-4 rounded border-l-4 border-red-500">
                    <strong>⚠️ Evite Esses Erros:</strong>
                    <ul class="space-y-1 text-gray-700 mt-3 text-sm">
                        <li>❌ Não selecione responsáveis que não estão relacionados à homologação</li>
                        <li>❌ Não deixe dados em branco sem explicação</li>
                        <li>❌ Não use nomes de clientes incorretos ou incompletos</li>
                        <li>❌ Não reprovem sem registrar o motivo</li>
                        <li>❌ Não atualize datas de forma inconsistente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de abas
document.querySelectorAll('.tab-button').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Esconder todas as abas
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });
        
        // Mostrar aba ativa
        document.getElementById(tabName).style.display = 'block';
        
        // Atualizar botão ativo
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('border-blue-600', 'text-blue-600');
            b.classList.add('border-transparent', 'text-gray-600');
        });
        
        this.classList.remove('border-transparent', 'text-gray-600');
        this.classList.add('border-blue-600', 'text-blue-600');
    });
});
</script>

<style>
.tab-button {
    transition: all 0.2s ease;
}

.tab-button:hover {
    background-color: #f3f4f6;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
