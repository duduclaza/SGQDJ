<?php
/**
 * Rotas Diversas e Especiais
 * 
 * Registros, Suporte, Financeiro, Master, Área Técnica, CRM, Implantação, Logística, Profile
 */

use App\Controllers\RegistrosController;
use App\Controllers\ClientesController;
use App\Controllers\SuporteController;
use App\Controllers\FinanceiroController;
use App\Controllers\MasterController;
use App\Controllers\AreaTecnicaController;
use App\Controllers\CRMController;
use App\Controllers\ImplantacaoController;
use App\Controllers\ProfileController;
use App\Controllers\PageController;

// ===== REGISTROS GERAIS =====

$router->get('/registros/filiais', [RegistrosController::class, 'filiais']);
$router->get('/registros/departamentos', [RegistrosController::class, 'departamentos']);
$router->get('/registros/fornecedores', [RegistrosController::class, 'fornecedores']);
$router->get('/registros/parametros', [RegistrosController::class, 'parametros']);

// Store
$router->post('/registros/filiais/store', [RegistrosController::class, 'storeFilial']);
$router->post('/registros/departamentos/store', [RegistrosController::class, 'storeDepartamento']);
$router->post('/registros/fornecedores/store', [RegistrosController::class, 'storeFornecedor']);
$router->post('/registros/parametros/store', [RegistrosController::class, 'storeParametro']);

// Update
$router->post('/registros/filiais/update', [RegistrosController::class, 'updateFilial']);
$router->post('/registros/departamentos/update', [RegistrosController::class, 'updateDepartamento']);
$router->post('/registros/fornecedores/update', [RegistrosController::class, 'updateFornecedor']);
$router->post('/registros/parametros/update', [RegistrosController::class, 'updateParametro']);

// Delete
$router->post('/registros/filiais/delete', [RegistrosController::class, 'deleteFilial']);
$router->post('/registros/departamentos/delete', [RegistrosController::class, 'deleteDepartamento']);
$router->post('/registros/fornecedores/delete', [RegistrosController::class, 'deleteFornecedor']);
$router->post('/registros/parametros/delete', [RegistrosController::class, 'deleteParametro']);

// ===== CADASTRO DE CLIENTES =====

$router->get('/cadastros/clientes', [ClientesController::class, 'index']);
$router->get('/cadastros/clientes/listar', [ClientesController::class, 'listar']);
$router->post('/cadastros/clientes/criar', [ClientesController::class, 'criar']);
$router->post('/cadastros/clientes/atualizar', [ClientesController::class, 'atualizar']);
$router->post('/cadastros/clientes/excluir', [ClientesController::class, 'excluir']);
$router->post('/cadastros/clientes/importar', [ClientesController::class, 'importar']);
$router->get('/cadastros/clientes/template', [ClientesController::class, 'template']);

// Contratos (placeholder)
$router->get('/cadastros/contratos', function() {
    $viewFile = __DIR__ . '/../views/pages/cadastros/contratos.php';
    include __DIR__ . '/../views/layouts/main.php';
});

// ===== SUPORTE =====

$router->get('/suporte', [SuporteController::class, 'index']);
$router->post('/suporte/store', [SuporteController::class, 'store']);
$router->post('/suporte/update-status', [SuporteController::class, 'updateStatus']);
$router->post('/suporte/delete', [SuporteController::class, 'delete']);
$router->get('/suporte/{id}/details', [SuporteController::class, 'details']);
$router->get('/suporte/anexo/{anexoId}', [SuporteController::class, 'downloadAnexo']);

// ===== FINANCEIRO =====

$router->get('/financeiro', [FinanceiroController::class, 'index']);
$router->post('/financeiro/anexar-comprovante', [FinanceiroController::class, 'anexarComprovante']);
$router->get('/financeiro/{id}/download-comprovante', [FinanceiroController::class, 'downloadComprovante']);

// ===== MASTER (Aprovação de Pagamentos) =====

$router->get('/master/login', [MasterController::class, 'loginPage']);
$router->post('/master/auth', [MasterController::class, 'authenticate']);
$router->get('/master/dashboard', [MasterController::class, 'dashboard']);
$router->post('/master/aprovar-pagamento', [MasterController::class, 'aprovarPagamento']);
$router->get('/master/logout', [MasterController::class, 'logout']);

// ===== ÁREA TÉCNICA =====

$router->get('/area-tecnica', [AreaTecnicaController::class, 'index']);
$router->post('/area-tecnica/ativar-trial', [AreaTecnicaController::class, 'ativarTrial']);
$router->get('/area-tecnica/trial-status', [AreaTecnicaController::class, 'getTrialStatus']);

// Checklist Virtual (rota pública - sem login)
$router->get('/area-tecnica/checklist', [AreaTecnicaController::class, 'checklistPublico']);
$router->post('/area-tecnica/checklist/salvar', [AreaTecnicaController::class, 'salvarChecklist']);

// Consulta de Checklists
$router->get('/area-tecnica/consulta', [AreaTecnicaController::class, 'consultaChecklists']);
$router->get('/area-tecnica/checklists/buscar', [AreaTecnicaController::class, 'buscarChecklists']);
$router->get('/area-tecnica/checklists/listar', [AreaTecnicaController::class, 'listarTodosChecklists']);
$router->get('/area-tecnica/checklists/{id}', [AreaTecnicaController::class, 'verChecklist']);

// ===== CRM (EM DESENVOLVIMENTO) =====

$router->get('/crm/prospeccao', [CRMController::class, 'prospeccao']);
$router->get('/crm/vendas', [CRMController::class, 'vendas']);
$router->get('/crm/relacionamento', [CRMController::class, 'relacionamento']);
$router->get('/crm/marketing', [CRMController::class, 'marketing']);
$router->get('/crm/relatorios', [CRMController::class, 'relatorios']);
$router->get('/crm/dashboards', [CRMController::class, 'dashboards']);

// ===== IMPLANTAÇÃO (EM DESENVOLVIMENTO) =====

$router->get('/implantacao/dpo', [ImplantacaoController::class, 'dpo']);
$router->get('/implantacao/ordem-servicos', [ImplantacaoController::class, 'ordemServicos']);
$router->get('/implantacao/fluxo', [ImplantacaoController::class, 'fluxo']);
$router->get('/implantacao/relatorios', [ImplantacaoController::class, 'relatorios']);

// ===== LOGÍSTICA (PREMIUM) =====

$router->get('/logistica/entrada-estoque', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/entrada-estoque.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/entrada-almoxarifados', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/entrada-almoxarifados.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/inventarios', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/inventarios.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/consulta-estoque', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/consulta-estoque.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/consulta-almoxarifado', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/consulta-almoxarifado.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/transferencias-internas', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/transferencias-internas.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/transferencias-externas', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/transferencias-externas.php';
    include __DIR__ . '/../views/layouts/main.php';
});

$router->get('/logistica/estoque-tecnico', function() {
    $viewFile = __DIR__ . '/../views/pages/logistica/estoque-tecnico.php';
    include __DIR__ . '/../views/layouts/main.php';
});

// ===== E-LEARNING ATLAS (EM BREVE) =====
$router->get('/e-learning-atlas', [PageController::class, 'eLearningAtlas']);

// ===== PROFILE (Perfil do Usuário) =====

$router->get('/profile', [ProfileController::class, 'index']);
