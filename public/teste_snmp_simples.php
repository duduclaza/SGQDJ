<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste SNMP Simples</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p { color: #666; margin-bottom: 30px; }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover { transform: translateY(-2px); }
        .result {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }
        .result.success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            display: block;
        }
        .result.error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            display: block;
        }
        .result h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .result.success h3 { color: #2e7d32; }
        .result.error h3 { color: #c62828; }
        .result p { color: #555; margin-bottom: 10px; }
        .code {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            margin-top: 10px;
            word-break: break-all;
        }
        .tips {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .tips h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        .tips ul {
            margin-left: 20px;
            color: #856404;
        }
        .tips li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Teste SNMP Simples</h1>
        <p>Valide se a conexão SNMP está funcionando</p>
        
        <form method="POST">
            <div class="form-group">
                <label>IP da Impressora</label>
                <input type="text" name="ip" value="<?= htmlspecialchars($_POST['ip'] ?? '127.0.0.1') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Porta SNMP</label>
                <input type="number" name="porta" value="<?= htmlspecialchars($_POST['porta'] ?? '1161') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Community String</label>
                <input type="text" name="community" value="<?= htmlspecialchars($_POST['community'] ?? 'public') ?>" required>
            </div>
            
            <button type="submit" name="testar">🚀 Testar Conexão</button>
        </form>
        
        <?php
        if (isset($_POST['testar'])) {
            $ip = $_POST['ip'] . ':' . $_POST['porta'];
            $community = $_POST['community'];
            $oid = "1.3.6.1.2.1.1.1.0"; // sysDescr
            
            // Verificar extensão SNMP
            if (!extension_loaded('snmp')) {
                echo '<div class="result error">';
                echo '<h3>❌ Extensão SNMP não instalada</h3>';
                echo '<p>A extensão PHP SNMP não está disponível neste servidor.</p>';
                echo '<div class="tips">';
                echo '<h4>💡 Como instalar:</h4>';
                echo '<ul>';
                echo '<li>Ubuntu/Debian: <code>sudo apt-get install php-snmp</code></li>';
                echo '<li>Windows: Habilite <code>extension=snmp</code> no php.ini</li>';
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            } else {
                // Testar conexão
                $result = @snmpget($ip, $community, $oid);
                
                if ($result === false) {
                    echo '<div class="result error">';
                    echo '<h3>❌ Sem resposta SNMP</h3>';
                    echo '<p>Não foi possível conectar à impressora.</p>';
                    echo '<div class="tips">';
                    echo '<h4>💡 Possíveis causas:</h4>';
                    echo '<ul>';
                    echo '<li>Impressora não está ligada ou acessível</li>';
                    echo '<li>SNMP não está habilitado na impressora</li>';
                    echo '<li>Community string incorreta (padrão: public)</li>';
                    echo '<li>Firewall bloqueando a porta SNMP</li>';
                    echo '<li>IP ou porta incorretos</li>';
                    echo '</ul>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    $descricao = trim(str_replace('"', '', $result));
                    echo '<div class="result success">';
                    echo '<h3>✅ Conexão SNMP funcionando!</h3>';
                    echo '<p><strong>Descrição do Sistema:</strong></p>';
                    echo '<div class="code">' . htmlspecialchars($descricao) . '</div>';
                    echo '<p style="margin-top: 15px; color: #2e7d32; font-weight: 600;">🎉 Sucesso! Agora você pode testar os outros OIDs de contadores e suprimentos.</p>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
</body>
</html>
