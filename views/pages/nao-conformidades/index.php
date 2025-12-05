<?php
$isAdmin = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin']);
$isSuperAdmin = \App\Services\PermissionService::isSuperAdmin($_SESSION['user_id']);
?>

<div class="space-y-8 animate-fade-in">
  <!-- Header Premium -->
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-red-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50"></div>
    
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/20 text-white">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Não Conformidades</h1>
          <p class="text-gray-500 mt-1">Gestão de qualidade e melhoria contínua</p>
        </div>
      </div>

      <?php if ($isAdmin || $isSuperAdmin): ?>
      <button onclick="abrirModalNovaNC()" 
              class="group px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl flex items-center gap-3 font-medium">
        <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </div>
        Nova Ocorrência
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tabs Modernas -->
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="border-b border-gray-100 p-2 bg-gray-50/50">
      <nav class="flex gap-2" aria-label="Tabs">
        <button onclick="mudarAba('pendentes')" id="tab-pendentes" class="tab-modern active">
          <span class="w-2 h-2 rounded-full bg-red-500"></span>
          Pendentes
          <span class="badge-modern bg-red-100 text-red-700"><?= count($pendentes ?? []) ?></span>
        </button>
        
        <button onclick="mudarAba('em_andamento')" id="tab-em_andamento" class="tab-modern">
          <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
          Em Andamento
          <span class="badge-modern bg-yellow-100 text-yellow-700"><?= count($emAndamento ?? []) ?></span>
        </button>
        
        <button onclick="mudarAba('solucionadas')" id="tab-solucionadas" class="tab-modern">
          <span class="w-2 h-2 rounded-full bg-green-500"></span>
          Solucionadas
          <span class="badge-modern bg-green-100 text-green-700"><?= count($solucionadas ?? []) ?></span>
        </button>
      </nav>
    </div>

    <div class="p-6 bg-white min-h-[400px]">
      <!-- Aba Pendentes -->
      <div id="aba-pendentes" class="tab-content animate-fade-in">
        <?php $ncs = $pendentes ?? []; include 'partials/lista_ncs.php'; ?>
      </div>
      
      <!-- Aba Em Andamento -->
      <div id="aba-em_andamento" class="tab-content hidden animate-fade-in">
        <?php $ncs = $emAndamento ?? []; include 'partials/lista_ncs.php'; ?>
      </div>
      
      <!-- Aba Solucionadas -->
      <div id="aba-solucionadas" class="tab-content hidden animate-fade-in">
        <?php $ncs = $solucionadas ?? []; include 'partials/lista_ncs.php'; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/modais.php'; ?>
<?php include 'partials/scripts.php'; ?>

<!-- Script de Correção de Layout -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mover modais para o body para evitar problemas de z-index e overflow
  const modais = ['modalNovaNC', 'modalDetalhes', 'modalAcao'];
  const body = document.body;
  
  // Criar container global se não existir
  let globalContainer = document.getElementById('global-modals-container');
  if (!globalContainer) {
    globalContainer = document.createElement('div');
    globalContainer.id = 'global-modals-container';
    body.appendChild(globalContainer);
  }

  modais.forEach(id => {
    const modal = document.getElementById(id);
    if (modal) {
      globalContainer.appendChild(modal);
      console.log(`✅ Modal ${id} movido para container global`);
    }
  });
});
</script>

<style>
/* Animações */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }

/* Tabs Modernas */
.tab-modern {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.25rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.9375rem;
  color: #64748b;
  transition: all 0.2s;
  border: 1px solid transparent;
}

.tab-modern:hover {
  background: white;
  color: #1e293b;
}

.tab-modern.active {
  background: white;
  color: #0f172a;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  border-color: #e2e8f0;
}

.badge-modern {
  padding: 0.15rem 0.6rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 700;
}
</style>
