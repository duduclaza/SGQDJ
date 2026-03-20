<section class="space-y-8">
  <!-- Cabeçalho de Boas-vindas -->
  <div class="text-center">
    <div class="mb-6">
      <h1 class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-emerald-600 dark:from-blue-400 dark:to-emerald-400 mb-4 tracking-tight">SGI <span class="text-gray-900 dark:text-white">OTI</span></h1>
    </div>
    
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-slate-700 rounded-lg p-6 mb-8 border border-transparent dark:border-slate-600 shadow-sm">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Olá, <?= e($userName) ?>!</h2>
      <p class="text-gray-700 dark:text-gray-300">Perfil: <span class="font-medium text-blue-600 dark:text-blue-400"><?= e($userProfile) ?></span></p>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Utilize o menu lateral para navegar pelos módulos disponíveis para seu perfil.</p>
    </div>
  </div>
</section>
