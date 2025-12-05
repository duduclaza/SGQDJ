<?php
/**
 * SCRIPT DE TESTE SNMP
 * Módulo: Monitoramento de Suprimentos e Contadores
 * 
 * INSTRUÇÕES:
 * 1. Certifique-se que a extensão PHP SNMP está instalada
 * 2. Configure o IP da impressora abaixo
 * 3. Execute: php teste_snmp.php
 */

// =====================================================
// CONFIGURAÇÕES
// =====================================================

$impressora_ip = '192.168.1.100'; // ALTERE PARA O IP DA SUA IMPRESSORA
$community = 'public'; // Community string SNMP (padrão: public)
$timeout = 5000000; // Timeout em microsegundos (5 segundos)
$retries = 3; // Número de tentativas

// =====================================================
// OIDs PADRÃO (RFC 3805)
// =====================================================

$oids = [
    // Informações da Impressora
    'modelo' => '1.3.6.1.2.1.25.3.2.1.3.1',
    'numero_serie' => '1.3.6.1.2.1.43.5.1.1.17.1',
    'status' => '1.3.6.1.2.1.25.3.2.1.5.1',
    
    // Contadores
    'contador_pb' => '1.3.6.1.2.1.43.10.2.1.4.1.1',
    'contador_color' => '1.3.6.1.2.1.43.10.2.1.4.1.2',
    
    // Níveis de Toner
    'toner_preto' => '1.3.6.1.2.1.43.11.1.1.9.1.1',
    'toner_ciano' => '1.3.6.1.2.1.43.11.1.1.9.1.2',
    'toner_magenta' => '1.3.6.1.2.1.43.11.1.1.9.1.3',
    'toner_amarelo' => '1.3.6.1.2.1.43.11.1.1.9.1.4',
    
    // Capacidade Máxima
    'capacidade_preto' => '1.3.6.1.2.1.43.11.1.1.8.1.1',
    'capacidade_ciano' => '1.3.6.1.2.1.43.11.1.1.8.1.2',
    'capacidade_magenta' => '1.3.6.1.2.1.43.11.1.1.8.1.3',
    'capacidade_amarelo' => '1.3.6.1.2.1.43.11.1.1.8.1.4',
];

// =====================================================
// FUNÇÕES AUXILIARES
// =====================================================

function exibirCabecalho() {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║          TESTE DE CONEXÃO SNMP - SGQ OTI DJ               ║\n";
    echo "║     Módulo: Monitoramento de Suprimentos e Contadores     ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
}

function exibirResultado($titulo, $valor, $sucesso = true) {
    $icone = $sucesso ? "✅" : "❌";
    $cor = $sucesso ? "\033[32m" : "\033[31m";
    $reset = "\033[0m";
    
    printf("%-30s %s %s%s%s\n", $titulo . ":", $icone, $cor, $valor, $reset);
}

function calcularPercentual($atual, $maximo) {
    if ($maximo <= 0) return 0;
    return round(($atual / $maximo) * 100, 2);
}

// =====================================================
// VERIFICAÇÕES INICIAIS
// =====================================================

exibirCabecalho();

echo "🔍 VERIFICAÇÕES INICIAIS\n";
echo str_repeat("─", 60) . "\n";

// Verificar se extensão SNMP está carregada
if (!extension_loaded('snmp')) {
    exibirResultado("Extensão SNMP", "NÃO INSTALADA", false);
    echo "\n❌ ERRO CRÍTICO: A extensão PHP SNMP não está instalada!\n";
    echo "\n📝 Para instalar:\n";
    echo "   - Ubuntu/Debian: sudo apt-get install php-snmp\n";
    echo "   - CentOS/RHEL: sudo yum install php-snmp\n";
    echo "   - Windows: Habilite extension=snmp no php.ini\n";
    echo "\n";
    exit(1);
}

exibirResultado("Extensão SNMP", "INSTALADA", true);
exibirResultado("IP da Impressora", $impressora_ip, true);
exibirResultado("Community String", $community, true);

echo "\n";

// =====================================================
// TESTE DE CONECTIVIDADE
// =====================================================

echo "🌐 TESTE DE CONECTIVIDADE\n";
echo str_repeat("─", 60) . "\n";

// Ping na impressora
$ping = @exec("ping -n 1 -w 1000 $impressora_ip", $output, $return);
$pingOk = ($return === 0);

exibirResultado("Ping", $pingOk ? "SUCESSO" : "FALHOU", $pingOk);

if (!$pingOk) {
    echo "\n⚠️  AVISO: Não foi possível fazer ping na impressora.\n";
    echo "   Verifique se o IP está correto e se a impressora está ligada.\n";
    echo "\n";
}

echo "\n";

// =====================================================
// LEITURA DE DADOS SNMP
// =====================================================

echo "📊 LEITURA DE DADOS SNMP\n";
echo str_repeat("─", 60) . "\n";

$dados = [];
$erros = [];

foreach ($oids as $nome => $oid) {
    try {
        // Configurar timeout e retries
        snmp_set_quick_print(true);
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
        
        // Tentar ler o OID
        $valor = @snmpget($impressora_ip, $community, $oid, $timeout, $retries);
        
        if ($valor !== false) {
            // Limpar o valor (remover aspas, espaços, etc)
            $valor = trim(str_replace('"', '', $valor));
            $dados[$nome] = $valor;
            exibirResultado($nome, $valor, true);
        } else {
            $erros[$nome] = "Sem resposta";
            exibirResultado($nome, "SEM RESPOSTA", false);
        }
    } catch (Exception $e) {
        $erros[$nome] = $e->getMessage();
        exibirResultado($nome, "ERRO: " . $e->getMessage(), false);
    }
}

echo "\n";

// =====================================================
// ANÁLISE DOS RESULTADOS
// =====================================================

echo "📈 ANÁLISE DOS RESULTADOS\n";
echo str_repeat("─", 60) . "\n";

if (count($dados) > 0) {
    echo "✅ Dados coletados com sucesso: " . count($dados) . "/" . count($oids) . "\n\n";
    
    // Informações da Impressora
    if (isset($dados['modelo']) || isset($dados['numero_serie'])) {
        echo "🖨️  INFORMAÇÕES DA IMPRESSORA:\n";
        if (isset($dados['modelo'])) {
            echo "   Modelo: " . $dados['modelo'] . "\n";
        }
        if (isset($dados['numero_serie'])) {
            echo "   Número de Série: " . $dados['numero_serie'] . "\n";
        }
        if (isset($dados['status'])) {
            echo "   Status: " . $dados['status'] . "\n";
        }
        echo "\n";
    }
    
    // Contadores
    if (isset($dados['contador_pb']) || isset($dados['contador_color'])) {
        echo "📊 CONTADORES:\n";
        if (isset($dados['contador_pb'])) {
            echo "   P&B: " . number_format($dados['contador_pb']) . " páginas\n";
        }
        if (isset($dados['contador_color'])) {
            echo "   Color: " . number_format($dados['contador_color']) . " páginas\n";
        }
        echo "\n";
    }
    
    // Níveis de Toner
    $temToner = isset($dados['toner_preto']) || isset($dados['toner_ciano']) || 
                isset($dados['toner_magenta']) || isset($dados['toner_amarelo']);
    
    if ($temToner) {
        echo "🎨 NÍVEIS DE TONER:\n";
        
        $cores = [
            'preto' => '⬛',
            'ciano' => '🔵',
            'magenta' => '🔴',
            'amarelo' => '🟡'
        ];
        
        foreach ($cores as $cor => $emoji) {
            if (isset($dados["toner_$cor"])) {
                $nivel = $dados["toner_$cor"];
                $capacidade = $dados["capacidade_$cor"] ?? 100;
                $percentual = calcularPercentual($nivel, $capacidade);
                
                // Barra de progresso
                $barraCheia = str_repeat("█", (int)($percentual / 5));
                $barraVazia = str_repeat("░", 20 - (int)($percentual / 5));
                
                // Cor do status
                $statusCor = $percentual > 50 ? "\033[32m" : ($percentual > 20 ? "\033[33m" : "\033[31m");
                $reset = "\033[0m";
                
                printf("   %s %-8s [%s%s%s%s] %s%.2f%%%s\n", 
                    $emoji, 
                    ucfirst($cor), 
                    $statusCor,
                    $barraCheia, 
                    $barraVazia,
                    $reset,
                    $statusCor,
                    $percentual,
                    $reset
                );
            }
        }
        echo "\n";
    }
    
} else {
    echo "❌ Nenhum dado foi coletado!\n\n";
}

// =====================================================
// ERROS E RECOMENDAÇÕES
// =====================================================

if (count($erros) > 0) {
    echo "⚠️  ERROS ENCONTRADOS:\n";
    echo str_repeat("─", 60) . "\n";
    
    foreach ($erros as $nome => $erro) {
        echo "   • $nome: $erro\n";
    }
    
    echo "\n📝 POSSÍVEIS SOLUÇÕES:\n";
    echo "   1. Verifique se SNMP está habilitado na impressora\n";
    echo "   2. Confirme se a community string está correta (padrão: public)\n";
    echo "   3. Verifique se o firewall permite porta 161 (SNMP)\n";
    echo "   4. Alguns OIDs podem variar por fabricante/modelo\n";
    echo "   5. Consulte o manual da impressora para OIDs específicos\n";
    echo "\n";
}

// =====================================================
// CONCLUSÃO
// =====================================================

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      CONCLUSÃO DO TESTE                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$percentualSucesso = (count($dados) / count($oids)) * 100;

if ($percentualSucesso >= 80) {
    echo "✅ EXCELENTE! A impressora está respondendo bem ao SNMP.\n";
    echo "   Taxa de sucesso: " . round($percentualSucesso, 2) . "%\n";
    echo "   O módulo de monitoramento funcionará perfeitamente!\n";
} elseif ($percentualSucesso >= 50) {
    echo "⚠️  PARCIAL: Alguns dados foram coletados, mas não todos.\n";
    echo "   Taxa de sucesso: " . round($percentualSucesso, 2) . "%\n";
    echo "   Pode ser necessário ajustar os OIDs para este modelo.\n";
} else {
    echo "❌ FALHA: Poucos ou nenhum dado foi coletado.\n";
    echo "   Taxa de sucesso: " . round($percentualSucesso, 2) . "%\n";
    echo "   Verifique as configurações SNMP da impressora.\n";
}

echo "\n";
echo "📄 Dados salvos em: teste_snmp_resultado.json\n";

// Salvar resultado em JSON
$resultado = [
    'data_teste' => date('Y-m-d H:i:s'),
    'impressora_ip' => $impressora_ip,
    'community' => $community,
    'dados_coletados' => $dados,
    'erros' => $erros,
    'taxa_sucesso' => round($percentualSucesso, 2)
];

file_put_contents('teste_snmp_resultado.json', json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n";
