<?php

namespace App\Controllers;

use App\Services\PermissionService;

class RhController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance()->getConnection();
    }
    
    /**
     * Página principal do módulo RH (Em Breve)
     */
    public function index()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Verificar permissão: Admin, Super Admin ou setor RH
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        $userSetor = $_SESSION['user_setor'] ?? '';
        
        $isAdmin = in_array($userRole, ['admin', 'super_admin']);
        $isRhSetor = strtoupper($userSetor) === 'RH' || stripos($userSetor, 'recursos humanos') !== false;
        
        if (!$isAdmin && !$isRhSetor) {
            header('Location: /inicio');
            exit;
        }
        
        $title = 'RH - Recursos Humanos - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/rh/index.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }
    
    /**
     * Verifica permissão de acesso ao RH
     */
    private function checkRhPermission()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $userRole = $_SESSION['user_role'] ?? '';
        $userSetor = $_SESSION['user_setor'] ?? '';
        
        $isAdmin = in_array($userRole, ['admin', 'super_admin']);
        $isRhSetor = strtoupper($userSetor) === 'RH' || stripos($userSetor, 'recursos humanos') !== false;
        
        if (!$isAdmin && !$isRhSetor) {
            header('Location: /inicio');
            exit;
        }
        
        return true;
    }
    
    /**
     * Submódulo: Avaliação de Desempenho
     */
    public function avaliacaoDesempenho()
    {
        $this->checkRhPermission();
        
        $title = 'Avaliação de Desempenho - RH - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/rh/avaliacao-desempenho.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }
    
    /**
     * API: Listar colaboradores (usuários do sistema)
     */
    public function listarColaboradores()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, setor, filial, role, status, created_at
                FROM users 
                WHERE status = 'active'
                ORDER BY name ASC
            ");
            $stmt->execute();
            $colaboradores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'colaboradores' => $colaboradores]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Listar avaliações
     */
    public function listarAvaliacoes()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            // Buscar usuários reais do banco como colaboradores
            $stmt = $this->db->prepare("
                SELECT id, name, email, setor, filial, role
                FROM users 
                WHERE status = 'active'
                ORDER BY name ASC
            ");
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Por enquanto, gerar avaliações de exemplo baseadas nos usuários reais
            $avaliacoes = [];
            foreach ($users as $index => $user) {
                // Apenas alguns usuários terão avaliações de exemplo
                if ($index < 5) {
                    $avaliacoes[] = [
                        'id' => $index + 1,
                        'user_id' => $user['id'],
                        'colaborador' => $user['name'],
                        'cargo' => $user['role'] ?? 'Colaborador',
                        'departamento' => $user['setor'] ?? 'Não informado',
                        'avaliador' => $_SESSION['user_name'] ?? 'Administrador',
                        'data_avaliacao' => $index < 3 ? date('Y-m-d', strtotime("-{$index} days")) : null,
                        'nota_geral' => $index < 3 ? round(7 + (mt_rand(0, 30) / 10), 1) : null,
                        'status' => $index < 3 ? 'concluída' : 'pendente'
                    ];
                }
            }
            
            echo json_encode(['success' => true, 'avaliacoes' => $avaliacoes]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
