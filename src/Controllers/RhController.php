<?php

namespace App\Controllers;

use App\Services\PermissionService;

class RhController
{
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
     * API: Listar avaliações
     */
    public function listarAvaliacoes()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        // Por enquanto retorna dados de exemplo
        $avaliacoes = [
            [
                'id' => 1,
                'colaborador' => 'João Silva',
                'cargo' => 'Analista de TI',
                'departamento' => 'Tecnologia',
                'avaliador' => 'Maria Santos',
                'data_avaliacao' => '2026-01-10',
                'nota_geral' => 8.5,
                'status' => 'concluída'
            ],
            [
                'id' => 2,
                'colaborador' => 'Ana Costa',
                'cargo' => 'Coordenadora de Qualidade',
                'departamento' => 'Qualidade',
                'avaliador' => 'Carlos Oliveira',
                'data_avaliacao' => '2026-01-12',
                'nota_geral' => 9.2,
                'status' => 'concluída'
            ],
            [
                'id' => 3,
                'colaborador' => 'Pedro Souza',
                'cargo' => 'Técnico de Suporte',
                'departamento' => 'Tecnologia',
                'avaliador' => 'Maria Santos',
                'data_avaliacao' => null,
                'nota_geral' => null,
                'status' => 'pendente'
            ]
        ];
        
        echo json_encode(['success' => true, 'avaliacoes' => $avaliacoes]);
        exit;
    }
}
