<?php
// Determine the view to load based on the current request
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Map routes to view files
$viewMap = [
    '/melhoria-continua/solicitacoes' => 'melhoria-continua/solicitacoes.php',
    // Add other routes as needed
];

// Find the correct view file
$viewFile = null;
foreach ($viewMap as $route => $view) {
    if ($path === $route) {
        $viewFile = $view;
        break;
    }
}

// If no specific view found, try to determine from path
if (!$viewFile) {
    // Default fallback
    $viewFile = 'dashboard.php';
}

// Function to safely escape output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGQ OTI DJ - Sistema de Gestão da Qualidade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/src/Support/modal-styles.css">
    <style>
        .submenu { transition: all 0.3s ease; }
        .submenu.hidden { max-height: 0; opacity: 0; }
        .submenu:not(.hidden) { max-height: 500px; opacity: 1; }
        
        /* Estilos globais para alertas JavaScript */
        .swal2-popup {
            border-radius: 1.5rem !important;
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.25) !important;
        }
        
        /* Melhorar aparência dos alerts nativos */
        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(30, 41, 59, 0.9));
            backdrop-filter: blur(8px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .alert-box {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border-radius: 1.5rem;
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem;
            max-width: 28rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .alert-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .alert-message {
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .alert-buttons {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }
        
        .alert-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .alert-btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
        }
        
        .alert-btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
        }
        
        .alert-btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #64748b;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .alert-btn-secondary:hover {
            background: rgba(248, 250, 252, 0.95);
            color: #475569;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Aviso de Migração do Servidor de E-mail -->
            <?php if (!isset($_COOKIE['aviso_email_fechado'])): ?>
            <div id="avisoMigracaoEmail" class="mx-4 mt-4 mb-2">
                <div class="relative overflow-hidden bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 border border-amber-200 rounded-xl shadow-lg">
                    <!-- Ícone decorativo de fundo -->
                    <div class="absolute -right-4 -top-4 text-amber-100 text-9xl opacity-30 pointer-events-none">📧</div>
                    
                    <div class="relative p-4 md:p-5">
                        <div class="flex items-start gap-4">
                            <!-- Ícone principal -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Conteúdo -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        🔧 Manutenção
                                    </span>
                                    <span class="text-xs text-gray-500">Aviso Importante</span>
                                </div>
                                <h4 class="text-base font-bold text-gray-800 mb-1">
                                    Migração do Servidor de Notificações por E-mail
                                </h4>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Estamos realizando a <strong>migração do nosso servidor de e-mails</strong> para oferecer um serviço ainda melhor. 
                                    Durante este período, as notificações por e-mail estão temporariamente suspensas.
                                </p>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <div class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-amber-200 shadow-sm">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-700">Previsão de retorno: <span class="text-green-600">16 de Dezembro</span></span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    🙏 Agradecemos sua compreensão e paciência! Em breve tudo estará funcionando normalmente.
                                </p>
                            </div>
                            
                            <!-- Botão Fechar -->
                            <button onclick="fecharAvisoEmail()" class="flex-shrink-0 p-1.5 rounded-lg hover:bg-amber-100 transition-colors group" title="Fechar aviso">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function fecharAvisoEmail() {
                    const aviso = document.getElementById('avisoMigracaoEmail');
                    aviso.style.transition = 'all 0.3s ease-out';
                    aviso.style.opacity = '0';
                    aviso.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        aviso.remove();
                        // Cookie expira em 24 horas
                        document.cookie = 'aviso_email_fechado=1; path=/; max-age=86400';
                    }, 300);
                }
            </script>
            <?php endif; ?>
            
            <div class="p-6">
                <?php
                $fullPath = __DIR__ . '/' . $viewFile;
                if (file_exists($fullPath)) {
                    include $fullPath;
                } else {
                    echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">';
                    echo 'Erro: View não encontrada - ' . e($viewFile);
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
    
    <!-- Script para modais personalizados -->
    <script>
        // Função para criar alertas personalizados mais bonitos
        window.showAlert = function(message, type = 'info', title = '') {
            return new Promise((resolve) => {
                // Remove alertas existentes
                const existingAlert = document.querySelector('.alert-overlay');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Define ícones e títulos baseados no tipo
                const config = {
                    success: { icon: '✅', title: title || 'Sucesso!', class: 'success-modal' },
                    error: { icon: '❌', title: title || 'Erro!', class: 'error-modal' },
                    warning: { icon: '⚠️', title: title || 'Atenção!', class: 'alert-modal' },
                    info: { icon: 'ℹ️', title: title || 'Informação', class: 'alert-modal' }
                };
                
                const alertConfig = config[type] || config.info;
                
                // Cria o overlay
                const overlay = document.createElement('div');
                overlay.className = 'alert-overlay';
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s ease';
                
                // Cria o modal
                overlay.innerHTML = `
                    <div class="alert-box ${alertConfig.class}" style="transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <div class="alert-title">
                            <span style="font-size: 1.5rem;">${alertConfig.icon}</span>
                            ${alertConfig.title}
                        </div>
                        <div class="alert-message">${message}</div>
                        <div class="alert-buttons">
                            <button class="alert-btn alert-btn-primary" onclick="this.closest('.alert-overlay').remove(); resolve(true);">
                                OK
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(overlay);
                
                // Animação de entrada
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                    const box = overlay.querySelector('.alert-box');
                    box.style.transform = 'scale(1)';
                });
                
                // Fechar com ESC
                const handleEsc = (e) => {
                    if (e.key === 'Escape') {
                        overlay.remove();
                        document.removeEventListener('keydown', handleEsc);
                        resolve(true);
                    }
                };
                document.addEventListener('keydown', handleEsc);
                
                // Fechar clicando fora
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        overlay.remove();
                        resolve(true);
                    }
                });
            });
        };
        
        // Função para confirmações personalizadas
        window.showConfirm = function(message, title = 'Confirmação') {
            return new Promise((resolve) => {
                // Remove alertas existentes
                const existingAlert = document.querySelector('.alert-overlay');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Cria o overlay
                const overlay = document.createElement('div');
                overlay.className = 'alert-overlay';
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s ease';
                
                // Cria o modal
                overlay.innerHTML = `
                    <div class="alert-box confirm-modal" style="transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <div class="alert-title">
                            <span style="font-size: 1.5rem;">❓</span>
                            ${title}
                        </div>
                        <div class="alert-message">${message}</div>
                        <div class="alert-buttons">
                            <button class="alert-btn alert-btn-secondary" onclick="this.closest('.alert-overlay').remove(); resolve(false);">
                                Cancelar
                            </button>
                            <button class="alert-btn alert-btn-primary" onclick="this.closest('.alert-overlay').remove(); resolve(true);">
                                OK
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(overlay);
                
                // Animação de entrada
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                    const box = overlay.querySelector('.alert-box');
                    box.style.transform = 'scale(1)';
                });
                
                // Fechar com ESC (cancelar)
                const handleEsc = (e) => {
                    if (e.key === 'Escape') {
                        overlay.remove();
                        document.removeEventListener('keydown', handleEsc);
                        resolve(false);
                    }
                };
                document.addEventListener('keydown', handleEsc);
                
                // Fechar clicando fora (cancelar)
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        overlay.remove();
                        resolve(false);
                    }
                });
            });
        };
        
        // Substituir alert nativo
        const originalAlert = window.alert;
        window.alert = function(message) {
            return showAlert(message, 'info');
        };
        
        // Substituir confirm nativo
        const originalConfirm = window.confirm;
        window.confirm = function(message) {
            return showConfirm(message);
        };
        
        // Função para mostrar notificações de toast
        window.showToast = function(message, type = 'success', duration = 3000) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300`;
            
            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-yellow-500 text-black',
                info: 'bg-blue-500 text-white'
            };
            
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            toast.className += ` ${colors[type] || colors.info}`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <span style="font-size: 1.2rem;">${icons[type] || icons.info}</span>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animação de entrada
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
            });
            
            // Remover após duração especificada
            setTimeout(() => {
                toast.style.transform = 'translateX(full)';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        };
    </script>
</body>
</html>
