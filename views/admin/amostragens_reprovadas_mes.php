<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amostragens Reprovadas - <?= htmlspecialchars($mesLabel) ?> | SGQ OTI DJ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <!-- Cabeçalho -->
    <header class="bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg no-print">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Amostragens Reprovadas</h1>
                        <p class="text-red-100"><?= htmlspecialchars($mesLabel) ?></p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimir
                    </button>
                    <button onclick="window.close()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Cabeçalho para impressão -->
    <div class="hidden print:block p-6">
        <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
            <h1 class="text-xl font-bold">SGQ OTI DJ - Sistema de Gestão da Qualidade</h1>
            <h2 class="text-lg font-semibold mt-2">Amostragens Reprovadas - <?= htmlspecialchars($mesLabel) ?></h2>
        </div>
    </div>
    
    <!-- Resumo -->
    <div class="max-w-7xl mx-auto px-6 py-4 no-print">
        <div class="bg-white rounded-lg shadow p-4 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 text-sm">📅 Mês:</span>
                <span class="font-medium text-gray-700"><?= htmlspecialchars($mesLabel) ?></span>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                    <?= count($itens) ?> <?= count($itens) === 1 ? 'registro' : 'registros' ?>
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-600 text-white">
                    <?= number_format($totalReprovado, 0, ',', '.') ?> itens reprovados
                </span>
            </div>
        </div>
    </div>
    
    <!-- Tabela de itens -->
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($itens)): ?>
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma amostragem encontrada</h3>
                <p class="text-gray-500">Não há amostragens reprovadas para este mês.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Nome do Produto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-red-700 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-red-700 uppercase tracking-wider">Qtd</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-red-700 uppercase tracking-wider">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Fornecedor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Reprovado Por</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($itens as $index => $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['codigo'] ?? '-') ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($item['nome_produto'] ?? '-') ?></span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                    <?= htmlspecialchars($item['tipo'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-red-600"><?= number_format($item['quantidade'] ?? 0, 0, ',', '.') ?></span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-gray-600">
                                    <?= !empty($item['data_registro']) ? date('d/m/Y', strtotime($item['data_registro'])) : '-' ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                    <?= htmlspecialchars($item['fornecedor'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-red-700 text-xs font-bold">
                                            <?= !empty($item['responsavel']) ? strtoupper(substr($item['responsavel'], 0, 1)) : '?' ?>
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($item['responsavel'] ?? 'Não informado') ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumo -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Total de registros: <span class="font-bold text-gray-900"><?= count($itens) ?></span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Total de itens reprovados: 
                        <span class="font-bold text-red-600">
                            <?= number_format($totalReprovado, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Rodapé para impressão -->
    <div class="hidden print:block p-6 mt-8">
        <div class="text-center text-sm text-gray-500 border-t border-gray-300 pt-4">
            <p>Documento gerado em <?= date('d/m/Y H:i:s') ?></p>
            <p>SGQ OTI DJ - Sistema de Gestão da Qualidade</p>
        </div>
    </div>
    
</body>
</html>
