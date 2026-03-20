<?php require __DIR__ . '/_subnav.php'; 

if ($u['perfil'] !== 'responsavel' && $u['perfil'] !== 'admin' && $u['perfil'] !== 'super_admin') {
    echo "<div class='bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 mb-6 shadow-sm dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-300 flex items-center gap-3'><i class='ph-fill ph-warning-circle text-xl'></i> Acesso restrito. Sua conta não está na pool técnica para assumir Testes e Checagens de Homologação. Use o switch de simulador de usuário se desejar.</div>";
    return;
}
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Assinaturas Técnicas (QA de Hardware)</h2>
    <p class="text-slate-500 dark:text-slate-400 text-sm">O motor por trás da aprovação do que a empresa vai adotar.</p>
</div>

<?php if (empty($minha_fila)): ?>
    <div class="bg-emerald-50 border border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800 rounded-2xl p-8 shadow-sm flex items-center gap-6 mb-10">
        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-300 rounded-full flex items-center justify-center shrink-0">
            <i class="ph-fill ph-check-square-offset text-3xl"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-emerald-800 dark:text-emerald-400 mb-1">Fila Vazia, Missão Cumprida!</h3>
            <p class="text-emerald-700 dark:text-emerald-500 text-sm">Seu board de checklists está limpo. Você testou todos os equipamentos atribuídos a você e repassou os vereditos aos encarregados de compra.</p>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
        <?php foreach ($minha_fila as $h): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col hover:shadow-md transition-shadow <?= $h['status'] === 'em_homologacao' ? 'ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-slate-900' : '' ?>">
            <div class="<?= $h['status'] === 'em_homologacao' ? 'bg-primary-500 h-1.5' : 'bg-cyan-500 h-1.5' ?>"></div>
            
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-3">
                    <span class="bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 font-mono text-xs font-bold px-2.5 py-1 rounded tracking-widest uppercase">
                        <?= $h['codigo'] ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= getBadgeClass($h['status']) ?>">
                        <?= getStatusLabel($h['status']) ?>
                    </span>
                </div>
                
                <h4 class="text-lg font-bold text-slate-800 dark:text-white leading-tight mb-2"><?= $h['titulo'] ?></h4>
                <div class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-0 flex items-center gap-2 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700 overflow-hidden text-ellipsis whitespace-nowrap">
                    <i class="ph-fill <?= getIconForTipo($h['tipo_equipamento']) ?> text-lg shrink-0"></i> <?= $h['fornecedor'] ?> <?= $h['modelo'] ?>
                </div>
                
                <?php if ($h['status'] === 'em_homologacao'): ?>
                    <?php 
                        $total_items = count($data['checklists'][$h['tipo_equipamento']] ?? []);
                        $respondidos = count(array_filter($h['checklist_respostas'] ?? [], fn($r) => $r !== null));
                        $perc = $total_items > 0 ? round(($respondidos / $total_items) * 100) : 0;
                    ?>
                    <div class="mt-auto mb-6 bg-slate-50 dark:bg-slate-900 rounded-lg p-4 border border-slate-100 dark:border-slate-700/50">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Progresso Técnico</span>
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400"><?= $perc ?>%</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden flex">
                            <div class="bg-primary-500 h-2 transition-all duration-500" style="width: <?= $perc ?>%"></div>
                        </div>
                        <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 text-center"><?= $respondidos ?> de <?= $total_items ?> requisitos validados</div>
                    </div>
                <?php else: ?>
                    <div class="mt-auto mb-6 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4 border border-cyan-100 dark:border-cyan-800/50 flex gap-3 text-cyan-800 dark:text-cyan-400 text-sm font-medium items-center">
                        <i class="ph-fill ph-package text-3xl opacity-50 shrink-0"></i>
                        <div>
                            Doca recebida. O hardware te aguarda no depósito de TI. Puxe e inicie!
                        </div>
                    </div>
                <?php endif; ?>
                
                <a href="detalhe_homologacao.php?id=<?= $h['id'] ?>" class="text-center justify-center flex items-center gap-2 <?= $h['status'] === 'em_homologacao' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-slate-800 hover:bg-slate-700 text-white dark:bg-slate-700 dark:hover:bg-slate-600' ?> focus:ring-4 font-bold rounded-lg text-sm px-5 py-3 transition-colors shadow-sm mt-auto">
                    <?php if ($h['status'] === 'em_homologacao'): ?>
                        <i class="ph-fill ph-list-checks text-lg -mt-0.5"></i> Continuar Crivagem
                    <?php else: ?>
                        <i class="ph-fill ph-play-circle text-lg -mt-0.5"></i> Startar Avaliação
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="pt-8 border-t border-slate-200 dark:border-slate-700">
    <h3 class="text-lg font-bold flex items-center gap-2 text-slate-800 dark:text-white mb-4">
        <i class="ph-fill ph-clock-counter-clockwise text-slate-400 text-2xl"></i> Últimas Participações Técnicas (Mural de Vitórias)
    </h3>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
            <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700/50">
                <tr>
                    <th class="px-6 py-4">Selo Lacre</th>
                    <th class="px-6 py-4">Aparelho Certificado</th>
                    <th class="px-6 py-4">Fechamento do Ticket</th>
                    <th class="px-6 py-4 text-right">Resultado do Veredito</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                <?php if (empty($historico)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Nenhum certificado lavrado ainda na sua conta.</td></tr>
                <?php endif; ?>
                <?php foreach ($historico as $hist): ?>
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4 font-mono font-semibold text-slate-800 dark:text-slate-200"><?= $hist['codigo'] ?></td>
                    <td class="px-6 py-4 font-medium"><?= $hist['titulo'] ?></td>
                    <td class="px-6 py-4 text-slate-500"><?= isset($hist['data_fim_homologacao']) ? date('d/m/Y', strtotime($hist['data_fim_homologacao'])) : '-' ?></td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($hist['status'] === 'cancelada'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                <i class="ph-bold ph-prohibit"></i> DROPADA
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold <?= $hist['resultado'] === 'aprovado' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' ?>">
                                <i class="ph-bold <?= $hist['resultado'] === 'aprovado' ? 'ph-check' : 'ph-x' ?>"></i> <?= strtoupper($hist['resultado']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
