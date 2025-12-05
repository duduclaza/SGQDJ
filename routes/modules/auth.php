<?php
/**
 * Rotas de Autenticação e Gestão de Acesso
 * 
 * Inclui:
 * - Login/Logout
 * - Registro
 * - Recuperação de senha
 * - Solicitação de acesso
 */

use App\Controllers\AuthController;
use App\Controllers\PasswordResetController;
use App\Controllers\AccessRequestController;

// ===== AUTENTICAÇÃO =====

// Login
$router->get('/login', [AuthController::class, 'login']);
$router->post('/auth/login', [AuthController::class, 'authenticate']);

// Logout
$router->get('/logout', [AuthController::class, 'logout']);

// Registro
$router->get('/register', [AuthController::class, 'register']);
$router->post('/auth/register', [AuthController::class, 'processRegister']);

// ===== RECUPERAÇÃO DE SENHA =====

$router->get('/password-reset/request', [PasswordResetController::class, 'requestResetPage']);
$router->post('/password-reset/request', [PasswordResetController::class, 'requestReset']);
$router->get('/password-reset/verify', [PasswordResetController::class, 'verifyCodePage']);
$router->post('/password-reset/verify-code', [PasswordResetController::class, 'verifyCode']);
$router->get('/password-reset/new', [PasswordResetController::class, 'resetPasswordPage']);
$router->post('/password-reset/reset', [PasswordResetController::class, 'resetPassword']);

// ===== SOLICITAÇÃO DE ACESSO =====

// Público
$router->get('/request-access', [AccessRequestController::class, 'requestAccess']);
$router->post('/access-request/process', [AccessRequestController::class, 'processRequest']);
$router->get('/access-request/filiais', [AccessRequestController::class, 'getFiliais']);
$router->get('/access-request/departamentos', [AccessRequestController::class, 'getDepartamentos']);

// Admin - Gestão de Solicitações
$router->get('/admin/access-requests', [AccessRequestController::class, 'index']);
$router->get('/admin/access-requests/list', [AccessRequestController::class, 'listPendingRequests']);
$router->get('/admin/access-requests/profiles', [AccessRequestController::class, 'listProfiles']);
$router->post('/admin/access-requests/approve', [AccessRequestController::class, 'approveRequest']);
$router->post('/admin/access-requests/reject', [AccessRequestController::class, 'rejectRequest']);
