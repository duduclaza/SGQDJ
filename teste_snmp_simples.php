<?php
/**
 * TESTE SNMP SIMPLES
 * Valida se a conexão SNMP está funcionando
 */

echo "🔍 TESTE SNMP SIMPLES\n";
echo str_repeat("=", 60) . "\n\n";

// Configurações
$ip = "127.0.0.1:1161";
$community = "public";
$oid = "1.3.6.1.2.1.1.1.0"; // sysDescr - Descrição do sistema

echo "📋 Configurações:\n";
echo "   IP: $ip\n";
echo "   Community: $community\n";
echo "   OID: $oid (sysDescr)\n\n";

// Verificar extensão SNMP
echo "🔧 Verificando extensão SNMP... ";
if (!extension_loaded('snmp')) {
    echo "❌ NÃO INSTALADA\n";
    echo "\n💡 Para instalar:\n";
    echo "   - Ubuntu/Debian: sudo apt-get install php-snmp\n";
    echo "   - Windows: Habilite extension=snmp no php.ini\n";
    exit(1);
}
echo "✅ INSTALADA\n\n";

// Testar conexão SNMP
echo "🌐 Testando conexão SNMP...\n";
$result = @snmpget($ip, $community, $oid);

if ($result === false) {
    echo "❌ SEM RESPOSTA SNMP\n\n";
    echo "💡 Possíveis causas:\n";
    echo "   1. Impressora não está ligada ou acessível\n";
    echo "   2. SNMP não está habilitado na impressora\n";
    echo "   3. Community string incorreta\n";
    echo "   4. Firewall bloqueando porta 1161\n";
    echo "   5. IP ou porta incorretos\n";
} else {
    echo "✅ RESPOSTA SNMP RECEBIDA!\n\n";
    echo "📄 Descrição do Sistema:\n";
    echo "   " . trim(str_replace('"', '', $result)) . "\n\n";
    echo "🎉 SUCESSO! A conexão SNMP está funcionando!\n";
    echo "   Agora você pode testar os outros OIDs.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
