<?php
/**
 * Rotas do módulo Usabilidade do SGQ
 * 
 * ACESSO: Exclusivo para super_admin
 */

use App\Controllers\UsabilidadeController;

// ===== USABILIDADE DO SGQ (SUPER ADMIN ONLY) =====

// Página principal
$router->get('/usabilidade', [UsabilidadeController::class, 'index']);

// APIs para dados
$router->get('/usabilidade/api/logins-por-dia', [UsabilidadeController::class, 'getLoginsPorDia']);
$router->get('/usabilidade/api/historico', [UsabilidadeController::class, 'getHistorico']);
$router->get('/usabilidade/api/estatisticas', [UsabilidadeController::class, 'getEstatisticas']);
