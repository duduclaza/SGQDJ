<?php
/**
 * Rotas do Módulo Atendimento
 * 
 * Inclui:
 * - Calculadora de Envio de Toners
 */

use App\Controllers\AtendimentoController;

// ===== CALCULADORA DE ENVIO DE TONERS =====

$router->get('/atendimento/calculadora-toners', [AtendimentoController::class , 'index']);
$router->get('/atendimento/calculadora-toners/buscar', [AtendimentoController::class , 'buscarToners']);
$router->get('/atendimento/calculadora-toners/config', [AtendimentoController::class , 'getConfig']);
$router->post('/atendimento/calculadora-toners/config', [AtendimentoController::class , 'saveConfig']);
