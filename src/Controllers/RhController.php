<?php

namespace App\Controllers;

use App\Services\PermissionService;
use App\Config\Database;

class RhController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
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
    
    /**
     * API: Listar formulários de avaliação
     */
    public function listarFormularios()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->db->prepare("
                SELECT f.*, u.name as criado_por_nome,
                       (SELECT COUNT(*) FROM rh_formularios_perguntas WHERE formulario_id = f.id) as total_perguntas,
                       (SELECT COUNT(*) FROM rh_avaliacoes WHERE formulario_id = f.id) as total_avaliacoes
                FROM rh_formularios_avaliacao f
                LEFT JOIN users u ON f.criado_por = u.id
                ORDER BY f.criado_em DESC
            ");
            $stmt->execute();
            $formularios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'formularios' => $formularios]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Obter detalhes de um formulário
     */
    public function detalhesFormulario($id)
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            // Buscar formulário
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_avaliacao WHERE id = ?");
            $stmt->execute([$id]);
            $formulario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$formulario) {
                echo json_encode(['success' => false, 'message' => 'Formulário não encontrado']);
                exit;
            }
            
            // Buscar perguntas
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_perguntas WHERE formulario_id = ? ORDER BY ordem ASC");
            $stmt->execute([$id]);
            $formulario['perguntas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'formulario' => $formulario]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Criar formulário de avaliação
     */
    public function criarFormulario()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $tipo = $_POST['tipo'] ?? 'desempenho';
            $perguntas = json_decode($_POST['perguntas'] ?? '[]', true);
            
            if (empty($titulo)) {
                echo json_encode(['success' => false, 'message' => 'Título é obrigatório']);
                exit;
            }
            
            if (empty($perguntas)) {
                echo json_encode(['success' => false, 'message' => 'Adicione pelo menos uma pergunta']);
                exit;
            }
            
            // Gerar URL pública única
            $urlPublica = $this->gerarUrlPublica();
            
            // Inserir formulário
            $stmt = $this->db->prepare("
                INSERT INTO rh_formularios_avaliacao (titulo, descricao, tipo, url_publica, criado_por, criado_em)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$titulo, $descricao, $tipo, $urlPublica, $_SESSION['user_id']]);
            $formularioId = $this->db->lastInsertId();
            
            // Inserir perguntas
            $stmtPergunta = $this->db->prepare("
                INSERT INTO rh_formularios_perguntas (formulario_id, texto, tipo, opcoes, obrigatoria, ordem, peso)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($perguntas as $index => $p) {
                $opcoes = isset($p['opcoes']) ? json_encode($p['opcoes']) : null;
                $stmtPergunta->execute([
                    $formularioId,
                    $p['texto'],
                    $p['tipo'] ?? 'texto',
                    $opcoes,
                    $p['obrigatoria'] ?? 1,
                    $index,
                    $p['peso'] ?? 1.00
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Formulário criado com sucesso!', 'id' => $formularioId]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Editar formulário de avaliação
     */
    public function editarFormulario()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? 0;
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $tipo = $_POST['tipo'] ?? 'desempenho';
            $perguntas = json_decode($_POST['perguntas'] ?? '[]', true);
            
            if (empty($id) || empty($titulo)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                exit;
            }
            
            // Verificar se tem avaliações
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rh_avaliacoes WHERE formulario_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Não é possível editar formulário com avaliações vinculadas']);
                exit;
            }
            
            // Atualizar formulário
            $stmt = $this->db->prepare("
                UPDATE rh_formularios_avaliacao 
                SET titulo = ?, descricao = ?, tipo = ?, atualizado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$titulo, $descricao, $tipo, $id]);
            
            // Remover perguntas antigas e inserir novas
            $stmt = $this->db->prepare("DELETE FROM rh_formularios_perguntas WHERE formulario_id = ?");
            $stmt->execute([$id]);
            
            $stmtPergunta = $this->db->prepare("
                INSERT INTO rh_formularios_perguntas (formulario_id, texto, tipo, opcoes, obrigatoria, ordem, peso)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($perguntas as $index => $p) {
                $opcoes = isset($p['opcoes']) ? json_encode($p['opcoes']) : null;
                $stmtPergunta->execute([
                    $id,
                    $p['texto'],
                    $p['tipo'] ?? 'texto',
                    $opcoes,
                    $p['obrigatoria'] ?? 1,
                    $index,
                    $p['peso'] ?? 1.00
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Formulário atualizado!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Excluir formulário
     */
    public function excluirFormulario()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? 0;
            
            // Verificar se tem avaliações
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rh_avaliacoes WHERE formulario_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Não é possível excluir formulário com avaliações vinculadas']);
                exit;
            }
            
            // Excluir (perguntas são excluídas em cascata)
            $stmt = $this->db->prepare("DELETE FROM rh_formularios_avaliacao WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Formulário excluído!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Gerar URL pública única
     */
    private function gerarUrlPublica()
    {
        do {
            $token = 'rh-' . bin2hex(random_bytes(8));
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rh_formularios_avaliacao WHERE url_publica = ?");
            $stmt->execute([$token]);
        } while ($stmt->fetchColumn() > 0);
        
        return $token;
    }
    
    /**
     * API: Duplicar formulário
     */
    public function duplicarFormulario()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? 0;
            
            // Buscar formulário original
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_avaliacao WHERE id = ?");
            $stmt->execute([$id]);
            $original = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$original) {
                echo json_encode(['success' => false, 'message' => 'Formulário não encontrado']);
                exit;
            }
            
            // Gerar nova URL pública
            $urlPublica = $this->gerarUrlPublica();
            
            // Criar cópia do formulário
            $stmt = $this->db->prepare("
                INSERT INTO rh_formularios_avaliacao (titulo, descricao, tipo, url_publica, ativo, criado_por, criado_em)
                VALUES (?, ?, ?, ?, 1, ?, NOW())
            ");
            $stmt->execute([
                $original['titulo'] . ' (Cópia)',
                $original['descricao'],
                $original['tipo'],
                $urlPublica,
                $_SESSION['user_id']
            ]);
            $novoId = $this->db->lastInsertId();
            
            // Copiar perguntas
            $stmtPerguntas = $this->db->prepare("SELECT * FROM rh_formularios_perguntas WHERE formulario_id = ? ORDER BY ordem");
            $stmtPerguntas->execute([$id]);
            $perguntas = $stmtPerguntas->fetchAll(\PDO::FETCH_ASSOC);
            
            $stmtInsert = $this->db->prepare("
                INSERT INTO rh_formularios_perguntas (formulario_id, texto, tipo, opcoes, obrigatoria, ordem, peso)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($perguntas as $p) {
                $stmtInsert->execute([
                    $novoId,
                    $p['texto'],
                    $p['tipo'],
                    $p['opcoes'],
                    $p['obrigatoria'],
                    $p['ordem'],
                    $p['peso']
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Formulário duplicado com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Página pública do formulário de avaliação
     */
    public function formularioPublico($token)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_avaliacao WHERE url_publica = ? AND ativo = 1");
            $stmt->execute([$token]);
            $formulario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$formulario) {
                http_response_code(404);
                echo '<h1>Formulário não encontrado ou inativo</h1>';
                exit;
            }
            
            // Buscar perguntas
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_perguntas WHERE formulario_id = ? ORDER BY ordem");
            $stmt->execute([$formulario['id']]);
            $formulario['perguntas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $title = $formulario['titulo'] . ' - Avaliação RH';
            $viewFile = __DIR__ . '/../../views/pages/rh/formulario-publico.php';
            include __DIR__ . '/../../views/layouts/public.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Erro ao carregar formulário</h1>';
        }
    }
    
    /**
     * Salvar resposta do formulário público
     */
    public function salvarRespostaPublica()
    {
        header('Content-Type: application/json');
        
        try {
            $token = $_POST['token'] ?? '';
            $colaboradorNome = trim($_POST['colaborador_nome'] ?? '');
            $avaliadorNome = trim($_POST['avaliador_nome'] ?? '');
            $respostas = json_decode($_POST['respostas'] ?? '[]', true);
            
            // Buscar formulário
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_avaliacao WHERE url_publica = ? AND ativo = 1");
            $stmt->execute([$token]);
            $formulario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$formulario) {
                echo json_encode(['success' => false, 'message' => 'Formulário inválido']);
                exit;
            }
            
            // Criar avaliação
            $stmt = $this->db->prepare("
                INSERT INTO rh_avaliacoes (formulario_id, colaborador_id, avaliador_id, data_inicio, status)
                VALUES (?, 0, 0, NOW(), 'concluida')
            ");
            $stmt->execute([$formulario['id']]);
            $avaliacaoId = $this->db->lastInsertId();
            
            // Salvar respostas
            $totalNota = 0;
            $countNotas = 0;
            
            $stmtResp = $this->db->prepare("
                INSERT INTO rh_avaliacoes_respostas (avaliacao_id, pergunta_id, resposta, nota, respondido_em)
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            foreach ($respostas as $r) {
                $nota = isset($r['nota']) && is_numeric($r['nota']) ? floatval($r['nota']) : null;
                $stmtResp->execute([$avaliacaoId, $r['pergunta_id'], $r['resposta'], $nota]);
                
                if ($nota !== null) {
                    $totalNota += $nota;
                    $countNotas++;
                }
            }
            
            // Atualizar nota geral
            if ($countNotas > 0) {
                $notaGeral = $totalNota / $countNotas;
                $stmt = $this->db->prepare("UPDATE rh_avaliacoes SET nota_geral = ?, data_conclusao = NOW() WHERE id = ?");
                $stmt->execute([$notaGeral, $avaliacaoId]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Avaliação enviada com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
