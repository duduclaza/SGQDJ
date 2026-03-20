<?php require __DIR__ . '/_subnav.php'; 

if ($u['perfil'] !== 'logistica' && $u['perfil'] !== 'admin' && $u['perfil'] !== 'super_admin') {
    echo "<div class='bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 mb-6 shadow-sm dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-300 flex items-center gap-3'><i class='ph-fill ph-warning-circle text-xl'></i> Acesso restrito. Mude o simulador no subnavbar para o perfil Logística.</div>";
    return;
}
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Guarita & Recebimento (Logística)</h2>
    <p class="text-slate-500 dark:text-slate-400 text-sm">Registre a chegada dos pacotes de teste no prédio alertando automaticamente a TI responsável.</p>
</div>

<div class="mb-10">
    <h3 class="text-lg font-bold flex items-center gap-2 text-slate-800 dark:text-white mb-4">
        <i class="ph-fill ph-truck text-amber-500 text-2xl"></i> Caminhões a caminho / Aguardando Entrega Físisca
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($aguardando)): ?>
            <div class="col-span-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 border-dashed rounded-xl p-8 text-center text-slate-500 dark:text-slate-400">
                Nenhuma carga pendente sob seu farol no momento.
            </div>
        <?php endif; ?>
        
        <?php foreach ($aguardando as $h): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 border-t-4 border-t-amber-500 flex flex-col hover:shadow-md transition-shadow">
            <div class="p-5 flex-1">
                <div class="flex justify-between items-start mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                        <i class="ph-fill ph-clock"></i> Pendente Doca
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 text-sm font-mono font-medium"><?= $h['codigo'] ?></span>
                </div>
                
                <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-1 line-clamp-2"><?= $h['titulo'] ?></h4>
                <div class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    <div class="flex flex-col gap-1 mt-3">
                        <span class="flex items-center gap-2"><i class="ph-fill ph-factory text-slate-400"></i> <strong class="text-slate-700 dark:text-slate-300">Fornecedor:</strong> <?= $h['fornecedor'] ?></span>
                        <span class="flex items-center gap-2"><i class="ph-fill ph-calendar text-slate-400"></i> <strong class="text-slate-700 dark:text-slate-300">Data Limite:</strong> <?= $h['data_prevista_chegada'] ? date('d/m/Y', strtotime($h['data_prevista_chegada'])) : '-' ?></span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 rounded-b-xl">
                <button type="button" onclick="openModal('receiveModal<?= $h['id'] ?>')" class="w-full flex justify-center items-center gap-2 text-amber-900 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-amber-200 font-bold rounded-lg text-sm px-5 py-2.5 dark:focus:ring-amber-900 transition-colors shadow-sm">
                    <i class="ph-bold ph-package"></i> Registrar Chegada da Carga
                </button>
            </div>
        </div>

        <!-- Modal Confirmação -->
        <div id="receiveModal<?= $h['id'] ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('receiveModal<?= $h['id'] ?>')"></div>
            <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform scale-100">
                <div class="bg-amber-400 px-6 py-4 flex justify-between items-center">
                    <h5 class="text-lg font-bold text-amber-950 flex items-center gap-2"><i class="ph-fill ph-clipboard-text"></i> Termo de Recebimento Físico</h5>
                    <button type="button" onclick="closeModal('receiveModal<?= $h['id'] ?>')" class="text-amber-800 hover:text-amber-950 transition-colors"><i class="ph-bold ph-x text-xl"></i></button>
                </div>
                <form method="POST" action="" class="p-6">
                    <input type="hidden" name="confirmar_recebimento_id" value="<?= $h['id'] ?>">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl mb-5 text-amber-800 dark:text-amber-300 text-sm flex gap-3">
                        <i class="ph-fill ph-info text-xl shrink-0"></i>
                        A conferência das peças (Nº Série, Qualidade) será feita pela TI durante a homologação. Apenas confirme que as caixas chegaram na porta.
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Data da Guarita</label>
                        <input type="date" name="data_recebimento" value="<?= date('Y-m-d') ?>" required class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Anotações Relevantes do Motorista</label>
                        <textarea name="observacoes" rows="2" placeholder="Caixa rasgada? Entregue via sedex?" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-slate-900 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('receiveModal<?= $h['id'] ?>')" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-amber-950 bg-amber-400 rounded-lg hover:bg-amber-500 shadow-sm">Carimbar Entrada</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div>
    <h3 class="text-lg font-bold flex items-center gap-2 text-slate-800 dark:text-white mb-4">
        <i class="ph-fill ph-check-square-offset text-cyan-500 text-2xl"></i> Caixas no Quarto / Pilha da Computação
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($recebidos)): ?>
            <div class="col-span-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 border-dashed rounded-xl p-8 text-center text-slate-500 dark:text-slate-400">
                A ilha de expedição interna está limpa! A TI levou tudo pendente.
            </div>
        <?php endif; ?>
        
        <?php foreach ($recebidos as $h): ?>
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 border-l-4 border-l-cyan-500 p-5 flex flex-col justify-between">
            <div class="mb-4">
                <div class="flex justify-between items-start mb-1">
                    <h5 class="font-bold text-slate-800 dark:text-white truncate"><?= $h['codigo'] ?></h5>
                    <span class="bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Na Fila TI</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1"><?= $h['titulo'] ?></p>
            </div>
            <div class="flex items-center justify-between mt-auto">
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300">
                        <i class="ph-fill ph-user text-xs"></i>
                    </div>
                    Recebido por <?= explode(' ', getUserById($h['recebido_por'])['nome'])[0] ?><br>em <?= date('d/m', strtotime($h['data_recebimento'])) ?>
                </div>
                <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="text-xs font-semibold text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300">Resumo Completo</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
