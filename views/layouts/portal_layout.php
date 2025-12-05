<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal do Cliente - SGQ OTI DJ' ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">

    <?php if (isset($_SESSION['portal_cliente_id'])): ?>
        <!-- Header -->
        <nav class="gradient-bg shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-white text-xl font-bold">🖨️ Portal de Monitoramento</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-white text-sm">
                            👤 <?= htmlspecialchars($_SESSION['portal_cliente_nome']) ?>
                        </span>
                        <a href="/portal/logout" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Menu -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8">
                    <a href="/portal/dashboard" class="border-b-2 border-purple-500 text-gray-900 inline-flex items-center px-1 pt-1 pb-4 text-sm font-medium">
                        📊 Dashboard
                    </a>
                    <a href="/portal/impressoras" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 pb-4 text-sm font-medium">
                        🖨️ Impressoras
                    </a>
                    <a href="/portal/relatorios" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 pb-4 text-sm font-medium">
                        📄 Relatórios
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo -->
    <main class="py-10">
        <?php include $viewFile; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © <?= date('Y') ?> SGQ OTI DJ - Todos os direitos reservados
            </p>
        </div>
    </footer>

</body>
</html>
