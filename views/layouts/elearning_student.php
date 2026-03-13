<!DOCTYPE html>
<html lang="pt-br" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'eLearning - SGQ OTI DJ' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-header { 
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .search-focus:focus-within {
            ring: 2px;
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="h-full antialiased text-slate-900">
    <!-- Header -->
    <header class="sticky top-0 z-50 glass-header">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Logo & Title -->
            <div class="flex items-center gap-4 shrink-0">
                <a href="/inicio" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200 group-hover:scale-105 transition-transform">
                        <span class="text-white text-xl">🎓</span>
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-lg font-bold tracking-tight text-slate-800">eLearning</span>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-widest leading-none">SGQ OTI DJ</p>
                    </div>
                </a>
            </div>

            <!-- Search Bar (Centered-ish) -->
            <div class="flex-1 max-w-2xl hidden md:block">
                <div class="relative group search-focus transition-all rounded-full bg-slate-100 border border-transparent px-4 py-2 flex items-center gap-3 focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-50">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="globalSearch" placeholder="O que você quer aprender hoje?" 
                           class="bg-transparent border-none focus:ring-0 w-full text-sm font-medium placeholder-slate-400 outline-none">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3 sm:gap-6 shrink-0">
                <nav class="hidden lg:flex items-center gap-6">
                    <a href="/elearning/colaborador" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Meus Cursos</a>
                    <a href="/elearning/colaborador/certificados" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Certificados</a>
                </nav>

                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-sm font-bold text-slate-700"><?= $_SESSION['user_name'] ?? 'Usuário' ?></span>
                        <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Colaborador</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-200 to-slate-300 border-2 border-white shadow-sm flex items-center justify-center">
                        <span class="text-slate-600 font-bold text-sm"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php include $viewFile; ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-12 mt-12">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2 grayscale opacity-50">
                    <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center">
                        <span class="text-white text-sm">🎓</span>
                    </div>
                    <span class="font-bold text-slate-800">eLearning</span>
                </div>
                <p class="text-slate-400 text-sm">© <?= date('Y') ?> SGQ OTI DJ - Todos os direitos reservados.</p>
                <div class="flex items-center gap-6">
                    <a href="/inicio" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">Voltar ao SGQ</a>
                    <a href="/suporte" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">Suporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Component -->
    <div id="elToast" class="fixed bottom-6 right-6 z-[100] transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div id="elToastContent" class="flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl text-white font-bold">
            <span id="elToastIcon"></span>
            <span id="elToastMsg"></span>
        </div>
    </div>

    <script>
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('elToast');
            const content = document.getElementById('elToastContent');
            const icon = document.getElementById('elToastIcon');
            const msgEl = document.getElementById('elToastMsg');

            const themes = {
                success: { bg: 'bg-emerald-600', icon: '✅' },
                error: { bg: 'bg-rose-600', icon: '❌' },
                info: { bg: 'bg-blue-600', icon: 'ℹ️' }
            };

            const theme = themes[type] || themes.info;
            
            content.className = `flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl text-white font-bold ${theme.bg}`;
            icon.textContent = theme.icon;
            msgEl.textContent = msg;

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }

        // Search logic from layout if needed
        const globalSearch = document.getElementById('globalSearch');
        if (globalSearch) {
            globalSearch.addEventListener('input', function() {
                if (typeof filtrarCursos === 'function') {
                    filtrarCursos(this.value);
                }
            });
        }
    </script>
</body>
</html>
