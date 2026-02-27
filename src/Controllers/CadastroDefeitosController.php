<?php

namespace App\Controllers;

use App\Config\Database;
use App\Services\PermissionService;
use PDO;

class CadastroDefeitosController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS cadastro_defeitos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome_defeito VARCHAR(255) NOT NULL,
                    imagem LONGBLOB NULL,
                    imagem_nome VARCHAR(255) NULL,
                    imagem_tipo VARCHAR(100) NULL,
                    created_by INT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_nome_defeito (nome_defeito)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (\Exception $e) {
            error_log('Erro ao garantir tabela cadastro_defeitos: ' . $e->getMessage());
        }
    }

    public function index(): void
    {
        if (!PermissionService::hasPermission((int)($_SESSION['user_id'] ?? 0), 'cadastro_defeitos', 'view')) {
            http_response_code(403);
            echo 'Acesso negado';
            return;
        }

        $stmt = $this->db->query(
            "SELECT d.*, u.name AS criado_por_nome
             FROM cadastro_defeitos d
             LEFT JOIN users u ON u.id = d.created_by
             ORDER BY d.created_at DESC"
        );
        $defeitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = 'Cadastro de Defeitos - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/pages/cadastro-defeitos/index.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission((int)($_SESSION['user_id'] ?? 0), 'cadastro_defeitos', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para cadastrar defeitos.']);
            return;
        }

        try {
            $nome = trim($_POST['nome_defeito'] ?? '');
            if ($nome === '') {
                echo json_encode(['success' => false, 'message' => 'Informe o nome do defeito.']);
                return;
            }

            if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'A imagem do defeito é obrigatória.']);
                return;
            }

            $mime = mime_content_type($_FILES['imagem']['tmp_name']);
            if (!is_string($mime) || !str_starts_with($mime, 'image/')) {
                echo json_encode(['success' => false, 'message' => 'Arquivo inválido. Envie uma imagem.']);
                return;
            }

            $stmt = $this->db->prepare(
                "INSERT INTO cadastro_defeitos (nome_defeito, imagem, imagem_nome, imagem_tipo, created_by)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bindValue(1, $nome);
            $stmt->bindValue(2, file_get_contents($_FILES['imagem']['tmp_name']), PDO::PARAM_LOB);
            $stmt->bindValue(3, basename($_FILES['imagem']['name']));
            $stmt->bindValue(4, $mime);
            $stmt->bindValue(5, (int)($_SESSION['user_id'] ?? 0), PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Defeito cadastrado com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
        }
    }

    public function update(): void
    {
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission((int)($_SESSION['user_id'] ?? 0), 'cadastro_defeitos', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para editar defeitos.']);
            return;
        }

        try {
            $id = (int)($_POST['id'] ?? 0);
            $nome = trim($_POST['nome_defeito'] ?? '');

            if ($id <= 0 || $nome === '') {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
                return;
            }

            $imagemSql = '';
            $params = [$nome];

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $mime = mime_content_type($_FILES['imagem']['tmp_name']);
                if (!is_string($mime) || !str_starts_with($mime, 'image/')) {
                    echo json_encode(['success' => false, 'message' => 'Arquivo inválido. Envie uma imagem.']);
                    return;
                }
                $imagemSql = ', imagem = ?, imagem_nome = ?, imagem_tipo = ?';
                $params[] = file_get_contents($_FILES['imagem']['tmp_name']);
                $params[] = basename($_FILES['imagem']['name']);
                $params[] = $mime;
            }

            $params[] = $id;

            $stmt = $this->db->prepare(
                "UPDATE cadastro_defeitos
                 SET nome_defeito = ? {$imagemSql}, updated_at = NOW()
                 WHERE id = ?"
            );
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Defeito atualizado com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }

    public function delete(): void
    {
        header('Content-Type: application/json');

        if (!PermissionService::hasPermission((int)($_SESSION['user_id'] ?? 0), 'cadastro_defeitos', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para excluir defeitos.']);
            return;
        }

        try {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }

            $stmt = $this->db->prepare('DELETE FROM cadastro_defeitos WHERE id = ?');
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Defeito excluído com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
        }
    }

    public function imagem(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $parts = explode('/', trim((string)$path, '/'));
        $id = (int)($parts[1] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido';
            return;
        }

        try {
            $stmt = $this->db->prepare('SELECT imagem, imagem_nome, imagem_tipo FROM cadastro_defeitos WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || empty($row['imagem'])) {
                http_response_code(404);
                echo 'Imagem não encontrada';
                return;
            }

            header('Content-Type: ' . ($row['imagem_tipo'] ?: 'image/jpeg'));
            header('Content-Disposition: inline; filename="' . ($row['imagem_nome'] ?: 'defeito.jpg') . '"');
            echo $row['imagem'];
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Erro ao carregar imagem';
        }
    }
}
