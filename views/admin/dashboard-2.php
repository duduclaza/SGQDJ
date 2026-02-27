<?php
if (!function_exists('e')) {
  function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
}

$triagemStats = $triagemStats ?? [
  'total_registros' => 0,
  'media_percentual' => 0,
  'total_estoque' => 0,
  'valor_recuperado' => 0,
  'por_destino' => [],
  'ultimos_registros' => [],
];

$moduloAtual = strtolower(trim((string)($_GET['modulo'] ?? '')));
?>

<section class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard 2.0</h1>
      <p class="text-sm text-gray-600 mt-1">Visão executiva dos principais indicadores</p>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <a href="/dashboard-2?modulo=triagem" class="group bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md hover:border-cyan-300 transition-all">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-base font-semibold text-gray-900">Triagem de Toners</h2>
          <p class="text-sm text-gray-600 mt-1">Acessar indicadores de triagem e valor recuperado.</p>
        </div>
        <span class="text-xl">🧪</span>
      </div>
      <div class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-cyan-700 group-hover:text-cyan-800">
        Entrar no dashboard
        <span>→</span>
      </div>
    </a>
  </div>

  <?php if ($moduloAtual === 'triagem'): ?>
  <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-xl">
    <div class="absolute -top-20 -right-20 h-56 w-56 rounded-full bg-cyan-400/20 blur-3xl"></div>
    <div class="absolute -bottom-16 -left-12 h-48 w-48 rounded-full bg-blue-500/20 blur-3xl"></div>

    <div class="relative px-6 py-5 border-b border-white/10 flex items-center justify-between gap-3">
      <div>
        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-[11px] font-medium uppercase tracking-[0.08em] text-cyan-100">
          Dashboard de Performance
        </span>
        <h2 class="mt-3 text-xl font-semibold text-white">Triagem de Toners</h2>
        <p class="text-sm text-slate-300 mt-1">Painel inteligente de volume, destino e valor recuperado</p>
      </div>
      <a href="/dashboard-2" class="text-sm px-3 py-1.5 rounded-lg border border-slate-500/60 bg-slate-900/60 text-slate-200 hover:bg-slate-800 transition-colors">Voltar</a>
    </div>

    <div class="relative p-6 space-y-5">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-4">
          <div class="text-[11px] uppercase tracking-[0.08em] text-slate-300">Total de Registros</div>
          <div class="text-3xl font-semibold text-white mt-2"><?= number_format((int)$triagemStats['total_registros'], 0, ',', '.') ?></div>
        </div>

        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-4">
          <div class="text-[11px] uppercase tracking-[0.08em] text-slate-300">Média % Gramatura</div>
          <div class="text-3xl font-semibold text-white mt-2"><?= number_format((float)$triagemStats['media_percentual'], 2, ',', '.') ?>%</div>
        </div>

        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-4">
          <div class="text-[11px] uppercase tracking-[0.08em] text-slate-300">Triagens em Estoque</div>
          <div class="text-3xl font-semibold text-white mt-2"><?= number_format((int)$triagemStats['total_estoque'], 0, ',', '.') ?></div>
        </div>

        <div class="rounded-xl border border-emerald-300/40 bg-emerald-400/10 backdrop-blur-sm p-4">
          <div class="text-[11px] uppercase tracking-[0.08em] text-emerald-100">Valor Recuperado</div>
          <div class="text-3xl font-semibold text-emerald-200 mt-2">R$ <?= number_format((float)$triagemStats['valor_recuperado'], 2, ',', '.') ?></div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-4">
          <h3 class="text-sm font-semibold text-white mb-3">Distribuição por Destino</h3>
          <?php if (empty($triagemStats['por_destino'])): ?>
            <p class="text-sm text-slate-300">Sem dados para exibir.</p>
          <?php else: ?>
            <div class="space-y-2">
              <?php foreach ($triagemStats['por_destino'] as $item): ?>
                <div class="flex items-center justify-between rounded-lg border border-white/10 bg-slate-900/40 px-3 py-2 text-sm">
                  <span class="text-slate-200"><?= e($item['destino']) ?></span>
                  <span class="font-semibold text-white"><?= (int)$item['total'] ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-4">
          <h3 class="text-sm font-semibold text-white mb-3">Últimas Triagens</h3>
          <?php if (empty($triagemStats['ultimos_registros'])): ?>
            <p class="text-sm text-slate-300">Sem registros recentes.</p>
          <?php else: ?>
            <div class="max-h-72 overflow-auto rounded-lg border border-white/10">
              <table class="w-full text-sm">
                <thead>
                  <tr class="text-left text-slate-300 border-b border-white/10 bg-slate-900/50">
                    <th class="py-2.5 px-3">Cliente</th>
                    <th class="py-2.5 px-3">Modelo</th>
                    <th class="py-2.5 px-3">%</th>
                    <th class="py-2.5 px-3">Destino</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($triagemStats['ultimos_registros'] as $row): ?>
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                      <td class="py-2.5 px-3 text-slate-200"><?= e($row['cliente_nome'] ?? '-') ?></td>
                      <td class="py-2.5 px-3 text-slate-200"><?= e($row['toner_modelo'] ?? '-') ?></td>
                      <td class="py-2.5 px-3 font-semibold text-white"><?= number_format((float)($row['percentual_calculado'] ?? 0), 2, ',', '.') ?>%</td>
                      <td class="py-2.5 px-3 text-slate-200"><?= e($row['destino'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
