<?php

namespace App\Services\Email;

/**
 * Auth Mailer
 * 
 * Responsável por emails de autenticação:
 * - Boas-vindas com senha temporária
 * - Reset de senha
 * - Confirmação de conta
 */
class AuthMailer extends BaseMailer
{
    /**
     * Enviar email de boas-vindas com credenciais
     */
    public function sendWelcomeEmail(array $user, string $tempPassword): bool
    {
        $subject = "Bem-vindo ao SGQ OTI DJ - Suas Credenciais de Acesso";
        $body = $this->buildWelcomeEmailTemplate($user, $tempPassword);
        $altBody = $this->buildWelcomeEmailAltText($user, $tempPassword);

        return $this->send($user['email'], $subject, $body, $altBody);
    }

    /**
     * Template HTML de boas-vindas
     */
    private function buildWelcomeEmailTemplate(array $user, string $tempPassword): string
    {
        $appUrl = $this->env('APP_URL', 'https://djbr.sgqoti.com.br');
        
        $content = <<<HTML
<h2 style="color: #111827; margin: 0 0 20px 0; font-size: 20px;">
    Olá, {$user['name']}!
</h2>

<p style="color: #374151; line-height: 1.6; margin: 0 0 20px 0;">
    Sua conta no <strong>SGQ OTI DJ</strong> foi criada com sucesso!
</p>

<div style="background-color: #f3f4f6; padding: 20px; border-radius: 6px; border-left: 4px solid #4F46E5; margin: 20px 0;">
    <p style="margin: 0 0 10px 0; color: #374151; font-weight: 600;">
        📧 Email: <span style="color: #4F46E5;">{$user['email']}</span>
    </p>
    <p style="margin: 0; color: #374151; font-weight: 600;">
        🔑 Senha Temporária: <span style="color: #4F46E5; font-family: monospace;">{$tempPassword}</span>
    </p>
</div>

<div style="background-color: #FEF3C7; padding: 15px; border-radius: 6px; border-left: 4px solid #F59E0B; margin: 20px 0;">
    <p style="margin: 0; color: #92400E; font-size: 14px;">
        ⚠️ <strong>Importante:</strong> Por segurança, altere sua senha no primeiro acesso!
    </p>
</div>

<p style="color: #374151; line-height: 1.6; margin: 20px 0;">
    Clique no botão abaixo para fazer login e começar a usar o sistema:
</p>

{$this->actionButton('Acessar Sistema', "{$appUrl}/login")}

<p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 20px 0 0 0;">
    Se você tiver alguma dúvida, entre em contato com o administradordo sistema.
</p>
HTML;

        return $this->baseTemplate('Bem-vindo ao SGQ OTI DJ', $content);
    }

    /**
     * Texto alternativo (plain text)
     */
    private function buildWelcomeEmailAltText(array $user, string $tempPassword): string
    {
        $appUrl = $this->env('APP_URL', 'https://djbr.sgqoti.com.br');
        
        return <<<TEXT
Olá, {$user['name']}!

Sua conta no SGQ OTI DJ foi criada com sucesso!

CREDENCIAIS DE ACESSO:
Email: {$user['email']}
Senha Temporária: {$tempPassword}

IMPORTANTE: Por segurança, altere sua senha no primeiro acesso!

Acesse o sistema em: {$appUrl}/login

Se você tiver alguma dúvida, entre em contato com o administrador do sistema.

---
Este é um email automático, por favor não responda.
© 2025 SGQ OTI DJ. Todos os direitos reservados.
TEXT;
    }
}
