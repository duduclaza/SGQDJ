<?php
/**
 * Rotas do Módulo Triagem de Toners
 */

use App\Controllers\TriagemTonersController;

$router->get('/triagem-toners',               [TriagemTonersController::class, 'index']);
$router->get('/triagem-toners/list',          [TriagemTonersController::class, 'list']);
$router->post('/triagem-toners/calcular',     [TriagemTonersController::class, 'calcular']);
$router->post('/triagem-toners/store',        [TriagemTonersController::class, 'store']);
$router->post('/triagem-toners/update',       [TriagemTonersController::class, 'update']);
$router->post('/triagem-toners/delete',       [TriagemTonersController::class, 'delete']);
$router->get('/triagem-toners/parametros',    [TriagemTonersController::class, 'getParametrosApi']);
$router->post('/triagem-toners/parametros/save', [TriagemTonersController::class, 'saveParametros']);
