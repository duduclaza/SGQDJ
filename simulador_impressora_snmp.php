<?php
/**
 * SIMULADOR DE IMPRESSORA SNMP
 * Para testes de desenvolvimento
 * 
 * Este script simula respostas SNMP de uma impressora
 * Útil para testar o módulo sem ter uma impressora física
 */

// Dados simulados da impressora
$impressora_simulada = [
    // Informações básicas
    '1.3.6.1.2.1.1.1.0' => 'HP LaserJet Pro M404dn (Simulado)',
    '1.3.6.1.2.1.25.3.2.1.3.1' => 'HP LaserJet Pro M404dn',
    '1.3.6.1.2.1.43.5.1.1.17.1' => 'BRXYZ123456',
    '1.3.6.1.2.1.25.3.2.1.5.1' => '3', // Running
    
    // Contadores
    '1.3.6.1.2.1.43.10.2.1.4.1.1' => '15234', // Contador P&B
    '1.3.6.1.2.1.43.10.2.1.4.1.2' => '0', // Contador Color
    
    // Níveis de Toner (atual)
    '1.3.6.1.2.1.43.11.1.1.9.1.1' => '75', // Toner Preto
    '1.3.6.1.2.1.43.11.1.1.9.1.2' => '0', // Toner Ciano
    '1.3.6.1.2.1.43.11.1.1.9.1.3' => '0', // Toner Magenta
    '1.3.6.1.2.1.43.11.1.1.9.1.4' => '0', // Toner Amarelo
    
    // Capacidade Máxima de Toner
    '1.3.6.1.2.1.43.11.1.1.8.1.1' => '100', // Capacidade Preto
    '1.3.6.1.2.1.43.11.1.1.8.1.2' => '100', // Capacidade Ciano
    '1.3.6.1.2.1.43.11.1.1.8.1.3' => '100', // Capacidade Magenta
    '1.3.6.1.2.1.43.11.1.1.8.1.4' => '100', // Capacidade Amarelo
];

// Função para simular snmpget
function snmpget_simulado($oid) {
    global $impressora_simulada;
    
    if (isset($impressora_simulada[$oid])) {
        return $impressora_simulada[$oid];
    }
    
    return false;
}

// Testar todos os OIDs
echo "🖨️  SIMULADOR DE IMPRESSORA SNMP\n";
echo str_repeat("=", 60) . "\n\n";

echo "📋 Impressora Simulada: HP LaserJet Pro M404dn\n";
echo "📍 Número de Série: BRXYZ123456\n\n";

echo "🔍 Testando OIDs...\n";
echo str_repeat("-", 60) . "\n\n";

$oids_teste = [
    'Descrição do Sistema' => '1.3.6.1.2.1.1.1.0',
    'Modelo' => '1.3.6.1.2.1.25.3.2.1.3.1',
    'Número de Série' => '1.3.6.1.2.1.43.5.1.1.17.1',
    'Status' => '1.3.6.1.2.1.25.3.2.1.5.1',
    'Contador P&B' => '1.3.6.1.2.1.43.10.2.1.4.1.1',
    'Contador Color' => '1.3.6.1.2.1.43.10.2.1.4.1.2',
    'Toner Preto' => '1.3.6.1.2.1.43.11.1.1.9.1.1',
    'Capacidade Preto' => '1.3.6.1.2.1.43.11.1.1.8.1.1',
];

foreach ($oids_teste as $nome => $oid) {
    $valor = snmpget_simulado($oid);
    
    if ($valor !== false) {
        printf("✅ %-25s %s\n", $nome . ":", $valor);
    } else {
        printf("❌ %-25s SEM RESPOSTA\n", $nome . ":");
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "💡 Este é um simulador para testes de desenvolvimento.\n";
echo "   Para usar com impressora real, configure o IP correto.\n";
echo str_repeat("=", 60) . "\n";
