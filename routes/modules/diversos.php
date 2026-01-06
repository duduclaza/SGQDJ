<?php
/**
 * Rotas de Módulos Diversos
 * 
 * Agrupa módulos menores: Controle RC, Descartes, Não Conformidades,
 * 5W2H, Auditorias, FMEA, Certificados, Cadastros
 */

use App\Controllers\ControleRcController;
use App\Controllers\ControleDescartesController;
use App\Controllers\NaoConformidadesController;
use App\Controllers\Planos5W2HController;
use App\Controllers\AuditoriasController;
use App\Controllers\FMEAController;
use App\Controllers\CertificadosController;
use App\Controllers\CadastroMaquinasController;
use App\Controllers\CadastroPecasController;

// ===== CONTROLE DE RC =====

$router->get('/controle-de-rc', [ControleRcController::class, 'index']);
$router->get('/controle-rc', [ControleRcController::class, 'index']);
$router->get('/controle-rc/list', [ControleRcController::class, 'list']);
$router->post('/controle-rc/create', [ControleRcController::class, 'create']);
$router->post('/controle-rc/update', [ControleRcController::class, 'update']);
$router->post('/controle-rc/update-status', [ControleRcController::class, 'updateStatus']);
$router->post('/controle-rc/delete', [ControleRcController::class, 'delete']);
$router->post('/controle-rc/alterar-status', [ControleRcController::class, 'alterarStatus']);
$router->get('/controle-rc/{id}', [ControleRcController::class, 'show']);
$router->get('/controle-rc/{id}/print', [ControleRcController::class, 'print']);
$router->post('/controle-rc/export', [ControleRcController::class, 'exportReport']);
$router->get('/controle-rc/evidencia/{id}', [ControleRcController::class, 'downloadEvidencia']);

// ===== CONTROLE DE DESCARTES =====

$router->get('/controle-descartes', [ControleDescartesController::class, 'index']);
$router->get('/controle-descartes/list', [ControleDescartesController::class, 'listDescartes']);
$router->get('/controle-descartes/action-logs', [ControleDescartesController::class, 'listLogs']);
$router->get('/controle-descartes/first-date', [ControleDescartesController::class, 'getFirstRecordDate']);
$router->get('/controle-descartes/template', [ControleDescartesController::class, 'downloadTemplate']);
$router->get('/controle-descartes/relatorios', [ControleDescartesController::class, 'relatorios']);
$router->get('/controle-descartes/anexo/{id}', [ControleDescartesController::class, 'downloadAnexo']);
$router->get('/controle-descartes/{id}', [ControleDescartesController::class, 'getDescarte']);
$router->post('/controle-descartes/verify-admin', [ControleDescartesController::class, 'verifyAdminPassword']);
$router->post('/controle-descartes/create', [ControleDescartesController::class, 'create']);
$router->post('/controle-descartes/update', [ControleDescartesController::class, 'update']);
$router->post('/controle-descartes/delete', [ControleDescartesController::class, 'delete']);
$router->post('/controle-descartes/alterar-status', [ControleDescartesController::class, 'alterarStatus']);
$router->post('/controle-descartes/alterar-status-andamento', [ControleDescartesController::class, 'alterarStatusAndamento']);
$router->post('/controle-descartes/importar', [ControleDescartesController::class, 'importar']);

// ===== NÃO CONFORMIDADES =====

$router->get('/nao-conformidades', [NaoConformidadesController::class, 'index']);
$router->post('/nao-conformidades/criar', [NaoConformidadesController::class, 'criar']);
$router->get('/nao-conformidades/detalhes/{id}', [NaoConformidadesController::class, 'detalhes']);
$router->post('/nao-conformidades/registrar-acao/{id}', [NaoConformidadesController::class, 'registrarAcao']);
$router->post('/nao-conformidades/mover-em-andamento/{id}', [NaoConformidadesController::class, 'moverParaEmAndamento']);
$router->post('/nao-conformidades/marcar-solucionada/{id}', [NaoConformidadesController::class, 'marcarSolucionada']);
$router->get('/nao-conformidades/anexo/{id}', [NaoConformidadesController::class, 'downloadAnexo']);
$router->post('/nao-conformidades/excluir/{id}', [NaoConformidadesController::class, 'excluir']);

// ===== 5W2H =====

$router->get('/5w2h', [Planos5W2HController::class, 'index']);
$router->get('/5w2h/list', [Planos5W2HController::class, 'listPlanos']);
$router->post('/5w2h/create', [Planos5W2HController::class, 'create']);
$router->post('/5w2h/update', [Planos5W2HController::class, 'update']);
$router->post('/5w2h/delete', [Planos5W2HController::class, 'delete']);
$router->get('/5w2h/{id}', [Planos5W2HController::class, 'getPlano']);
$router->get('/5w2h/details/{id}', [Planos5W2HController::class, 'details']);
$router->get('/5w2h/print/{id}', [Planos5W2HController::class, 'printPlano']);
$router->get('/5w2h/anexos/{id}', [Planos5W2HController::class, 'anexos']);
$router->get('/5w2h/anexo/{id}', [Planos5W2HController::class, 'downloadAnexo']);
$router->get('/5w2h/relatorios', [Planos5W2HController::class, 'relatorios']);

// ===== AUDITORIAS =====

$router->get('/auditorias', [AuditoriasController::class, 'index']);
$router->get('/auditorias/list', [AuditoriasController::class, 'listAuditorias']);
$router->post('/auditorias/create', [AuditoriasController::class, 'create']);
$router->post('/auditorias/update', [AuditoriasController::class, 'update']);
$router->post('/auditorias/delete', [AuditoriasController::class, 'delete']);
$router->get('/auditorias/{id}', [AuditoriasController::class, 'getAuditoria']);
$router->get('/auditorias/anexo/{id}', [AuditoriasController::class, 'downloadAnexo']);
$router->get('/auditorias/relatorios', [AuditoriasController::class, 'relatorios']);

// ===== FMEA =====

$router->get('/fmea', [FMEAController::class, 'index']);
$router->get('/fmea/list', [FMEAController::class, 'list']);
$router->post('/fmea/store', [FMEAController::class, 'store']);
$router->get('/fmea/{id}', [FMEAController::class, 'show']);
$router->post('/fmea/{id}/update', [FMEAController::class, 'update']);
$router->delete('/fmea/{id}/delete', [FMEAController::class, 'delete']);
$router->get('/fmea/charts', [FMEAController::class, 'chartData']);
$router->get('/fmea/{id}/print', [FMEAController::class, 'print']);

// ===== CERTIFICADOS =====

$router->get('/certificados', [CertificadosController::class, 'index']);
$router->post('/certificados/store', [CertificadosController::class, 'store']);
$router->get('/certificados/download/{id}', [CertificadosController::class, 'download']);
$router->post('/certificados/delete', [CertificadosController::class, 'delete']);

// ===== CADASTRO DE MÁQUINAS =====

$router->get('/cadastro-maquinas', [CadastroMaquinasController::class, 'index']);
$router->post('/cadastro-maquinas/store', [CadastroMaquinasController::class, 'store']);
$router->post('/cadastro-maquinas/update', [CadastroMaquinasController::class, 'update']);
$router->post('/cadastro-maquinas/delete', [CadastroMaquinasController::class, 'delete']);

// ===== CADASTRO DE PEÇAS =====

// ===== CADASTRO DE PEÇAS =====

$router->get('/cadastro-pecas', [CadastroPecasController::class, 'index']);
$router->post('/cadastro-pecas/store', [CadastroPecasController::class, 'store']);
$router->post('/cadastro-pecas/update', [CadastroPecasController::class, 'update']);
$router->post('/cadastro-pecas/delete', [CadastroPecasController::class, 'delete']);
$router->post('/cadastro-pecas/import', [CadastroPecasController::class, 'import']);

// ===== ROTA DE TESTE DE EMAIL (DEBUG) =====
use App\Controllers\TesteEmailController;
$router->get('/teste-smtp-debug', [TesteEmailController::class, 'index']);
$router->post('/teste-smtp-debug', [TesteEmailController::class, 'index']);

// ===== ROTA DE TESTE RESEND API =====
use App\Controllers\TesteResendController;
$router->get('/teste-resend', [TesteResendController::class, 'index']);
$router->post('/teste-resend', [TesteResendController::class, 'index']);
