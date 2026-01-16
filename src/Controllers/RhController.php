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
                SELECT id, name, name as nome, email, setor, filial, role, status, created_at
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
            // Buscar avaliações reais do banco
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       f.titulo as formulario_titulo,
                       COALESCE(u_colab.name, a.colaborador_nome, 'Externo') as colaborador_nome,
                       COALESCE(u_aval.name, a.avaliador_nome, 'Externo') as avaliador_nome_final,
                       COALESCE(u_colab.role, 'Não informado') as cargo,
                       COALESCE(u_colab.setor, 'Não informado') as departamento
                FROM rh_avaliacoes a
                JOIN rh_formularios_avaliacao f ON a.formulario_id = f.id
                LEFT JOIN users u_colab ON a.colaborador_id = u_colab.id
                LEFT JOIN users u_aval ON a.avaliador_id = u_aval.id
                ORDER BY a.data_inicio DESC
            ");
            $stmt->execute();
            $avaliacoesDb = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $avaliacoes = [];
            foreach ($avaliacoesDb as $a) {
                $avaliacoes[] = [
                    'id' => $a['id'],
                    'formulario' => $a['formulario_titulo'],
                    'colaborador' => $a['colaborador_nome'],
                    'cargo' => $a['cargo'] ?? 'Não informado',
                    'departamento' => $a['departamento'] ?? 'Não informado',
                    'avaliador' => $a['avaliador_nome_final'],
                    'data_avaliacao' => date('Y-m-d', strtotime($a['data_inicio'])),
                    'nota_geral' => $a['nota_geral'] ? round($a['nota_geral'], 1) : null,
                    'status' => $a['status']
                ];
            }
            
            echo json_encode(['success' => true, 'avaliacoes' => $avaliacoes]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * API: Excluir avaliação
     */
    public function excluirAvaliacao()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                exit;
            }
            
            // Excluir respostas vinculada (se não tiver CASCADE no banco)
            $stmt = $this->db->prepare("DELETE FROM rh_avaliacoes_respostas WHERE avaliacao_id = ?");
            $stmt->execute([$id]);
            
            // Excluir avaliação
            $stmt = $this->db->prepare("DELETE FROM rh_avaliacoes WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Avaliação excluída com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
        }
        exit;
    }



    /**
     * Salvar resposta do formulário público
     */
    public function salvarRespostaPublica()
    {
        header('Content-Type: application/json');
        
        try {
            $token = $_POST['token'] ?? '';
            $colaboradorNome = trim($_POST['colaborador_nome'] ?? 'Não informado');
            $avaliadorNome = trim($_POST['avaliador_nome'] ?? 'Não informado');
            $respostas = json_decode($_POST['respostas'] ?? '[]', true);
            
            // Buscar formulário
            $stmt = $this->db->prepare("SELECT * FROM rh_formularios_avaliacao WHERE url_publica = ? AND ativo = 1");
            $stmt->execute([$token]);
            $formulario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$formulario) {
                echo json_encode(['success' => false, 'message' => 'Formulário inválido']);
                exit;
            }
            
            // Buscar ID do colaborador (se existir)
            $colaboradorId = null;
            if ($colaboradorNome && $colaboradorNome !== 'Não informado') {
                $stmtUser = $this->db->prepare("SELECT id FROM users WHERE name = ? LIMIT 1");
                $stmtUser->execute([$colaboradorNome]);
                $colaboradorId = $stmtUser->fetchColumn() ?: null;
            }

            // Criar avaliação salvando os nomes e IDs
            $stmt = $this->db->prepare("
                INSERT INTO rh_avaliacoes (formulario_id, colaborador_id, colaborador_nome, avaliador_id, avaliador_nome, data_inicio, status)
                VALUES (?, ?, ?, NULL, ?, NOW(), 'concluida')
            ");
            $stmt->execute([$formulario['id'], $colaboradorId, $colaboradorNome, $avaliadorNome]);
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

            // Buscar colaboradores para o select
            $stmt = $this->db->query("SELECT id, name, setor FROM users WHERE status = 'active' ORDER BY name ASC");
            $colaboradores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $title = $formulario['titulo'] . ' - Avaliação RH';
            $viewFile = __DIR__ . '/../../views/pages/rh/formulario-publico.php';
            include __DIR__ . '/../../views/layouts/public.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<h1>Erro ao carregar formulário</h1>';
        }
    }

    /**
     * API: Estatísticas para o Dashboard
     */
    public function dashboardStats()
    {
        $this->checkRhPermission();
        header('Content-Type: application/json');

        try {
            $colaboradorId = isset($_GET['colaborador_id']) && is_numeric($_GET['colaborador_id']) ? $_GET['colaborador_id'] : null;
            $whereClause = "";
            $params = [];

            if ($colaboradorId) {
                $whereClause = " AND colaborador_id = ? ";
                $params[] = $colaboradorId;
            }

            $stats = [];
            
            // KPIs
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rh_avaliacoes WHERE 1=1 $whereClause");
            $stmt->execute($params);
            $stats['total'] = $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rh_avaliacoes WHERE status = 'concluida' $whereClause");
            $stmt->execute($params);
            $stats['concluidas'] = $stmt->fetchColumn();
            
            $stats['pendentes'] = $stats['total'] - $stats['concluidas'];
            
            $stmt = $this->db->prepare("SELECT AVG(nota_geral) FROM rh_avaliacoes WHERE status = 'concluida' $whereClause");
            $stmt->execute($params);
            $media = $stmt->fetchColumn();
            $stats['media'] = $media ? number_format($media, 1) : '0.0';

            // Chart Departamentos
            $stmt = $this->db->prepare("
                SELECT COALESCE(NULLIF(u.setor, ''), 'Externo/Outros') as departamento, COUNT(*) as qtd 
                FROM rh_avaliacoes a 
                LEFT JOIN users u ON a.colaborador_id = u.id 
                WHERE 1=1 $whereClause
                GROUP BY departamento 
                ORDER BY qtd DESC 
                LIMIT 5
            ");
            $stmt->execute($params);
            $stats['departamentos'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Chart Notas
            $stmt = $this->db->prepare("
                SELECT 
                    CASE 
                        WHEN nota_geral >= 9 THEN 'Excelente (9-10)'
                        WHEN nota_geral >= 7 THEN 'Bom (7-8.9)'
                        WHEN nota_geral >= 5 THEN 'Regular (5-6.9)'
                        ELSE 'Ruim (0-4.9)'
                    END as faixa,
                    COUNT(*) as qtd
                FROM rh_avaliacoes 
                WHERE status = 'concluida' $whereClause
                GROUP BY faixa
                ORDER BY faixa DESC
            ");
            $stmt->execute($params);
            $stats['notas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Chart Evolução Mensal
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(data_conclusao, '%Y-%m') as mes, 
                    AVG(nota_geral) as media 
                FROM rh_avaliacoes 
                WHERE status = 'concluida' 
                  AND data_conclusao >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                  $whereClause
                GROUP BY mes 
                ORDER BY mes ASC
            ");
            $stmt->execute($params);
            $stats['evolucao'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

}
