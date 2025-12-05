<?php
/**
 * Rotas do Módulo Fluxogramas
 * 
 * Sistema de gestão de fluxogramas de processos
 */

use App\Controllers\FluxogramasController;

// ===== PRINCIPAL =====

$router->get('/fluxogramas', [FluxogramasController::class, 'index']);

// ===== TÍTULOS =====

$router->post('/fluxogramas/titulo/create', [FluxogramasController::class, 'createTitulo']);
$router->get('/fluxogramas/titulos/list', [FluxogramasController::class, 'listTitulos']);
$router->get('/fluxogramas/titulos/search', [FluxogramasController::class, 'searchTitulos']);
$router->post('/fluxogramas/titulo/delete', [FluxogramasController::class, 'deleteTitulo']);

// ===== REGISTROS =====

$router->post('/fluxogramas/registro/create', [FluxogramasController::class, 'createRegistro']);
$router->post('/fluxogramas/registro/editar', [FluxogramasController::class, 'editarRegistro']);
$router->post('/fluxogramas/registros/atualizar-visibilidade', [FluxogramasController::class, 'atualizarVisibilidade']);
$router->get('/fluxogramas/registros/meus', [FluxogramasController::class, 'listMeusRegistros']);
$router->get('/fluxogramas/registros/{id}', [FluxogramasController::class, 'getRegistro']);
$router->get('/fluxogramas/arquivo/{id}', [FluxogramasController::class, 'downloadArquivo']);
$router->get('/fluxogramas/visualizar/{id}', [FluxogramasController::class, 'visualizarArquivo']);

// ===== APROVAÇÃO =====

$router->get('/fluxogramas/pendentes/list', [FluxogramasController::class, 'listPendentes']);
$router->post('/fluxogramas/registro/aprovar', [FluxogramasController::class, 'aprovarRegistro']);
$router->post('/fluxogramas/registro/reprovar', [FluxogramasController::class, 'reprovarRegistro']);

// ===== SOLICITAÇÕES DE EXCLUSÃO =====

$router->post('/fluxogramas/solicitacao/create', [FluxogramasController::class, 'createSolicitacaoExclusao']);
$router->get('/fluxogramas/solicitacoes/list', [FluxogramasController::class, 'listSolicitacoes']);
$router->post('/fluxogramas/solicitacao/aprovar', [FluxogramasController::class, 'aprovarSolicitacao']);
$router->post('/fluxogramas/solicitacao/reprovar', [FluxogramasController::class, 'reprovarSolicitacao']);

// ===== VISUALIZAÇÃO E LOGS =====

$router->get('/fluxogramas/visualizacao/list', [FluxogramasController::class, 'listVisualizacao']);
$router->get('/fluxogramas/logs/visualizacao', [FluxogramasController::class, 'listLogs']);
