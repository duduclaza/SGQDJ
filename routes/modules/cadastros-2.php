<?php
/**
 * Rotas do Módulo Cadastros 2.0
 * 
 * Unifica cadastros de Toners, Peças e Máquinas
 */

use App\Controllers\CadastrosProdutosController;

// ===== PÁGINA PRINCIPAL =====
$router->get('/cadastros-2', [CadastrosProdutosController::class, 'index']);

// ===== API =====
$router->get('/cadastros-2/list', [CadastrosProdutosController::class, 'list']);
$router->get('/cadastros-2/get/{id}', [CadastrosProdutosController::class, 'get']);

// ===== CRUD =====
$router->post('/cadastros-2/store', [CadastrosProdutosController::class, 'store']);
$router->post('/cadastros-2/update', [CadastrosProdutosController::class, 'update']);
$router->post('/cadastros-2/delete', [CadastrosProdutosController::class, 'delete']);
