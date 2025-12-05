<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class TesteEmailController
{
    public function index()
    {
        echo '<!DOCTYPE html><html><head><title>Teste SMTP</title><style>body{font-family:sans-serif;padding:20px;background:#f0f0f0}.container{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);max-width:800px;margin:0 auto}h1{margin-top:0}pre{background:#1e1e1e;color:#0f0;padding:15px;overflow:auto;border-radius:5px;max-height:500px;font-size:12px}.input-group{margin-bottom:15px}input{padding:8px;border:1px solid #ddd;border-radius:4px;width:300px}button{padding:10px 20px;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer}button:hover{background:#0056b3}</style></head><body>';
        
        echo '<div class="container">';
        echo '<h1>🕵️ Teste de Diagnóstico SMTP</h1>';
        echo '<p>Configuração usada: <strong>smtp.hostinger.com:465 (SSL)</strong></p>';
        echo '<p>Usuário: <strong>suporte@sgqoti.com.br</strong></p>';

        $destinatario = $_POST['email'] ?? '';

        echo '<form method="POST">';
        echo '<div class="input-group">';
        echo '<label>Enviar para: </label>';
        echo '<input type="email" name="email" value="' . htmlspecialchars($destinatario) . '" placeholder="seu-email@exemplo.com" required>';
        echo ' <button type="submit">🚀 Testar Envio</button>';
        echo '</div>';
        echo '</form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($destinatario)) {
            echo '<hr><h3>📜 Log da Transação SMTP:</h3>';
            echo '<pre>';

            $mail = new PHPMailer(true);

            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'suporte@sgqoti.com.br';
                $mail->Password   = 'Pandora@1989';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
                $mail->Port       = 465;

                // Debug
                $mail->SMTPDebug = 2; // Debug detalhado (Cliente e Servidor)
                $mail->Debugoutput = 'html'; // Formato HTML para o log

                // Remetente e destinatário
                $mail->setFrom('suporte@sgqoti.com.br', 'SGQ OTI DJ - Diagnóstico');
                $mail->addAddress($destinatario);

                // Conteúdo do email
                $mail->isHTML(true);
                $mail->Subject = 'Teste de Diagnóstico SMTP - ' . date('H:i:s');
                $mail->Body    = '<h1>Teste Bem Sucedido! ✅</h1><p>Se você está lendo isso, o envio via SMTP da Hostinger funcionou perfeitamente.</p><p>Horário: ' . date('d/m/Y H:i:s') . '</p>';
                $mail->AltBody = 'Teste de Diagnóstico SMTP bem sucedido.';

                $mail->send();
                echo '</pre>';
                echo '<div style="margin-top:15px;padding:15px;background:#d4edda;color:#155724;border-radius:4px;border:1px solid #c3e6cb"><strong>✅ SUCESSO!</strong> O e-mail foi aceito pelo servidor SMTP. Verifique sua caixa de entrada (e SPAM).</div>';

            } catch (Exception $e) {
                echo '</pre>';
                echo '<div style="margin-top:15px;padding:15px;background:#f8d7da;color:#721c24;border-radius:4px;border:1px solid #f5c6cb"><strong>❌ ERRO:</strong> ' . $mail->ErrorInfo . '</div>';
            }
        } else {
            echo '<div style="color:#666;margin-top:20px">Digite um e-mail acima e clique em testar para ver o log de conexão.</div>';
        }
        
        echo '</div></body></html>';
    }
}
