<?php
// views/pages/elearning/colaborador/curso_detalhe.php
?>
<div class="max-w-5xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-8 flex items-center gap-2 text-sm font-bold">
        <a href="/elearning/colaborador" class="text-slate-400 hover:text-blue-600 transition-colors">Catálogo</a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 line-clamp-1"><?= htmlspecialchars($curso['titulo']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content (Course Content) -->
        <div class="lg:col-span-2 space-y-12">
            <section>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight"><?= htmlspecialchars($curso['titulo']) ?></h1>
                <p class="text-slate-500 mt-4 text-lg font-medium leading-relaxed">
                    <?= htmlspecialchars($curso['descricao'] ?: 'Domine as competências necessárias com este treinamento focado e prático.') ?>
                </p>
                
                <div class="flex flex-wrap items-center gap-6 mt-8 text-sm font-bold text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs">👤</span>
                        <span>Liderado por <?= htmlspecialchars($curso['gestor_nome'] ?? 'Equipe SGQ') ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs">⏱</span>
                        <span><?= (int)$curso['carga_horaria'] ?> minutos</span>
                    </div>
                </div>
            </section>

            <!-- Course Curriculum -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Grade Curricular</h2>
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-widest"><?= count($aulas) ?> Módulos</span>
                </div>

                <div class="space-y-4">
                    <?php if (empty($aulas)): ?>
                        <div class="p-12 text-center bg-white rounded-[2rem] border-2 border-dashed border-slate-200">
                            <p class="text-slate-400 font-bold italic">Este curso ainda não possui aulas cadastradas.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($aulas as $idx => $a): 
                            $mats = $materiaisByAula[$a['id']] ?? [];
                        ?>
                        <div class="group bg-white rounded-[1.5rem] border border-slate-200 overflow-hidden hover:border-blue-200 transition-all duration-300">
                            <button onclick="toggleAccordion(<?= (int)$a['id'] ?>)" class="w-full p-6 flex items-center justify-between text-left group-hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center gap-6">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs group-hover:bg-blue-100 group-hover:text-blue-600 transition-all">
                                        <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800 group-hover:text-slate-900 transition-colors"><?= htmlspecialchars($a['titulo']) ?></h3>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= count($mats) ?> Materiais registrados</p>
                                    </div>
                                </div>
                                <svg id="chevron_<?= (int)$a['id'] ?>" class="w-5 h-5 text-slate-300 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="content_<?= (int)$a['id'] ?>" class="hidden px-6 pb-6">
                                <div class="space-y-2 pt-2 border-t border-slate-100">
                                    <?php foreach ($mats as $m): 
                                        $visto = !empty($progresso[$m['id']]);
                                        $icones = ['video' => '🎬', 'pdf' => '📄', 'imagem' => '🖼️', 'texto' => '📝'];
                                    ?>
                                    <a href="/elearning/colaborador/materiais/<?= (int)$m['id'] ?>/assistir" target="_blank"
                                       class="flex items-center justify-between p-4 rounded-2xl hover:bg-blue-50 group/item transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-xl <?= $visto ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center text-sm shadow-sm group-hover/item:bg-white transition-all">
                                                <?= $visto ? '✅' : ($icones[$m['tipo']] ?? '📁') ?>
                                            </div>
                                            <span class="text-sm font-bold <?= $visto ? 'text-slate-500' : 'text-slate-700' ?> group-hover/item:text-blue-700 transition-colors">
                                                <?= htmlspecialchars($m['titulo']) ?>
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest group-hover/item:text-blue-400 transition-colors"><?= $m['tipo'] ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Exams -->
            <?php if (!empty($provas)): ?>
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Avaliações Finalizadoras</h2>
                </div>
                <div class="space-y-4">
                    <?php foreach ($provas as $p): 
                         $bloqueado = (int)$p['tentativas_feitas'] >= (int)$p['tentativas_max'];
                    ?>
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2rem] p-8 text-white shadow-xl shadow-indigo-900/10 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="text-center sm:text-left">
                            <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">Prova Obrigatória</div>
                            <h3 class="text-xl font-black"><?= htmlspecialchars($p['titulo']) ?></h3>
                            <div class="flex flex-wrap justify-center sm:justify-start gap-4 mt-3 text-indigo-100 text-xs font-bold">
                                <span>🎯 Mínimo: <?= $p['nota_minima'] ?>%</span>
                                <span>🔄 Tentativas: <?= $p['tentativas_feitas'] ?>/<?= $p['tentativas_max'] ?></span>
                            </div>
                        </div>
                        
                        <?php 
                        // Calcular se pode fazer a prova (todos materiais vistos)
                        $totalMateriais = 0;
                        foreach($materiaisByAula as $ma) $totalMateriais += count($ma);
                        $totalVistos = count(array_filter($progresso, fn($v) => $v == 1));
                        $tudoVisto = $totalVistos >= $totalMateriais && $totalMateriais > 0;
                        ?>

                        <?php if (!$bloqueado): ?>
                            <?php if ($tudoVisto): ?>
                                <a href="/elearning/colaborador/provas/<?= (int)$p['id'] ?>/fazer" 
                                   class="px-8 py-4 bg-white text-indigo-700 rounded-2xl text-sm font-black hover:scale-105 active:scale-95 transition-all shadow-xl shadow-black/10">
                                    INICIAR AVALIAÇÃO
                                </a>
                            <?php else: ?>
                                <button onclick="alert('Você precisa concluir todos os <?= $totalMateriais ?> materiais do curso antes de realizar a prova. (Concluídos: <?= $totalVistos ?>)')"
                                   class="px-8 py-4 bg-white/20 text-white/80 border border-white/30 rounded-2xl text-sm font-black cursor-not-allowed">
                                    🔒 CONCLUA O CURSO PRIMEIRO
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                        <div class="px-8 py-4 bg-white/10 border border-white/20 text-white/60 rounded-2xl text-sm font-black cursor-not-allowed">
                            TENTATIVAS ESGOTADAS
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar (Progress & Certificate) -->
        <div class="space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-xl shadow-slate-200/50 sticky top-24">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Status do Curso</h3>
                
                <!-- Progress Circular / Large Info -->
                <div class="relative flex flex-col items-center justify-center p-8 bg-slate-50 rounded-[2rem] border border-slate-100">
                    <?php $pct = (float)($matricula['progresso_pct'] ?? 0); ?>
                    <div class="text-5xl font-black text-blue-600"><?= number_format($pct, 0) ?><span class="text-2xl text-blue-400">%</span></div>
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mt-2">Concluído</div>
                    
                    <div class="w-full h-2 bg-slate-200 rounded-full mt-8 overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-1000" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-8">
                    <?php if ($pct < 100): ?>
                        <p class="text-xs font-bold text-slate-400 text-center mb-4">Continue assistindo para liberar seu certificado.</p>
                    <?php endif; ?>
                </div>

                <!-- Certificate -->
                <?php if ($certificado): ?>
                <div class="mt-8 pt-8 border-t border-slate-100 animate-bounce">
                    <a href="/elearning/colaborador/certificados/<?= htmlspecialchars($certificado['codigo_validacao']) ?>" target="_blank"
                       class="w-full flex items-center justify-center gap-3 px-6 py-5 bg-emerald-500 text-white rounded-3xl text-sm font-black shadow-xl shadow-emerald-200 hover:bg-emerald-600 hover:shadow-emerald-300 transition-all">
                        <span>📄</span> BAIXAR CERTIFICADO
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAccordion(id) {
        const content = document.getElementById('content_' + id);
        const chevron = document.getElementById('chevron_' + id);
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }
</script>

