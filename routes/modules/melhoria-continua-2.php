<?php
/**
 * Rotas do Módulo Melhoria Contínua 2.0
 * 
 * Sistema de solicitações de melhoria com pontuação
 */

use App\Controllers\MelhoriaContinua2Controller;

// ===== PRINCIPAL =====

$router->get('/melhoria-continua-2', [MelhoriaContinua2Controller::class, 'index']);
$router->post('/melhoria-continua-2/store', [MelhoriaContinua2Controller::class, 'store']);

// ===== ATUALIZAÇÃO =====

$router->post('/melhoria-continua-2/update', [MelhoriaContinua2Controller::class, 'update']);
$router->post('/melhoria-continua-2/update-status', [MelhoriaContinua2Controller::class, 'updateStatus']);
$router->post('/melhoria-continua-2/{id}/update-status', [MelhoriaContinua2Controller::class, 'updateStatus']);
$router->post('/melhoria-continua-2/{id}/update-pontuacao', [MelhoriaContinua2Controller::class, 'updatePontuacao']);

// ===== VISUALIZAÇÃO =====

$router->get('/melhoria-continua-2/{id}/details', [MelhoriaContinua2Controller::class, 'details']);
$router->get('/melhoria-continua-2/{id}/view', [MelhoriaContinua2Controller::class, 'view']);

// ===== EXCLUSÃO E RELATÓRIOS =====

$router->post('/melhoria-continua-2/delete', [MelhoriaContinua2Controller::class, 'delete']);
$router->get('/melhoria-continua-2/export', [MelhoriaContinua2Controller::class, 'exportExcel']);
