<?php 
// Segurança para não acessar diretamente
if (!defined('__DIR__')) exit; 

$modulo = isset($_GET['module']) ? htmlspecialchars($_GET['module'], ENT_QUOTES, 'UTF-8') : 'Premium';
$whatsappNumber = "5535984418378";
$whatsappMessage = urlencode("Olá! Gostaria de receber uma demonstração/amostra do Módulo {$modulo} no meu sistema.");
$whatsappLink = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";
?>
<div class="h-[calc(100vh-80px)] w-full flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Efeitos de Luz Premium Estilo ERP SAP -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Container Principal do Hub de Vendas -->
    <div class="relative z-10 max-w-lg w-full">
        <!-- Card -->
        <div class="bg-white/50 dark:bg-slate-900/80 backdrop-blur-xl border border-white/50 dark:border-slate-800 rounded-2xl shadow-2xl p-8 sm:p-12 text-center transform transition-all duration-500 hover:scale-[1.01]">
            
            <!-- Ícone Pulsante -->
            <div class="mx-auto w-24 h-24 mb-8 relative flex items-center justify-center">
                <div class="absolute inset-0 bg-blue-100 dark:bg-blue-900/40 rounded-full animate-ping opacity-75"></div>
                <div class="relative bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-full w-20 h-20 flex items-center justify-center text-white shadow-lg border-4 border-white dark:border-slate-950">
                    <i class="ph-fill ph-lock-key text-3xl"></i>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold tracking-wider uppercase mb-6 shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Módulo <?= $modulo ?>
            </div>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white tracking-tight mb-4 font-sans">
                Acesso Limitado
            </h2>
            
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg mb-10 leading-relaxed max-w-md mx-auto">
                Este módulo de alta performance não está ativo em sua assinatura atual. Solicite uma <strong class="text-blue-600 dark:text-blue-400 font-semibold">amostra grátis</strong> diretamente ao nosso centro de tecnologia.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?= $whatsappLink ?>" target="_blank" rel="noopener noreferrer" 
                   class="group w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-3.5 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold rounded-xl shadow-[0_8px_20px_rgba(37,211,102,0.3)] hover:shadow-[0_12px_25px_rgba(37,211,102,0.4)] transition-all duration-300">
                    <i class="ph-fill ph-whatsapp-logo text-2xl group-hover:scale-110 transition-transform"></i>
                    <span>Falar no WhatsApp</span>
                </a>
                
                <a href="/inicio" 
                   class="w-full sm:w-auto flex items-center justify-center px-6 py-3.5 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium transition-colors duration-200">
                    Sair e voltar ao início
                </a>
            </div>
            
            <!-- Badges Visuais (Efeito Sistema ERP Premium) -->
            <div class="mt-12 flex items-center justify-center gap-6 border-t border-slate-200 dark:border-slate-800/60 pt-6">
                <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                    <i class="ph-fill ph-shield-check text-blue-500"></i>
                    <span>Criptografia de Ponta</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                    <i class="ph-fill ph-lightning text-amber-500"></i>
                    <span>API SAP Integrada</span>
                </div>
            </div>
        </div>
    </div>
</div>
