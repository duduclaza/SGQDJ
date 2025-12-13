<?php

namespace App\Controllers;

use App\Config\Database;

/**
 * Controller para o módulo Usabilidade do SGQ
 * 
 * Este módulo é EXCLUSIVO para super_admin e permite monitorar:
 * - Gráfico de logins por dia
 * - Histórico detalhado de acessos ao sistema
 */
class UsabilidadeController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTableExists();
    }
    
    /**
     * Verificar se o usuário é super_admin
     */
    private function requireSuperAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Verificar se é super_admin (usando a função helper)
        if (!isSuperAdmin()) {
            // Redirecionar para início com mensagem de erro
            $_SESSION['flash']['error'] = 'Acesso negado. Este módulo é exclusivo para Super Admin.';
            header('Location: /inicio');
            exit;
        }
    }
    
    /**
     * Garantir que a tabela de logs existe
     */
    private function ensureTableExists(): void
    {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS login_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    user_name VARCHAR(255) NOT NULL,
                    user_email VARCHAR(255) NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    
                    INDEX idx_user_id (user_id),
                    INDEX idx_login_at (login_at),
                    INDEX idx_user_email (user_email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            error_log("UsabilidadeController: Erro ao criar tabela login_logs: " . $e->getMessage());
        }
    }
    
    /**
     * Registrar um login no sistema (método estático para ser chamado do AuthController)
     */
    public static function registrarLogin(int $userId, string $userName, string $userEmail): void
    {
        try {
            $db = Database::getInstance();
            
            // Garantir que a tabela existe
            $db->exec("
                CREATE TABLE IF NOT EXISTS login_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    user_name VARCHAR(255) NOT NULL,
                    user_email VARCHAR(255) NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    
                    INDEX idx_user_id (user_id),
                    INDEX idx_login_at (login_at),
                    INDEX idx_user_email (user_email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            // Obter IP e User Agent
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Inserir log de login
            $stmt = $db->prepare("
                INSERT INTO login_logs (user_id, user_name, user_email, ip_address, user_agent, login_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $userName, $userEmail, $ipAddress, $userAgent]);
            
        } catch (\Exception $e) {
            error_log("UsabilidadeController::registrarLogin: " . $e->getMessage());
        }
    }
    
    /**
     * Página principal do módulo
     */
    public function index(): void
    {
        $this->requireSuperAdmin();
        
        $title = 'Usabilidade do SGQ - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/usabilidade/index.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }
    
    /**
     * API: Retornar dados de logins por dia para o gráfico
     */
    public function getLoginsPorDia(): void
    {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        try {
            // Últimos 30 dias de logins
            $dias = (int)($_GET['dias'] ?? 30);
            if ($dias < 7) $dias = 7;
            if ($dias > 90) $dias = 90;
            
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(login_at) as data,
                    COUNT(*) as total_logins,
                    COUNT(DISTINCT user_id) as usuarios_unicos
                FROM login_logs
                WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(login_at)
                ORDER BY data ASC
            ");
            $stmt->execute([$dias]);
            $dados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Preencher dias sem logins com zero
            $resultado = [];
            $dataInicio = new \DateTime("-{$dias} days");
            $dataFim = new \DateTime();
            
            $interval = new \DateInterval('P1D');
            $periodo = new \DatePeriod($dataInicio, $interval, $dataFim->modify('+1 day'));
            
            $dadosIndexados = [];
            foreach ($dados as $d) {
                $dadosIndexados[$d['data']] = $d;
            }
            
            foreach ($periodo as $data) {
                $dataStr = $data->format('Y-m-d');
                $resultado[] = [
                    'data' => $dataStr,
                    'data_formatada' => $data->format('d/m'),
                    'total_logins' => isset($dadosIndexados[$dataStr]) ? (int)$dadosIndexados[$dataStr]['total_logins'] : 0,
                    'usuarios_unicos' => isset($dadosIndexados[$dataStr]) ? (int)$dadosIndexados[$dataStr]['usuarios_unicos'] : 0
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);
            
        } catch (\Exception $e) {
            error_log("getLoginsPorDia error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar dados: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Retornar histórico de logins
     */
    public function getHistorico(): void
    {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(100, max(10, (int)($_GET['limit'] ?? 50)));
            $offset = ($page - 1) * $limit;
            
            $filtroUsuario = $_GET['usuario'] ?? '';
            $filtroData = $_GET['data'] ?? '';
            
            // Construir query com filtros
            $where = "WHERE 1=1";
            $params = [];
            
            if (!empty($filtroUsuario)) {
                $where .= " AND (user_name LIKE ? OR user_email LIKE ?)";
                $params[] = "%{$filtroUsuario}%";
                $params[] = "%{$filtroUsuario}%";
            }
            
            if (!empty($filtroData)) {
                $where .= " AND DATE(login_at) = ?";
                $params[] = $filtroData;
            }
            
            // Contar total
            $stmtCount = $this->db->prepare("SELECT COUNT(*) as total FROM login_logs {$where}");
            $stmtCount->execute($params);
            $total = $stmtCount->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Buscar dados paginados
            $stmt = $this->db->prepare("
                SELECT 
                    id,
                    user_id,
                    user_name,
                    user_email,
                    ip_address,
                    user_agent,
                    login_at,
                    DATE_FORMAT(login_at, '%d/%m/%Y %H:%i') as login_formatado
                FROM login_logs
                {$where}
                ORDER BY login_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $logs,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => (int)$total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("getHistorico error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar histórico: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Retornar estatísticas gerais
     */
    public function getEstatisticas(): void
    {
        $this->requireSuperAdmin();
        header('Content-Type: application/json');
        
        try {
            // Total de logins hoje
            $stmtHoje = $this->db->query("
                SELECT COUNT(*) as total FROM login_logs WHERE DATE(login_at) = CURDATE()
            ");
            $loginsHoje = $stmtHoje->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Total de logins últimos 7 dias
            $stmt7dias = $this->db->query("
                SELECT COUNT(*) as total FROM login_logs WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $logins7dias = $stmt7dias->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Total de logins últimos 30 dias
            $stmt30dias = $this->db->query("
                SELECT COUNT(*) as total FROM login_logs WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $logins30dias = $stmt30dias->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Total geral de logins
            $stmtTotal = $this->db->query("SELECT COUNT(*) as total FROM login_logs");
            $totalLogins = $stmtTotal->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Usuários únicos últimos 30 dias
            $stmtUnicos = $this->db->query("
                SELECT COUNT(DISTINCT user_id) as total FROM login_logs WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $usuariosUnicos = $stmtUnicos->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Último login registrado
            $stmtUltimo = $this->db->query("
                SELECT user_name, user_email, login_at, DATE_FORMAT(login_at, '%d/%m/%Y %H:%i') as formatado 
                FROM login_logs 
                ORDER BY login_at DESC 
                LIMIT 1
            ");
            $ultimoLogin = $stmtUltimo->fetch(\PDO::FETCH_ASSOC);
            
            // Usuários mais ativos (top 5)
            $stmtTop = $this->db->query("
                SELECT 
                    user_name,
                    user_email,
                    COUNT(*) as total_logins,
                    MAX(login_at) as ultimo_login
                FROM login_logs
                WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY user_id, user_name, user_email
                ORDER BY total_logins DESC
                LIMIT 5
            ");
            $usuariosMaisAtivos = $stmtTop->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'logins_hoje' => (int)$loginsHoje,
                    'logins_7_dias' => (int)$logins7dias,
                    'logins_30_dias' => (int)$logins30dias,
                    'total_logins' => (int)$totalLogins,
                    'usuarios_unicos_30_dias' => (int)$usuariosUnicos,
                    'ultimo_login' => $ultimoLogin,
                    'usuarios_mais_ativos' => $usuariosMaisAtivos
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("getEstatisticas error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
        }
    }
}
