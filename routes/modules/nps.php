<?php
/**
 * Rotas do Módulo NPS (Net Promoter Score)
 * 
 * Sistema de pesquisas de satisfação
 */

use App\Controllers\NpsController;

// ===== PRINCIPAL =====

$router->get('/nps', [NpsController::class, 'index']);
$router->get('/nps/dashboard', [NpsController::class, 'dashboard']);
$router->get('/nps/dashboard/data', [NpsController::class, 'getDashboardData']);

// ===== GESTÃO DE PESQUISAS =====

$router->get('/nps/listar', [NpsController::class, 'listar']);
$router->post('/nps/criar', [NpsController::class, 'criar']);
$router->post('/nps/editar', [NpsController::class, 'editar']);
$router->post('/nps/toggle-status', [NpsController::class, 'toggleStatus']);
$router->post('/nps/excluir', [NpsController::class, 'excluir']);
$router->post('/nps/duplicar', [NpsController::class, 'duplicar']);

// ===== DETALHES E RESPOSTAS =====

$router->get('/nps/{id}/detalhes', [NpsController::class, 'detalhes']);
$router->get('/nps/{id}/respostas', [NpsController::class, 'verRespostas']);
$router->post('/nps/excluir-resposta', [NpsController::class, 'excluirResposta']);

// ===== ROTAS PÚBLICAS (SEM AUTENTICAÇÃO) =====

$router->get('/nps/responder/{id}', [NpsController::class, 'responder']);
$router->post('/nps/salvar-resposta', [NpsController::class, 'salvarResposta']);

// ===== RELATÓRIOS E MANUTENÇÃO =====

$router->get('/nps/exportar-csv', [NpsController::class, 'exportarCSV']);
$router->get('/nps/contar-orfas', [NpsController::class, 'contarRespostasOrfas']);
$router->post('/nps/limpar-orfas', [NpsController::class, 'limparRespostasOrfas']);
