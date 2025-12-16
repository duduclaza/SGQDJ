<?php

namespace App\Controllers;

use App\Services\ResendService;

class TesteResendController
{
    public function index()
    {
        echo '<!DOCTYPE html><html lang="pt-BR"><head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Teste Resend API</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f0f4f8; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                h1 { color: #1e40af; margin-top: 0; }
                .form-group { margin-bottom: 20px; }
                label { display: block; margin-bottom: 8px; font-weight: bold; color: #374151; }
                input[type="email"], input[type="text"] { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; }
                input:focus { outline: none; border-color: #3b82f6; }
                button { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 14px 28px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; width: 100%; }
                button:hover { opacity: 0.9; }
                .result { margin-top: 20px; padding: 20px; border-radius: 8px; }
                .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
                .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
                .info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; margin-bottom: 20px; }
                pre { background: #1e293b; color: #22c55e; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 12px; }
            </style>
        </head><body>
        <div class="container">';
        
        echo '<h1>🚀 Teste API Resend</h1>';
        echo '<div class="info">Usando a API Resend para envio de emails. Esta é uma alternativa mais confiável ao SMTP.</div>';
        
        // Processar envio
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $resend = new ResendService();
            
            // Testar conexão primeiro
            echo '<h3>📡 Testando conexão com API Resend...</h3>';
            $testResult = $resend->testConnection();
            
            if ($testResult['success']) {
                echo '<div class="result success">✅ ' . htmlspecialchars($testResult['message']) . '</div>';
            } else {
                echo '<div class="result error">❌ ' . htmlspecialchars($testResult['message']) . '</div>';
            }
            
            // Enviar email de teste
            echo '<h3>📧 Enviando email de teste...</h3>';
            
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                echo '<div class="result error">❌ Email inválido!</div>';
            } else {
                $subject = 'Teste Resend API - ' . date('d/m/Y H:i:s');
                $html = '
                    <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;">
                        <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <h1 style="color: white; margin: 0;">✅ Teste Bem Sucedido!</h1>
                            <p style="color: #bfdbfe; margin: 10px 0 0 0;">API Resend funcionando perfeitamente</p>
                        </div>
                        <div style="background: white; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 12px 12px;">
                            <p>Se você está lendo este email, a integração com a <strong>API Resend</strong> está funcionando corretamente!</p>
                            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 10px; background: #f8fafc; border: 1px solid #e5e7eb; font-weight: bold;">Data/Hora:</td>
                                    <td style="padding: 10px; border: 1px solid #e5e7eb;">' . date('d/m/Y H:i:s') . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; background: #f8fafc; border: 1px solid #e5e7eb; font-weight: bold;">Método:</td>
                                    <td style="padding: 10px; border: 1px solid #e5e7eb;">API Resend (REST)</td>
                                </tr>
                            </table>
                            <div style="background: #d1fae5; padding: 15px; border-radius: 8px; border: 1px solid #a7f3d0;">
                                <p style="margin: 0; color: #065f46;">🎉 O sistema de notificações está pronto para uso!</p>
                            </div>
                        </div>
                        <p style="text-align: center; color: #6b7280; font-size: 12px; margin-top: 20px;">
                            SGQ OTI DJ - Sistema de Gestão da Qualidade
                        </p>
                    </div>
                ';
                
                $result = $resend->send($email, $subject, $html, 'Teste Resend API bem sucedido!');
                
                if ($result) {
                    echo '<div class="result success">✅ Email enviado com sucesso para <strong>' . htmlspecialchars($email) . '</strong>!</div>';
                } else {
                    echo '<div class="result error">❌ Falha ao enviar: ' . htmlspecialchars($resend->getLastError()) . '</div>';
                }
            }
        }
        
        // Formulário
        echo '
        <form method="POST">
            <div class="form-group">
                <label for="email">Email para teste:</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com" value="' . ($_POST['email'] ?? '') . '">
            </div>
            <button type="submit">📧 Enviar Email de Teste</button>
        </form>
        ';
        
        echo '</div></body></html>';
    }
}
