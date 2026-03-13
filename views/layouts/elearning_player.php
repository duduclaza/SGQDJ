<!DOCTYPE html>
<html lang="pt-br" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'eLearning - SGQ OTI DJ' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .player-sidebar { height: calc(100vh - 64px); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full overflow-hidden">
    <div class="flex flex-col h-full uppercase-none">
        <!-- Header -->
        <header class="h-16 bg-slate-900 text-white flex items-center justify-between px-6 z-10 shrink-0">
            <div class="flex items-center space-x-4">
                <a href="/inicio" class="hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold truncate max-w-md"><?= $curso['titulo'] ?? 'Curso' ?></h1>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-xs text-slate-400">Seu Progresso</span>
                    <div class="w-48 h-2 bg-slate-700 rounded-full mt-1 overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: <?= $matricula['progresso_pct'] ?? 0 ?>%"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-sm"><?= $matricula['progresso_pct'] ?? 0 ?>%</span>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex flex-1 overflow-hidden">
            <!-- Central Player / Content -->
            <div class="flex-1 overflow-y-auto bg-black flex flex-col">
                <?php include $viewFile; ?>
            </div>

            <!-- Right Sidebar (Playlist) -->
            <aside class="w-96 bg-white border-l border-gray-200 flex flex-col shrink-0 overflow-hidden hidden lg:flex">
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Conteúdo do Curso</h2>
                    <span class="text-xs text-gray-500"><?= count($aulas) ?> aulas</span>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <?php foreach ($aulas as $aula): ?>
                        <div class="border-b border-gray-100">
                            <div class="p-4 bg-gray-50 flex items-center justify-between cursor-pointer hover:bg-gray-100">
                                <span class="font-semibold text-sm text-gray-700"><?= $aula['titulo'] ?></span>
                            </div>
                            <div class="bg-white">
                                <?php if (!empty($aula['materiais'])): ?>
                                    <?php foreach ($aula['materiais'] as $mat): ?>
                                        <a href="/elearning/assistir/<?= $mat['id'] ?>" 
                                           class="flex items-center p-4 hover:bg-blue-50 transition-colors <?= ($material['id'] ?? 0) == $mat['id'] ? 'bg-blue-50' : '' ?>">
                                            <div class="mr-3">
                                                <?php if (($mat['progresso']['visualizado'] ?? 0) == 1): ?>
                                                    <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium <?= ($material['id'] ?? 0) == $mat['id'] ? 'text-blue-700' : 'text-gray-600' ?>">
                                                    <?= $mat['titulo'] ?>
                                                </p>
                                                <div class="flex items-center mt-1 text-xs text-gray-400">
                                                    <?php if ($mat['tipo'] === 'video'): ?>
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Vídeo
                                                    <?php elseif ($mat['tipo'] === 'pdf'): ?>
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                        Documento PDF
                                                    <?php else: ?>
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                                        Texto / Leitura
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </main>
    </div>
</body>
</html>
