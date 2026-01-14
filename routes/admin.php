<?php
/**
 * Rotas Administrativas
 * 
 * Dashboard, usuários, perfis, configurações
 */

use App\Controllers\AdminController;
use App\Controllers\ProfilesController;
use App\Controllers\ConfigController;
use App\Controllers\HomeController;

// ===== PÁGINA INICIAL AUTENTICADA =====

$router->get('/inicio', [HomeController::class, 'index']);

// ===== DASHBOARD =====

// Rota raiz - redireciona conforme permissão
$router->get('/', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if (\App\Services\PermissionService::hasPermission($_SESSION['user_id'], 'dashboard', 'view')) {
        (new App\Controllers\AdminController())->dashboard();
    } else {
        header('Location: /inicio');
        exit;
    }
});

// Dashboard completo
$router->get('/dashboard', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if (\App\Services\PermissionService::hasPermission($_SESSION['user_id'], 'dashboard', 'view')) {
        (new App\Controllers\AdminController())->dashboard();
    } else {
        header('Location: /inicio');
        exit;
    }
});

// Admin (alias para dashboard)
$router->get('/admin', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if (\App\Services\PermissionService::hasPermission($_SESSION['user_id'], 'dashboard', 'view')) {
        (new App\Controllers\AdminController())->dashboard();
    } else {
        header('Location: /inicio');
        exit;
    }
});

// Dashboard em manutenção
$router->get('/dashboard-manutencao', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $title = 'Dashboard em Manutenção - SGQ OTI DJ';
    $viewFile = __DIR__ . '/../../views/pages/dashboard-manutencao.php';
    include __DIR__ . '/../../views/layouts/main.php';
});

// ===== DADOS DO DASHBOARD =====

$router->get('/admin/dashboard/data', [AdminController::class, 'getDashboardData']);
$router->get('/admin/dashboard/ranking-clientes', [AdminController::class, 'getRankingClientes']);
$router->get('/admin/dashboard/toners-por-cliente', [AdminController::class, 'getTonersPorCliente']);
$router->get('/admin/dashboard/amostragens-data', [AdminController::class, 'getAmostragemsDashboardData']);
$router->get('/admin/dashboard/fornecedores-data', [AdminController::class, 'fornecedoresData']);
$router->get('/admin/fornecedor-itens', [AdminController::class, 'fornecedorItens']);
$router->get('/admin/amostragens-reprovadas-mes', [AdminController::class, 'amostragemReprovadasMes']);
$router->get('/admin/dashboard/melhorias-data', [AdminController::class, 'getMelhoriasData']);
$router->get('/admin/dashboard/nao-conformidades-data', [AdminController::class, 'getNaoConformidadesData']);
$router->get('/admin/dashboard/departamentos', [AdminController::class, 'getDepartamentos']);
$router->get('/admin/dashboard/diagnostico', [AdminController::class, 'diagnosticoDashboard']);

// ===== GESTÃO DE USUÁRIOS =====

$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/invitations', [AdminController::class, 'invitations']);
$router->post('/admin/users/create', [AdminController::class, 'createUser']);
$router->post('/admin/users/update', [AdminController::class, 'updateUser']);
$router->post('/admin/users/delete', [AdminController::class, 'deleteUser']);
$router->post('/admin/users/send-credentials', [AdminController::class, 'sendCredentials']);
$router->get('/admin/users/{id}/permissions', [AdminController::class, 'userPermissions']);
$router->post('/admin/users/{id}/permissions', [AdminController::class, 'updateUserPermissions']);

// ===== GESTÃO DE PERFIS =====

$router->get('/admin/profiles', [ProfilesController::class, 'index']);
$router->post('/admin/profiles/create', [ProfilesController::class, 'create']);
$router->post('/admin/profiles/update', [ProfilesController::class, 'update']);
$router->post('/admin/profiles/delete', [ProfilesController::class, 'delete']);
$router->get('/admin/profiles/{id}/permissions', [ProfilesController::class, 'getPermissions']);
$router->get('/admin/profiles/{id}/dashboard-tabs', [ProfilesController::class, 'getDashboardTabPermissions']);

// ===== DIAGNÓSTICOS =====

$router->get('/admin/diagnostico/pops-pendentes', [App\Controllers\PopItsController::class, 'diagnosticoPendentes']);
$router->get('/admin/diagnostico/permissoes-usuario', [AdminController::class, 'diagnosticoPermissoes']);

// ===== MANUTENÇÃO E CONFIGURAÇÃO =====

$router->post('/admin/db/patch-amostragens', [ConfigController::class, 'patchAmostragens']);
$router->post('/admin/db/run-migrations', [ConfigController::class, 'runMigrations']);
$router->get('/admin/db/run-migrations', [ConfigController::class, 'runMigrations']);
$router->post('/admin/sync-admin-permissions', [ConfigController::class, 'syncAdminPermissions']);
$router->get('/admin/sync-admin-permissions', [ConfigController::class, 'syncAdminPermissions']);
