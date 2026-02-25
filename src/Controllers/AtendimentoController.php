<?php

namespace App\Controllers;

use PDO;

class AtendimentoController
{
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance();
        $this->ensureConfigTable();
    }

    /**
     * Cria a tabela de configuração se não existir
     */
    private function ensureConfigTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS calculadora_toner_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                limiar_paginas INT NOT NULL DEFAULT 300,
                updated_by INT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Inserir config padrão se não existir
        $stmt = $this->db->query("SELECT COUNT(*) FROM calculadora_toner_config");
        if ((int)$stmt->fetchColumn() === 0) {
            $this->db->exec("INSERT INTO calculadora_toner_config (limiar_paginas) VALUES (300)");
        }
    }

    /**
     * Página da Calculadora de Envio de Toners
     */
    public function index(): void
    {
        // Buscar configuração atual
        $stmt = $this->db->query("SELECT limiar_paginas FROM calculadora_toner_config ORDER BY id DESC LIMIT 1");
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        $limiar = $config ? (int)$config['limiar_paginas'] : 300;

        // Verificar se é admin
        $userRole = $_SESSION['user_role'] ?? '';
        $isAdmin = in_array($userRole, ['admin', 'super_admin']);

        $data = [
            'limiar' => $limiar,
            'isAdmin' => $isAdmin,
        ];

        extract($data);
        $title = 'Calculadora de Envio de Toners - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/atendimento/calculadora-toners.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }

    /**
     * Busca toners para autocomplete (JSON)
     */
    public function buscarToners(): void
    {
        header('Content-Type: application/json');

        try {
            $busca = trim($_GET['q'] ?? '');

            if (strlen($busca) < 1) {
                echo json_encode(['success' => true, 'data' => []]);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT id, modelo, capacidade_folhas, cor, tipo
                FROM toners
                WHERE modelo LIKE :busca
                ORDER BY modelo ASC
                LIMIT 20
            ");
            $stmt->execute([':busca' => "%{$busca}%"]);
            $toners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $toners]);
        }
        catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar toners: ' . $e->getMessage()]);
        }
    }

    /**
     * Retorna configuração atual (JSON)
     */
    public function getConfig(): void
    {
        header('Content-Type: application/json');

        try {
            $stmt = $this->db->query("SELECT limiar_paginas FROM calculadora_toner_config ORDER BY id DESC LIMIT 1");
            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'limiar_paginas' => $config ? (int)$config['limiar_paginas'] : 300
            ]);
        }
        catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Salva configuração do limiar (admin only)
     */
    public function saveConfig(): void
    {
        header('Content-Type: application/json');

        try {
            // Verificar se é admin
            $userRole = $_SESSION['user_role'] ?? '';
            if (!in_array($userRole, ['admin', 'super_admin'])) {
                echo json_encode(['success' => false, 'message' => 'Apenas administradores podem alterar a configuração.']);
                return;
            }

            $limiar = (int)($_POST['limiar_paginas'] ?? 0);

            if ($limiar < 0) {
                echo json_encode(['success' => false, 'message' => 'O limiar deve ser um número positivo.']);
                return;
            }

            // Atualizar configuração existente
            $stmt = $this->db->prepare("
                UPDATE calculadora_toner_config 
                SET limiar_paginas = ?, updated_by = ?, updated_at = NOW()
                ORDER BY id DESC LIMIT 1
            ");
            $stmt->execute([$limiar, $_SESSION['user_id'] ?? null]);

            echo json_encode([
                'success' => true,
                'message' => "Limiar atualizado para {$limiar} páginas.",
                'limiar_paginas' => $limiar
            ]);
        }
        catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
        }
    }
}
