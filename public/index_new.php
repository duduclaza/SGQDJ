<?php
/**
 * SGQ OTI DJ - Entry Point
 * Versão Refatorada com Sistema Modular de Rotas
 * 
 * Este arquivo foi refatorado de 708 linhas para ~130 linhas
 * Todas as rotas agora estão em arquivos modulares em /routes
 */

// =============================================================================
// BOOTSTRAP
// =============================================================================

session_start();

// No-cache headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Middleware\PermissionMiddleware;

// =============================================================================
// ENVIRONMENT
// =============================================================================

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
} catch (Exception $e) {
    die("Erro ao carregar .env: " . $e->getMessage());
}

// =============================================================================
// ERROR REPORTING (com whitelist de IPs para segurança)
// =============================================================================

$isDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

// Security: Only allow debug mode for whitelisted IPs
if ($isDebug) {
    $allowedDebugIPs = [
        '127.0.0.1',    // Localhost IPv4
        '::1',          // Localhost IPv6
        // Adicione IPs de desenvolvimento autorizados aqui:
        // '192.168.1.100',
    ];
    
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Se não estiver na whitelist, desabilita debug mesmo que APP_DEBUG=true
    if (!in_array($clientIP, $allowedDebugIPs)) {
        $isDebug = false;
        error_log("Debug mode tentado de IP não autorizado: {$clientIP}");
    }
}

// Configure error reporting
if ($isDebug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// =============================================================================
// ROUTER INITIALIZATION
// =============================================================================

$router = new Router(__DIR__);

// =============================================================================
// LOAD ALL ROUTES (Sistema Modular)
// =============================================================================

// Carrega RouteServiceProvider
require_once __DIR__ . '/../routes/RouteServiceProvider.php';

// Registra TODAS as rotas de forma modular
RouteServiceProvider::register($router);

// =============================================================================
// MIDDLEWARE & DISPATCH
// =============================================================================

try {
    $currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    // Define rotas públicas (sem autenticação/permissão)
    $isPublicRoute = (
        $currentRoute === '/' ||  // Rota raiz tem lógica própria
        $currentRoute === '/inicio' ||  // Página inicial para logados
        strpos($currentRoute, '/login') === 0 ||
        strpos($currentRoute, '/auth/') === 0 ||
        strpos($currentRoute, '/register') === 0 ||
        strpos($currentRoute, '/logout') === 0 ||
        strpos($currentRoute, '/password-reset') === 0 ||
        strpos($currentRoute, '/request-access') === 0 ||
        strpos($currentRoute, '/access-request') === 0 ||
        strpos($currentRoute, '/nps/responder/') === 0 ||  // NPS público
        strpos($currentRoute, '/nps/salvar-resposta') === 0 ||
        strpos($currentRoute, '/area-tecnica/checklist') === 0  // Checklist público
    );
    
    // Aplica middleware de permissões apenas para rotas protegidas
    if (!$isPublicRoute) {
        PermissionMiddleware::handle($currentRoute, $method);
    }
    
    // Despacha a requisição
    $router->dispatch();
    
} catch (\Exception $e) {
    // =============================================================================
    // ERROR HANDLING
    // =============================================================================
    
    error_log('Application error: ' . $e->getMessage());
    
    // Log detalhado em arquivo
    try {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . '/app_' . date('Y-m-d') . '.log';
        
        $context = [
            'timestamp' => date('Y-m-d H:i:s'),
            'route' => $currentRoute ?? 'unknown',
            'method' => $method ?? 'unknown',
            'session_user_id' => $_SESSION['user_id'] ?? null,
            'session_user_email' => $_SESSION['user_email'] ?? null,
            'get' => $_GET ?? [],
            'post_keys' => array_keys($_POST ?? []),
            'exception' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
        ];
        
        $logEntry = '[' . $context['timestamp'] . '] ' . $context['method'] . ' ' . $context['route'] . ' | User: ' . ($context['session_user_id'] ?? 'guest') . '\n';
        $logEntry .= 'Message: ' . $context['exception']['message'] . ' (' . $context['exception']['file'] . ':' . $context['exception']['line'] . ')\n';
        $logEntry .= 'GET: ' . json_encode($context['get']) . ' | POST_KEYS: ' . json_encode($context['post_keys']) . '\n';
        $logEntry .= str_repeat('-', 80) . "\n";
        
        error_log($logEntry, 3, $logFile);
    } catch (\Throwable $logError) {
        error_log('Erro ao escrever log: ' . $logError->getMessage());
    }
    
    // Exibe erro conforme modo debug
    if ($isDebug) {
        echo '<h1>Erro: ' . htmlspecialchars($e->getMessage()) . '</h1>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Erro 500</title></head><body>';
        echo '<h1>Erro Interno do Servidor</h1>';
        echo '<p>Tente novamente em alguns minutos.</p>';
        echo '</body></html>';
    }
}
