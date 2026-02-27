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
?>

<section class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard 2.0</h1>
      <p class="text-sm text-gray-600 mt-1">Visão executiva dos principais indicadores</p>
    </div>
  </div>

  <!-- Bloco solicitado -->
  <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-blue-50">
      <h2 class="text-lg font-semibold text-gray-900">Dashboard de Triagem de Toners</h2>
      <p class="text-sm text-gray-600 mt-1">Resumo de desempenho e destino das triagens</p>
    </div>

    <div class="p-5 space-y-5">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
          <div class="text-xs text-gray-500 uppercase">Total de Registros</div>
          <div class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((int)$triagemStats['total_registros'], 0, ',', '.') ?></div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
          <div class="text-xs text-gray-500 uppercase">Média % Gramatura</div>
          <div class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((float)$triagemStats['media_percentual'], 2, ',', '.') ?>%</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
          <div class="text-xs text-gray-500 uppercase">Triagens em Estoque</div>
          <div class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((int)$triagemStats['total_estoque'], 0, ',', '.') ?></div>
        </div>

        <div class="rounded-lg border border-emerald-200 p-4 bg-emerald-50">
          <div class="text-xs text-emerald-700 uppercase">Valor Recuperado</div>
          <div class="text-2xl font-bold text-emerald-700 mt-1">R$ <?= number_format((float)$triagemStats['valor_recuperado'], 2, ',', '.') ?></div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="text-sm font-semibold text-gray-800 mb-3">Distribuição por Destino</h3>
          <?php if (empty($triagemStats['por_destino'])): ?>
            <p class="text-sm text-gray-500">Sem dados para exibir.</p>
          <?php else: ?>
            <div class="space-y-2">
              <?php foreach ($triagemStats['por_destino'] as $item): ?>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-700"><?= e($item['destino']) ?></span>
                  <span class="font-semibold text-gray-900"><?= (int)$item['total'] ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="text-sm font-semibold text-gray-800 mb-3">Últimas Triagens</h3>
          <?php if (empty($triagemStats['ultimos_registros'])): ?>
            <p class="text-sm text-gray-500">Sem registros recentes.</p>
          <?php else: ?>
            <div class="max-h-72 overflow-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="text-left text-gray-500 border-b border-gray-100">
                    <th class="py-2 pr-2">Cliente</th>
                    <th class="py-2 pr-2">Modelo</th>
                    <th class="py-2 pr-2">%</th>
                    <th class="py-2 pr-2">Destino</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($triagemStats['ultimos_registros'] as $row): ?>
                    <tr class="border-b border-gray-50">
                      <td class="py-2 pr-2 text-gray-700"><?= e($row['cliente_nome'] ?? '-') ?></td>
                      <td class="py-2 pr-2 text-gray-700"><?= e($row['toner_modelo'] ?? '-') ?></td>
                      <td class="py-2 pr-2 font-medium text-gray-900"><?= number_format((float)($row['percentual_calculado'] ?? 0), 2, ',', '.') ?>%</td>
                      <td class="py-2 pr-2 text-gray-700"><?= e($row['destino'] ?? '-') ?></td>
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
</section>
