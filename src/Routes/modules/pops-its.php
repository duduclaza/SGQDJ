<?php
/**
 * Rotas do Módulo POPs e ITs
 * 
 * Sistema de Procedimentos Operacionais Padrão e Instruções de Trabalho
 */

use App\Controllers\PopItsController;

// ===== PRINCIPAL =====

$router->get('/pops-e-its', [PopItsController::class, 'index']);
$router->get('/pops-its/diagnostico', [PopItsController::class, 'diagnostico']);
$router->get('/pops-its/teste', [PopItsController::class, 'testeTitulos']);
$router->get('/pops-its/test', [PopItsController::class, 'testEndpoint']);

// ===== ABA 1: CADASTRO DE TÍTULOS =====

$router->post('/pops-its/titulo/create', [PopItsController::class, 'createTitulo']);
$router->get('/pops-its/titulos/list', [PopItsController::class, 'listTitulos']);
$router->get('/pops-its/titulos/search', [PopItsController::class, 'searchTitulos']);
$router->post('/pops-its/titulo/delete', [PopItsController::class, 'deleteTitulo']);

// ===== ABA 2: MEUS REGISTROS =====

$router->post('/pops-its/registro/create', [PopItsController::class, 'createRegistro']);
$router->post('/pops-its/registro/editar', [PopItsController::class, 'editarRegistro']);
$router->get('/pops-its/registros/meus', [PopItsController::class, 'listMeusRegistros']);
$router->get('/pops-its/arquivo/{id}', [PopItsController::class, 'downloadArquivo']);
$router->post('/pops-its/registro/update', [PopItsController::class, 'updateRegistro']);
$router->post('/pops-its/registro/delete', [PopItsController::class, 'deleteRegistro']);

// ===== ABA 3: PENDENTE APROVAÇÃO =====

$router->get('/pops-its/pendentes/list', [PopItsController::class, 'listPendentesAprovacao']);
$router->post('/pops-its/registro/aprovar', [PopItsController::class, 'aprovarRegistro']);
$router->post('/pops-its/registro/reprovar', [PopItsController::class, 'reprovarRegistro']);

// ===== ABA 4: VISUALIZAÇÃO =====

$router->get('/pops-its/visualizacao/list', [PopItsController::class, 'listVisualizacao']);
$router->get('/pops-its/visualizar/{id}', [PopItsController::class, 'visualizarArquivo']);

// ===== ABA 5: LOG DE VISUALIZAÇÕES =====

$router->get('/pops-its/logs/visualizacao', [PopItsController::class, 'listLogsVisualizacao']);
$router->post('/pops-its/registrar-log', [PopItsController::class, 'registrarLog']);

// ===== SISTEMA DE SOLICITAÇÕES DE EXCLUSÃO =====

$router->post('/pops-its/solicitacao/create', [PopItsController::class, 'createSolicitacao']);
$router->get('/pops-its/solicitacoes/list', [PopItsController::class, 'listSolicitacoes']);
$router->post('/pops-its/solicitacao/aprovar', [PopItsController::class, 'aprovarSolicitacao']);
$router->post('/pops-its/solicitacao/reprovar', [PopItsController::class, 'reprovarSolicitacao']);

// ===== TESTES E NOTIFICAÇÕES =====

$router->get('/pops-its/teste-notificacoes', [PopItsController::class, 'testeNotificacoes']);
$router->post('/pops-its/teste-notificacao-manual', [PopItsController::class, 'testeNotificacaoManual']);
