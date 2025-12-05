<?php

namespace App\Services\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Base Mailer Class
 * 
 * Classe base para todos os mailers
 * Contém configuração compartilhada do PHPMailer
 */
abstract class BaseMailer
{
    protected PHPMailer $mailer;
    protected ?string $lastError = null;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    /**
     * Configurar PHPMailer com credenciais SMTP
     */
    protected function configureMailer(): void
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->env('MAIL_HOST', 'smtp.hostinger.com');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->env('MAIL_USERNAME');
            $this->mailer->Password = $this->env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = (int) $this->env('MAIL_PORT', 465);
            $this->mailer->CharSet = 'UTF-8';

            // Sender
            $this->mailer->setFrom(
                $this->env('MAIL_FROM_ADDRESS', 'noreply@sgqoti.com.br'),
                $this->env('MAIL_FROM_NAME', 'SGQ OTI DJ')
            );

            // Disable debug output by default
            $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;

        } catch (Exception $e) {
            $this->lastError = "Erro ao configurar mailer: {$e->getMessage()}";
            error_log($this->lastError);
        }
    }

    /**
     * Obter variável de ambiente
     */
    protected function env(string $key, $default = null)
    {
        // Priorizar $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Fallback para getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Enviar email
     */
    protected function send(
        string|array $to,
        string $subject,
        string $body,
        ?string $altBody = null,
        array $attachments = []
    ): bool {
        try {
            // Clear previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Add recipients
            if (is_array($to)) {
                foreach ($to as $email) {
                    $this->mailer->addAddress($email);
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?? strip_tags($body);

            // Attachments
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $this->mailer->addAttachment($attachment);
                }
            }

            // Send
            $result = $this->mailer->send();

            if ($result) {
                $this->lastError = null;
                return true;
            }

            return false;

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Email sending failed: {$this->lastError}");
            return false;
        }
    }

    /**
     * Obter último erro
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Escurecer cor hex (útil para botões)
     */
    protected function darkenColor(string $color): string
    {
        $color = ltrim($color, '#');
        $rgb = sscanf($color, "%02x%02x%02x");
        
        $r = max(0, $rgb[0] - 30);
        $g = max(0, $rgb[1] - 30);
        $b = max(0, $rgb[2] - 30);
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Template base HTML para emails
     */
    protected function baseTemplate(string $title, string $content, string $primaryColor = '#4F46E5'): string
    {
        $darkerColor = $this->darkenColor($primaryColor);
        $appUrl = $this->env('APP_URL', 'https://djbr.sgqoti.com.br');

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {$primaryColor} 0%, {$darkerColor} 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">SGQ OTI DJ</h1>
                            <p style="margin: 10px 0 0 0; color: #e5e7eb; font-size: 14px;">Sistema de Gestão da Qualidade</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            {$content}
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                Este é um email automático, por favor não responda.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © 2025 SGQ OTI DJ. Todos os direitos reservados.
                            </p>
                            <p style="margin: 10px 0 0 0;">
                                <a href="{$appUrl}" style="color: {$primaryColor}; text-decoration: none; font-size: 12px;">Acessar Sistema</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Botão de ação para templates
     */
    protected function actionButton(string $text, string $url, string $color = '#4F46E5'): string
    {
        $darkerColor = $this->darkenColor($color);
        
        return <<<HTML
<table role="presentation" style="margin: 20px 0;">
    <tr>
        <td align="center">
            <a href="{$url}" style="display: inline-block; padding: 12px 24px; background-color: {$color}; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 500; transition: background-color 0.3s;">
                {$text}
            </a>
        </td>
    </tr>
</table>
HTML;
    }
}
