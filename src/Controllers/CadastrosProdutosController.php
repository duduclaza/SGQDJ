<?php

namespace App\Controllers;

use App\Config\Database;
use App\Services\PermissionService;
use PDO;

/**
 * Controller para o Módulo Cadastros 2.0
 * Unifica cadastros de Toners, Peças e Máquinas (Impressoras)
 */
class CadastrosProdutosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Página principal - Lista todos os produtos com tabs
     */
    public function index()
    {
        // Verificar permissão
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Carregar produtos por tipo
        $tipo = $_GET['tipo'] ?? 'toner';
        $produtos = $this->getProdutosByTipo($tipo);

        // Estatísticas
        $stats = $this->getEstatisticas();

        $viewFile = __DIR__ . '/../../views/pages/cadastros-2/index.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }

    /**
     * API: Listar produtos com filtros
     */
    public function list()
    {
        header('Content-Type: application/json');

        try {
            $tipo = $_GET['tipo'] ?? '';
            $busca = $_GET['busca'] ?? '';
            
            $sql = "SELECT cp.*, u.name as criador_nome 
                    FROM cadastros_produtos cp
                    LEFT JOIN users u ON cp.created_by = u.id
                    WHERE cp.ativo = 1";
            $params = [];

            if ($tipo) {
                $sql .= " AND cp.tipo_produto = ?";
                $params[] = $tipo;
            }

            if ($busca) {
                $sql .= " AND (cp.codigo LIKE ? OR cp.modelo LIKE ? OR cp.descricao LIKE ?)";
                $params[] = "%{$busca}%";
                $params[] = "%{$busca}%";
                $params[] = "%{$busca}%";
            }

            $sql .= " ORDER BY cp.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $produtos]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Criar novo produto
     */
    public function store()
    {
        header('Content-Type: application/json');

        try {
            $tipo = $_POST['tipo_produto'] ?? '';
            
            if (!in_array($tipo, ['toner', 'peca', 'maquina'])) {
                throw new \Exception('Tipo de produto inválido');
            }

            // Campos comuns
            $codigo = trim($_POST['codigo'] ?? '');
            $modelo = trim($_POST['modelo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

            // Validação por tipo
            if ($tipo === 'toner') {
                if (empty($modelo)) {
                    throw new \Exception('Modelo é obrigatório para Toner');
                }
            } elseif ($tipo === 'peca') {
                if (empty($codigo) || empty($descricao)) {
                    throw new \Exception('Código e Descrição são obrigatórios para Peça');
                }
            } elseif ($tipo === 'maquina') {
                if (empty($modelo) || empty($codigo)) {
                    throw new \Exception('Modelo e Código são obrigatórios para Máquina');
                }
            }

            // Campos específicos de Toner
            $peso_cheio = !empty($_POST['peso_cheio']) ? (float)$_POST['peso_cheio'] : null;
            $peso_vazio = !empty($_POST['peso_vazio']) ? (float)$_POST['peso_vazio'] : null;
            $capacidade_folhas = !empty($_POST['capacidade_folhas']) ? (int)$_POST['capacidade_folhas'] : null;
            $preco = !empty($_POST['preco']) ? (float)$_POST['preco'] : null;
            $cor = $_POST['cor'] ?? null;
            $tipo_toner = $_POST['tipo_toner'] ?? null;

            // Calcular gramatura
            $gramatura = null;
            $gramatura_por_folha = null;
            $custo_por_folha = null;

            if ($peso_cheio && $peso_vazio) {
                $gramatura = $peso_cheio - $peso_vazio;
                if ($capacidade_folhas && $capacidade_folhas > 0) {
                    $gramatura_por_folha = $gramatura / $capacidade_folhas;
                }
            }

            if ($preco && $capacidade_folhas && $capacidade_folhas > 0) {
                $custo_por_folha = $preco / $capacidade_folhas;
            }

            $stmt = $this->db->prepare("
                INSERT INTO cadastros_produtos (
                    tipo_produto, codigo, modelo, descricao,
                    peso_cheio, peso_vazio, gramatura, capacidade_folhas,
                    preco, gramatura_por_folha, custo_por_folha,
                    cor, tipo_toner, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $tipo, $codigo, $modelo, $descricao,
                $peso_cheio, $peso_vazio, $gramatura, $capacidade_folhas,
                $preco, $gramatura_por_folha, $custo_por_folha,
                $cor, $tipo_toner, $_SESSION['user_id']
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Produto cadastrado com sucesso!',
                'id' => $this->db->lastInsertId()
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Atualizar produto
     */
    public function update()
    {
        header('Content-Type: application/json');

        try {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                throw new \Exception('ID do produto é obrigatório');
            }

            $tipo = $_POST['tipo_produto'] ?? '';
            $codigo = trim($_POST['codigo'] ?? '');
            $modelo = trim($_POST['modelo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

            // Campos específicos de Toner
            $peso_cheio = !empty($_POST['peso_cheio']) ? (float)$_POST['peso_cheio'] : null;
            $peso_vazio = !empty($_POST['peso_vazio']) ? (float)$_POST['peso_vazio'] : null;
            $capacidade_folhas = !empty($_POST['capacidade_folhas']) ? (int)$_POST['capacidade_folhas'] : null;
            $preco = !empty($_POST['preco']) ? (float)$_POST['preco'] : null;
            $cor = $_POST['cor'] ?? null;
            $tipo_toner = $_POST['tipo_toner'] ?? null;

            // Calcular gramatura
            $gramatura = null;
            $gramatura_por_folha = null;
            $custo_por_folha = null;

            if ($peso_cheio && $peso_vazio) {
                $gramatura = $peso_cheio - $peso_vazio;
                if ($capacidade_folhas && $capacidade_folhas > 0) {
                    $gramatura_por_folha = $gramatura / $capacidade_folhas;
                }
            }

            if ($preco && $capacidade_folhas && $capacidade_folhas > 0) {
                $custo_por_folha = $preco / $capacidade_folhas;
            }

            $stmt = $this->db->prepare("
                UPDATE cadastros_produtos SET
                    tipo_produto = ?, codigo = ?, modelo = ?, descricao = ?,
                    peso_cheio = ?, peso_vazio = ?, gramatura = ?, capacidade_folhas = ?,
                    preco = ?, gramatura_por_folha = ?, custo_por_folha = ?,
                    cor = ?, tipo_toner = ?, updated_by = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $tipo, $codigo, $modelo, $descricao,
                $peso_cheio, $peso_vazio, $gramatura, $capacidade_folhas,
                $preco, $gramatura_por_folha, $custo_por_folha,
                $cor, $tipo_toner, $_SESSION['user_id'], $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso!']);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Excluir produto (soft delete)
     */
    public function delete()
    {
        header('Content-Type: application/json');

        try {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                throw new \Exception('ID do produto é obrigatório');
            }

            $stmt = $this->db->prepare("UPDATE cadastros_produtos SET ativo = 0, updated_by = ? WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $id]);

            echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso!']);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Obter produto por ID
     */
    public function get($id)
    {
        header('Content-Type: application/json');

        try {
            $stmt = $this->db->prepare("SELECT * FROM cadastros_produtos WHERE id = ? AND ativo = 1");
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new \Exception('Produto não encontrado');
            }

            echo json_encode(['success' => true, 'data' => $produto]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Obter produtos por tipo
     */
    private function getProdutosByTipo($tipo)
    {
        $sql = "SELECT cp.*, u.name as criador_nome 
                FROM cadastros_produtos cp
                LEFT JOIN users u ON cp.created_by = u.id
                WHERE cp.ativo = 1";
        $params = [];

        if ($tipo && in_array($tipo, ['toner', 'peca', 'maquina'])) {
            $sql .= " AND cp.tipo_produto = ?";
            $params[] = $tipo;
        }

        $sql .= " ORDER BY cp.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obter estatísticas
     */
    private function getEstatisticas()
    {
        $stmt = $this->db->query("
            SELECT 
                tipo_produto,
                COUNT(*) as total
            FROM cadastros_produtos
            WHERE ativo = 1
            GROUP BY tipo_produto
        ");
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = ['toner' => 0, 'peca' => 0, 'maquina' => 0];
        foreach ($result as $row) {
            $stats[$row['tipo_produto']] = (int)$row['total'];
        }
        
        return $stats;
    }
}
