<?php
/**
 * TESTE SNMP - VERSÃO WEB
 * Módulo: Monitoramento de Suprimentos e Contadores
 * 
 * Acesse: http://localhost/teste_snmp_web.php
 * ou: https://djbr.sgqoti.com.br/teste_snmp_web.php
 */

// Configurações
$impressora_ip = $_POST['ip'] ?? '127.0.0.1';
$impressora_porta = $_POST['porta'] ?? 1161;
$community = $_POST['community'] ?? 'public';
$timeout = 5000000;
$retries = 3;

$snmp_host = $impressora_ip . ':' . $impressora_porta;

// OIDs padrão
$oids = [
    'modelo' => '1.3.6.1.2.1.25.3.2.1.3.1',
    'numero_serie' => '1.3.6.1.2.1.43.5.1.1.17.1',
    'status' => '1.3.6.1.2.1.25.3.2.1.5.1',
    'contador_pb' => '1.3.6.1.2.1.43.10.2.1.4.1.1',
    'contador_color' => '1.3.6.1.2.1.43.10.2.1.4.1.2',
    'toner_preto' => '1.3.6.1.2.1.43.11.1.1.9.1.1',
    'toner_ciano' => '1.3.6.1.2.1.43.11.1.1.9.1.2',
    'toner_magenta' => '1.3.6.1.2.1.43.11.1.1.9.1.3',
    'toner_amarelo' => '1.3.6.1.2.1.43.11.1.1.9.1.4',
    'capacidade_preto' => '1.3.6.1.2.1.43.11.1.1.8.1.1',
    'capacidade_ciano' => '1.3.6.1.2.1.43.11.1.1.8.1.2',
    'capacidade_magenta' => '1.3.6.1.2.1.43.11.1.1.8.1.3',
    'capacidade_amarelo' => '1.3.6.1.2.1.43.11.1.1.8.1.4',
];

$testeRealizado = isset($_POST['testar']);
$dados = [];
$erros = [];
$snmpInstalado = extension_loaded('snmp');

if ($testeRealizado && $snmpInstalado) {
    foreach ($oids as $nome => $oid) {
        try {
            snmp_set_quick_print(true);
            snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
            
            $valor = @snmpget($snmp_host, $community, $oid, $timeout, $retries);
            
            if ($valor !== false) {
                $valor = trim(str_replace('"', '', $valor));
                $dados[$nome] = $valor;
            } else {
                $erros[$nome] = "Sem resposta";
            }
        } catch (Exception $e) {
            $erros[$nome] = $e->getMessage();
        }
    }
}

function calcularPercentual($atual, $maximo) {
    if ($maximo <= 0) return 0;
    return round(($atual / $maximo) * 100, 2);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste SNMP - Monitoramento de Impressoras</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .header h1 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee;
            border-left: 4px solid #f44;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            border-left: 4px solid #4c4;
            color: #3a3;
        }
        
        .alert-warning {
            background: #ffc;
            border-left: 4px solid #fc3;
            color: #963;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
        }
        
        .toner-bar {
            margin: 15px 0;
        }
        
        .toner-bar .label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }
        
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        .progress-fill.high { background: linear-gradient(90deg, #4caf50, #8bc34a); }
        .progress-fill.medium { background: linear-gradient(90deg, #ff9800, #ffc107); }
        .progress-fill.low { background: linear-gradient(90deg, #f44336, #e91e63); }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        table th {
            background: #f5f5f5;
            font-weight: 600;
            color: #555;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-error { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖨️ Teste SNMP - Monitoramento de Impressoras</h1>
            <p>Módulo: Monitoramento de Suprimentos e Contadores - SGQ OTI DJ</p>
        </div>
        
        <?php if (!$snmpInstalado): ?>
        <div class="alert alert-error">
            <strong>❌ ERRO:</strong> A extensão PHP SNMP não está instalada!<br>
            <small>Para instalar: Ubuntu/Debian: <code>sudo apt-get install php-snmp</code></small>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>⚙️ Configurações do Teste</h2>
            <form method="POST">
                <div class="grid">
                    <div class="form-group">
                        <label>IP da Impressora</label>
                        <input type="text" name="ip" value="<?= htmlspecialchars($impressora_ip) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Porta SNMP</label>
                        <input type="number" name="porta" value="<?= htmlspecialchars($impressora_porta) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Community String</label>
                        <input type="text" name="community" value="<?= htmlspecialchars($community) ?>">
                    </div>
                </div>
                <button type="submit" name="testar" class="btn" <?= !$snmpInstalado ? 'disabled' : '' ?>>
                    🔍 Testar Conexão SNMP
                </button>
            </form>
        </div>
        
        <?php if ($testeRealizado): ?>
            
            <?php if (count($dados) > 0): ?>
                <div class="alert alert-success">
                    <strong>✅ Sucesso!</strong> Dados coletados: <?= count($dados) ?>/<?= count($oids) ?> 
                    (<?= round((count($dados) / count($oids)) * 100, 2) ?>%)
                </div>
                
                <?php if (isset($dados['modelo']) || isset($dados['numero_serie'])): ?>
                <div class="card">
                    <h2>🖨️ Informações da Impressora</h2>
                    <div class="grid">
                        <?php if (isset($dados['modelo'])): ?>
                        <div class="stat-card">
                            <h3>Modelo</h3>
                            <div class="value"><?= htmlspecialchars($dados['modelo']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($dados['numero_serie'])): ?>
                        <div class="stat-card">
                            <h3>Número de Série</h3>
                            <div class="value"><?= htmlspecialchars($dados['numero_serie']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($dados['status'])): ?>
                        <div class="stat-card">
                            <h3>Status</h3>
                            <div class="value"><?= htmlspecialchars($dados['status']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($dados['contador_pb']) || isset($dados['contador_color'])): ?>
                <div class="card">
                    <h2>📊 Contadores</h2>
                    <div class="grid">
                        <?php if (isset($dados['contador_pb'])): ?>
                        <div class="stat-card">
                            <h3>Contador P&B</h3>
                            <div class="value"><?= number_format($dados['contador_pb']) ?></div>
                            <small>páginas</small>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($dados['contador_color'])): ?>
                        <div class="stat-card">
                            <h3>Contador Color</h3>
                            <div class="value"><?= number_format($dados['contador_color']) ?></div>
                            <small>páginas</small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php 
                $temToner = isset($dados['toner_preto']) || isset($dados['toner_ciano']) || 
                           isset($dados['toner_magenta']) || isset($dados['toner_amarelo']);
                if ($temToner): 
                ?>
                <div class="card">
                    <h2>🎨 Níveis de Toner</h2>
                    
                    <?php
                    $cores = [
                        'preto' => ['emoji' => '⬛', 'nome' => 'Preto'],
                        'ciano' => ['emoji' => '🔵', 'nome' => 'Ciano'],
                        'magenta' => ['emoji' => '🔴', 'nome' => 'Magenta'],
                        'amarelo' => ['emoji' => '🟡', 'nome' => 'Amarelo']
                    ];
                    
                    foreach ($cores as $cor => $info):
                        if (isset($dados["toner_$cor"])):
                            $nivel = $dados["toner_$cor"];
                            $capacidade = $dados["capacidade_$cor"] ?? 100;
                            $percentual = calcularPercentual($nivel, $capacidade);
                            $classe = $percentual > 50 ? 'high' : ($percentual > 20 ? 'medium' : 'low');
                    ?>
                    <div class="toner-bar">
                        <div class="label">
                            <span><?= $info['emoji'] ?> <?= $info['nome'] ?></span>
                            <span><strong><?= $percentual ?>%</strong></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill <?= $classe ?>" style="width: <?= $percentual ?>%">
                                <?= $percentual ?>%
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>❌ Falha!</strong> Nenhum dado foi coletado da impressora.
                </div>
            <?php endif; ?>
            
            <?php if (count($erros) > 0): ?>
            <div class="card">
                <h2>⚠️ Erros Encontrados</h2>
                <table>
                    <thead>
                        <tr>
                            <th>OID</th>
                            <th>Erro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($erros as $nome => $erro): ?>
                        <tr>
                            <td><?= htmlspecialchars($nome) ?></td>
                            <td><span class="badge badge-error"><?= htmlspecialchars($erro) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="alert alert-warning" style="margin-top: 20px;">
                    <strong>💡 Dicas:</strong><br>
                    • Verifique se SNMP está habilitado na impressora<br>
                    • Confirme se a community string está correta (padrão: public)<br>
                    • Verifique se o firewall permite a porta SNMP<br>
                    • Alguns OIDs podem variar por fabricante/modelo
                </div>
            </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</body>
</html>
