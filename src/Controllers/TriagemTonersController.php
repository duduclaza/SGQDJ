<?php

namespace App\Controllers;

use App\Config\Database;
use App\Services\PermissionService;
use PDO;

class TriagemTonersController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist(): void
    {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS triagem_toners (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    toner_id INT NOT NULL,
                    toner_modelo VARCHAR(255) NOT NULL,
                    modo ENUM('peso','percentual') NOT NULL DEFAULT 'peso',
                    peso_retornado DECIMAL(10,2) NULL,
                    percentual_informado DECIMAL(5,2) NULL,
                    gramatura_restante DECIMAL(10,2) NULL,
                    percentual_calculado DECIMAL(5,2) NOT NULL,
                    parecer TEXT NULL,
                    destino ENUM('Descarte','Garantia','Uso Interno','Estoque') NOT NULL,
                    valor_recuperado DECIMAL(10,2) NULL DEFAULT 0.00,
                    observacoes TEXT NULL,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_by INT NULL,
                    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_toner_id (toner_id),
                    INDEX idx_destino (destino),
                    INDEX idx_created_at (created_at),
                    INDEX idx_created_by (created_by)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            $this->db->exec("
                CREATE TABLE IF NOT EXISTS triagem_toners_parametros (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    percentual_min DECIMAL(5,2) NOT NULL,
                    percentual_max DECIMAL(5,2) NOT NULL,
                    parecer TEXT NOT NULL,
                    ordem INT NOT NULL DEFAULT 0,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Inserir parâmetros padrão se a tabela estiver vazia
            $count = $this->db->query("SELECT COUNT(*) FROM triagem_toners_parametros")->fetchColumn();
            if ($count == 0) {
                $this->db->exec("
                    INSERT INTO triagem_toners_parametros (percentual_min, percentual_max, parecer, ordem, created_by) VALUES
                    (0,   5,   'Descartar o toner.', 1, 0),
                    (6,   40,  'Teste o toner: se tiver com boa qualidade, use internamente ou em clientes próximos. Se estiver com má qualidade, descarte.', 2, 0),
                    (41,  80,  'Teste o toner: se tiver com boa qualidade, use internamente ou em clientes próximos. Se estiver com má qualidade, solicite garantia para o fornecedor.', 3, 0),
                    (81,  100, 'Teste o toner: se tiver com boa qualidade, envie para a logística como novo. Se estiver com má qualidade, solicite garantia para o fornecedor.', 4, 0)
                ");
            }
        } catch (\Exception $e) {
            error_log('Erro ao criar tabelas de triagem: ' . $e->getMessage());
        }
    }

    // Página principal
    public function index(): void
    {
        if (!PermissionService::hasPermission($_SESSION['user_id'], 'triagem_toners', 'view')) {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        $isAdmin  = in_array($userRole, ['admin', 'super_admin']);

        try {
            $stmt = $this->db->query("SELECT id, modelo, peso_cheio, peso_vazio, gramatura, capacidade_folhas, custo_por_folha, preco_toner FROM toners WHERE peso_cheio IS NOT NULL AND peso_vazio IS NOT NULL ORDER BY modelo");
            $toners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $toners = [];
        }

        $parametros = $this->getParametros();

        $title    = 'Triagem de Toners - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/toners/triagem.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }

    // API: Listar registros de triagem
    public function list(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission($_SESSION['user_id'], 'triagem_toners', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        try {
            $page     = max(1, (int)($_GET['page'] ?? 1));
            $perPage  = max(1, min(100, (int)($_GET['per_page'] ?? 15)));
            $offset   = ($page - 1) * $perPage;

            $where  = "WHERE 1=1";
            $params = [];

            if (!empty($_GET['toner_modelo'])) {
                $where .= " AND t.toner_modelo LIKE ?";
                $params[] = '%' . $_GET['toner_modelo'] . '%';
            }
            if (!empty($_GET['destino'])) {
                $where .= " AND t.destino = ?";
                $params[] = $_GET['destino'];
            }
            if (!empty($_GET['data_inicio'])) {
                $where .= " AND DATE(t.created_at) >= ?";
                $params[] = $_GET['data_inicio'];
            }
            if (!empty($_GET['data_fim'])) {
                $where .= " AND DATE(t.created_at) <= ?";
                $params[] = $_GET['data_fim'];
            }

            $countStmt = $this->db->prepare("SELECT COUNT(*) FROM triagem_toners t $where");
            $countStmt->execute($params);
            $total      = (int)$countStmt->fetchColumn();
            $totalPages = (int)ceil($total / $perPage);

            $stmt = $this->db->prepare("
                SELECT t.*,
                       u.name  AS criado_por_nome,
                       uu.name AS atualizado_por_nome
                FROM triagem_toners t
                LEFT JOIN users u  ON u.id  = t.created_by
                LEFT JOIN users uu ON uu.id = t.updated_by
                $where
                ORDER BY t.created_at DESC
                LIMIT $perPage OFFSET $offset
            ");
            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data'    => $registros,
                'pagination' => [
                    'page'        => $page,
                    'per_page'    => $perPage,
                    'total'       => $total,
                    'total_pages' => $totalPages,
                ],
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // API: Calcular % e parecer (chamado via AJAX antes de salvar)
    public function calcular(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        try {
            $toner_id   = (int)($_POST['toner_id'] ?? 0);
            $modo       = $_POST['modo'] ?? 'peso';
            $peso_ret   = isset($_POST['peso_retornado'])   ? (float)$_POST['peso_retornado']   : null;
            $pct_inf    = isset($_POST['percentual'])       ? (float)$_POST['percentual']       : null;

            if (!$toner_id) {
                echo json_encode(['success' => false, 'message' => 'Selecione um toner.']);
                return;
            }

            $stmt = $this->db->prepare("SELECT peso_cheio, peso_vazio, gramatura, capacidade_folhas, custo_por_folha, preco_toner FROM toners WHERE id = ?");
            $stmt->execute([$toner_id]);
            $toner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$toner) {
                echo json_encode(['success' => false, 'message' => 'Toner não encontrado.']);
                return;
            }

            $gramatura_restante  = null;
            $percentual_calculado = null;

            if ($modo === 'peso') {
                if ($peso_ret === null || $peso_ret < 0) {
                    echo json_encode(['success' => false, 'message' => 'Informe o peso retornado.']);
                    return;
                }
                $gramatura_toner    = (float)($toner['gramatura'] ?: ($toner['peso_cheio'] - $toner['peso_vazio']));
                $gramatura_restante = max(0, $peso_ret - (float)$toner['peso_vazio']);
                $percentual_calculado = $gramatura_toner > 0
                    ? min(100, max(0, round(($gramatura_restante / $gramatura_toner) * 100, 2)))
                    : 0;
            } else {
                if ($pct_inf === null || $pct_inf < 0) {
                    echo json_encode(['success' => false, 'message' => 'Informe o percentual.']);
                    return;
                }
                $percentual_calculado = min(100, max(0, round($pct_inf, 2)));
                $gramatura_toner      = (float)($toner['gramatura'] ?: ($toner['peso_cheio'] - $toner['peso_vazio']));
                if ($gramatura_toner > 0) {
                    $gramatura_restante = round(($percentual_calculado / 100) * $gramatura_toner, 2);
                }
            }

            $parecer = $this->getParecer($percentual_calculado);

            // Calcular valor recuperado se destino for estoque
            $valor_recuperado = 0;
            $capacidade       = (float)($toner['capacidade_folhas'] ?? 0);
            $custo_folha      = (float)($toner['custo_por_folha']   ?? 0);
            if ($capacidade > 0 && $custo_folha > 0 && $percentual_calculado > 0) {
                $folhas_restantes = ($percentual_calculado / 100) * $capacidade;
                $valor_recuperado = round($folhas_restantes * $custo_folha, 2);
            }

            echo json_encode([
                'success'              => true,
                'percentual_calculado' => $percentual_calculado,
                'gramatura_restante'   => $gramatura_restante,
                'parecer'              => $parecer,
                'valor_recuperado'     => $valor_recuperado,
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Salvar registro de triagem
    public function store(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission($_SESSION['user_id'], 'triagem_toners', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para registrar triagem.']);
            return;
        }

        try {
            $toner_id    = (int)($_POST['toner_id'] ?? 0);
            $modo        = $_POST['modo'] ?? 'peso';
            $peso_ret    = isset($_POST['peso_retornado'])  && $_POST['peso_retornado'] !== '' ? (float)$_POST['peso_retornado'] : null;
            $pct_inf     = isset($_POST['percentual'])      && $_POST['percentual'] !== ''     ? (float)$_POST['percentual']     : null;
            $destino     = $_POST['destino']     ?? '';
            $observacoes = $_POST['observacoes'] ?? null;

            if (!$toner_id || !$destino) {
                echo json_encode(['success' => false, 'message' => 'Toner e destino são obrigatórios.']);
                return;
            }

            $stmt = $this->db->prepare("SELECT modelo, peso_cheio, peso_vazio, gramatura, capacidade_folhas, custo_por_folha FROM toners WHERE id = ?");
            $stmt->execute([$toner_id]);
            $toner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$toner) {
                echo json_encode(['success' => false, 'message' => 'Toner não encontrado.']);
                return;
            }

            $gramatura_restante   = null;
            $percentual_calculado = 0;
            $gramatura_toner      = (float)($toner['gramatura'] ?: ($toner['peso_cheio'] - $toner['peso_vazio']));

            if ($modo === 'peso' && $peso_ret !== null) {
                $gramatura_restante   = max(0, $peso_ret - (float)$toner['peso_vazio']);
                $percentual_calculado = $gramatura_toner > 0
                    ? min(100, max(0, round(($gramatura_restante / $gramatura_toner) * 100, 2))) : 0;
            } elseif ($modo === 'percentual' && $pct_inf !== null) {
                $percentual_calculado = min(100, max(0, round($pct_inf, 2)));
                if ($gramatura_toner > 0) {
                    $gramatura_restante = round(($percentual_calculado / 100) * $gramatura_toner, 2);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Informe o peso ou o percentual.']);
                return;
            }

            $parecer = $this->getParecer($percentual_calculado);

            $valor_recuperado = 0;
            if ($destino === 'Estoque') {
                $capacidade  = (float)($toner['capacidade_folhas'] ?? 0);
                $custo_folha = (float)($toner['custo_por_folha']   ?? 0);
                if ($capacidade > 0 && $custo_folha > 0) {
                    $folhas_restantes = ($percentual_calculado / 100) * $capacidade;
                    $valor_recuperado = round($folhas_restantes * $custo_folha, 2);
                }
            }

            $insert = $this->db->prepare("
                INSERT INTO triagem_toners
                    (toner_id, toner_modelo, modo, peso_retornado, percentual_informado,
                     gramatura_restante, percentual_calculado, parecer, destino,
                     valor_recuperado, observacoes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->execute([
                $toner_id,
                $toner['modelo'],
                $modo,
                $peso_ret,
                $pct_inf,
                $gramatura_restante,
                $percentual_calculado,
                $parecer,
                $destino,
                $valor_recuperado,
                $observacoes,
                $_SESSION['user_id'],
            ]);

            echo json_encode(['success' => true, 'message' => 'Triagem registrada com sucesso!', 'id' => $this->db->lastInsertId()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Atualizar registro
    public function update(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission($_SESSION['user_id'], 'triagem_toners', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para editar.']);
            return;
        }

        try {
            $id          = (int)($_POST['id'] ?? 0);
            $toner_id    = (int)($_POST['toner_id'] ?? 0);
            $modo        = $_POST['modo'] ?? 'peso';
            $peso_ret    = isset($_POST['peso_retornado']) && $_POST['peso_retornado'] !== '' ? (float)$_POST['peso_retornado'] : null;
            $pct_inf     = isset($_POST['percentual'])     && $_POST['percentual'] !== ''     ? (float)$_POST['percentual']     : null;
            $destino     = $_POST['destino']     ?? '';
            $observacoes = $_POST['observacoes'] ?? null;

            if (!$id || !$toner_id || !$destino) {
                echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
                return;
            }

            $stmt = $this->db->prepare("SELECT modelo, peso_cheio, peso_vazio, gramatura, capacidade_folhas, custo_por_folha FROM toners WHERE id = ?");
            $stmt->execute([$toner_id]);
            $toner = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$toner) {
                echo json_encode(['success' => false, 'message' => 'Toner não encontrado.']);
                return;
            }

            $gramatura_toner      = (float)($toner['gramatura'] ?: ($toner['peso_cheio'] - $toner['peso_vazio']));
            $gramatura_restante   = null;
            $percentual_calculado = 0;

            if ($modo === 'peso' && $peso_ret !== null) {
                $gramatura_restante   = max(0, $peso_ret - (float)$toner['peso_vazio']);
                $percentual_calculado = $gramatura_toner > 0
                    ? min(100, max(0, round(($gramatura_restante / $gramatura_toner) * 100, 2))) : 0;
            } elseif ($modo === 'percentual' && $pct_inf !== null) {
                $percentual_calculado = min(100, max(0, round($pct_inf, 2)));
                if ($gramatura_toner > 0) {
                    $gramatura_restante = round(($percentual_calculado / 100) * $gramatura_toner, 2);
                }
            }

            $parecer = $this->getParecer($percentual_calculado);

            $valor_recuperado = 0;
            if ($destino === 'Estoque') {
                $capacidade  = (float)($toner['capacidade_folhas'] ?? 0);
                $custo_folha = (float)($toner['custo_por_folha']   ?? 0);
                if ($capacidade > 0 && $custo_folha > 0) {
                    $valor_recuperado = round((($percentual_calculado / 100) * $capacidade) * $custo_folha, 2);
                }
            }

            $upd = $this->db->prepare("
                UPDATE triagem_toners SET
                    toner_id = ?, toner_modelo = ?, modo = ?,
                    peso_retornado = ?, percentual_informado = ?,
                    gramatura_restante = ?, percentual_calculado = ?,
                    parecer = ?, destino = ?, valor_recuperado = ?,
                    observacoes = ?, updated_by = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $upd->execute([
                $toner_id, $toner['modelo'], $modo,
                $peso_ret, $pct_inf,
                $gramatura_restante, $percentual_calculado,
                $parecer, $destino, $valor_recuperado,
                $observacoes, $_SESSION['user_id'], $id,
            ]);

            echo json_encode(['success' => true, 'message' => 'Registro atualizado com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Excluir registro
    public function delete(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission($_SESSION['user_id'], 'triagem_toners', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para excluir.']);
            return;
        }

        try {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }
            $this->db->prepare("DELETE FROM triagem_toners WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Registro excluído com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== PARÂMETROS =====

    public function getParametrosApi(): void
    {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $this->getParametros()]);
    }

    public function saveParametros(): void
    {
        ob_clean();
        header('Content-Type: application/json');

        $userRole = $_SESSION['user_role'] ?? '';
        if (!in_array($userRole, ['admin', 'super_admin'])) {
            echo json_encode(['success' => false, 'message' => 'Apenas admin pode alterar parâmetros.']);
            return;
        }

        try {
            $parametros = json_decode(file_get_contents('php://input'), true);
            if (!is_array($parametros) || empty($parametros)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
                return;
            }

            $this->db->exec("DELETE FROM triagem_toners_parametros");
            $stmt = $this->db->prepare("
                INSERT INTO triagem_toners_parametros (percentual_min, percentual_max, parecer, ordem, created_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($parametros as $i => $p) {
                $stmt->execute([
                    (float)$p['percentual_min'],
                    (float)$p['percentual_max'],
                    trim($p['parecer']),
                    $i + 1,
                    $_SESSION['user_id'],
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Parâmetros salvos com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== HELPERS =====

    private function getParametros(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM triagem_toners_parametros ORDER BY ordem ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getParecer(float $percentual): string
    {
        $parametros = $this->getParametros();
        foreach ($parametros as $p) {
            if ($percentual >= (float)$p['percentual_min'] && $percentual <= (float)$p['percentual_max']) {
                return $p['parecer'];
            }
        }
        return 'Sem parecer definido para este percentual. Verifique os parâmetros de triagem.';
    }
}
