<?php
// views/pages/elearning/colaborador/meus_cursos.php
?>
<div class="space-y-12">

    <!-- Active Trainings / Continue Watching -->
    <?php 
    $emAndamento = array_filter($cursos, fn($c) => !empty($c['matricula_id']) && $c['matricula_status'] === 'em_andamento');
    if (!empty($emAndamento)): 
    ?>
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Continuar aprendendo</h2>
            <span class="text-sm font-bold text-blue-600"><?= count($emAndamento) ?> cursos em progresso</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($emAndamento as $c): 
                $pct = (float)($c['progresso_pct'] ?? 0);
            ?>
            <a href="/elearning/colaborador/cursos/<?= (int)$c['id'] ?>/continuar" class="group bg-white rounded-3xl border border-slate-200 overflow-hidden hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 flex">
                <div class="w-32 sm:w-40 h-auto shrink-0 bg-slate-100 border-r border-slate-100 overflow-hidden">
                    <?php if ($c['has_thumbnail']): ?>
                        <img src="/elearning/gestor/cursos/thumbnail?id=<?= (int)$c['id'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                            <span class="text-4xl">🎓</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 line-clamp-2 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($c['titulo']) ?></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Liderado por <?= htmlspecialchars($c['gestor_nome']) ?></p>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs font-bold mb-2">
                            <span class="text-slate-500">Seu progresso</span>
                            <span class="text-blue-600"><?= number_format($pct, 0) ?>%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-1000" style="width: <?= $pct ?>%"></div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Catalog -->
    <section>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">O que você quer aprender hoje?</h2>
                <p class="text-slate-500 mt-2 font-medium">Explore nossa biblioteca de treinamentos gratuitos e potencialize sua carreira.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="setFilter('all')" class="filter-btn active px-4 py-2 rounded-full text-sm font-bold transition-all">Todos</button>
                <button onclick="setFilter('not_enrolled')" class="filter-btn px-4 py-2 rounded-full text-sm font-bold text-slate-500 hover:bg-slate-200 transition-all">Disponíveis</button>
                <button onclick="setFilter('completed')" class="filter-btn px-4 py-2 rounded-full text-sm font-bold text-slate-500 hover:bg-slate-200 transition-all">Concluídos</button>
            </div>
        </div>

        <div id="catalogGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($cursos as $c): 
                $matriculado = !empty($c['matricula_id']);
                $concluido = ($c['matricula_status'] ?? '') === 'concluido';
                $statusClass = $concluido ? 'completed' : ($matriculado ? 'enrolled' : 'not_enrolled');
            ?>
            <article class="curso-card flex flex-col group bg-white rounded-[2rem] border border-slate-200 overflow-hidden hover:border-blue-200 hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-300" 
                     data-status="<?= $statusClass ?>"
                     data-title="<?= strtolower(htmlspecialchars($c['titulo'])) ?>">
                
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <?php if ($c['has_thumbnail']): ?>
                        <img src="/elearning/gestor/cursos/thumbnail?id=<?= (int)$c['id'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-200 to-slate-300 group-hover:scale-110 transition-transform duration-700">
                            <span class="text-6xl grayscale opacity-30">🎓</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                        <p class="text-white text-xs font-medium line-clamp-3"><?= htmlspecialchars($c['descricao'] ?: 'Sem descrição disponível.') ?></p>
                    </div>

                    <?php if ($concluido): ?>
                        <div class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                            <span>✅</span> CONCLUÍDO
                        </div>
                    <?php endif; ?>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 leading-snug group-hover:text-blue-600 transition-colors line-clamp-2">
                            <?= htmlspecialchars($c['titulo'] ?: 'Curso sem título') ?>
                        </h3>
                        <p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-slate-100 flex items-center justify-center text-[10px]">👤</span>
                            <?= htmlspecialchars($c['gestor_nome']) ?>
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-4 text-slate-400">
                            <div class="flex items-center gap-1.5">
                                <span class="text-base">⏱</span>
                                <span class="text-xs font-bold tracking-tight"><?= (int)$c['carga_horaria'] ?>m</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-base">📖</span>
                                <span class="text-xs font-bold tracking-tight"><?= (int)$c['total_aulas'] ?></span>
                            </div>
                        </div>

                        <?php if ($matriculado): ?>
                            <a href="/elearning/colaborador/cursos/<?= (int)$c['id'] ?>/continuar" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-2xl text-xs font-black sm:hover:bg-blue-600 sm:hover:text-white transition-all">
                                CONTINUAR
                            </a>
                        <?php else: ?>
                            <button onclick="matricular(<?= (int)$c['id'] ?>)" class="px-5 py-2.5 bg-blue-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-blue-200 sm:hover:bg-blue-700 sm:hover:shadow-blue-300 transition-all">
                                INSCREVER-SE
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
    .filter-btn.active {
        background-color: #3b82f6;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    }
</style>

<script>
    function setFilter(status) {
        // Update UI
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
            btn.classList.add('text-slate-500', 'hover:bg-slate-200');
        });
        
        const activeBtn = event.target;
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
        activeBtn.classList.remove('text-slate-500', 'hover:bg-slate-200');

        // Filter cards
        const cards = document.querySelectorAll('.curso-card');
        cards.forEach(card => {
            if (status === 'all') {
                card.style.display = 'flex';
            } else if (status === 'not_enrolled') {
                card.style.display = card.dataset.status === 'not_enrolled' ? 'flex' : 'none';
            } else if (status === 'completed') {
                card.style.display = card.dataset.status === 'completed' ? 'flex' : 'none';
            }
        });
    }

    // Integrated search from layout
    window.filtrarCursos = function(query) {
        query = query.toLowerCase();
        document.querySelectorAll('.curso-card').forEach(card => {
            const title = card.dataset.title;
            card.style.display = title.includes(query) ? 'flex' : 'none';
        });
    };

    async function matricular(cursoId) {
        // No confirmation needed for free internal courses
        const fd = new FormData();
        fd.append('curso_id', cursoId);
        fd.append('redirect', '1'); // Redirecionar direto pro curso

        try {
            const res = await fetch('/elearning/colaborador/matricular', { method: 'POST', body: fd });
            const data = await res.json();
            
            if (data.success) {
                showToast('Inscrição realizada! Bons estudos.', 'success');
                if (data.redirect_url) {
                    setTimeout(() => window.location.href = data.redirect_url + '/continuar', 1000);
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showToast(data.message, 'error');
            }
        } catch(e) {
            showToast('Erro de conexão', 'error');
        }
    }
</script>
