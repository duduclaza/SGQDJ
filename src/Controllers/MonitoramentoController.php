<?php

namespace App\Controllers;

use App\Config\Database;
use App\Services\EmailService;
use PDO;

class MonitoramentoController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Página principal - Gerenciar Clientes
     */
    public function index()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Buscar todos os clientes
        $stmt = $this->db->query("
            SELECT 
                c.*,
                COUNT(DISTINCT i.id) as total_impressoras,
                COUNT(DISTINCT CASE WHEN a.lido = 0 THEN a.id END) as alertas_nao_lidos
            FROM monitoramento_clientes c
            LEFT JOIN monitoramento_impressoras i ON c.id = i.cliente_id AND i.ativa = 1
            LEFT JOIN monitoramento_alertas a ON i.id = a.impressora_id
            GROUP BY c.id
            ORDER BY c.nome
        ");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar licença
        $stmt = $this->db->query("SELECT * FROM monitoramento_licencas LIMIT 1");
        $licenca = $stmt->fetch(PDO::FETCH_ASSOC);

        // Contar clientes ativos
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM monitoramento_clientes WHERE ativo = 1");
        $clientesAtivos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $title = 'Monitoramento - Portal de Clientes';
        $viewFile = __DIR__ . '/../../views/monitoramento/index.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }

    /**
     * Criar novo cliente
     */
    public function criarCliente()
    {
        ob_start();
        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION['user_id'])) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Não autenticado']);
                exit;
            }

            // Validar campos
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $cnpj = trim($_POST['cnpj'] ?? '');

            if (empty($nome) || empty($email)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Nome e email são obrigatórios']);
                exit;
            }

            // Verificar limite de clientes
            $stmt = $this->db->query("SELECT total_clientes_permitidos FROM monitoramento_licencas LIMIT 1");
            $licenca = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM monitoramento_clientes WHERE ativo = 1");
            $clientesAtivos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            if ($clientesAtivos >= $licenca['total_clientes_permitidos']) {
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'Limite de clientes atingido. Entre em contato para adicionar mais clientes.'
                ]);
                exit;
            }

            // Verificar se email já existe
            $stmt = $this->db->prepare("SELECT id FROM monitoramento_clientes WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Email já cadastrado']);
                exit;
            }

            // Gerar token único
            $token = bin2hex(random_bytes(32));

            // Senha padrão
            $senhaTemporaria = 'mudar@123';
            $senhaHash = password_hash($senhaTemporaria, PASSWORD_DEFAULT);

            // Inserir cliente
            $stmt = $this->db->prepare("
                INSERT INTO monitoramento_clientes 
                (nome, email, telefone, cnpj, portal_token, senha_hash, senha_temporaria, ativo, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW())
            ");
            $stmt->execute([$nome, $email, $telefone, $cnpj, $token, $senhaHash]);
            $clienteId = $this->db->lastInsertId();

            // Enviar email com credenciais
            $this->enviarEmailCredenciais($clienteId, $email, $senhaTemporaria, $token);

            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Cliente criado com sucesso! Email com credenciais enviado.',
                'cliente_id' => $clienteId
            ]);

        } catch (\Exception $e) {
            error_log("Erro ao criar cliente: " . $e->getMessage());
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Erro ao criar cliente: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Enviar email com credenciais de acesso
     */
    private function enviarEmailCredenciais($clienteId, $email, $senha, $token)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM monitoramento_clientes WHERE id = ?");
            $stmt->execute([$clienteId]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            $linkPortal = "https://djbr.sgqoti.com.br/portal?token=" . $token;

            $assunto = "Bem-vindo ao Portal de Monitoramento - SGQ OTI DJ";
            
            $mensagem = "
                <h2>Olá, {$cliente['nome']}!</h2>
                <p>Seu portal de monitoramento de impressoras foi criado com sucesso!</p>
                
                <h3>📋 Dados de Acesso:</h3>
                <ul>
                    <li><strong>Link do Portal:</strong> <a href='{$linkPortal}'>{$linkPortal}</a></li>
                    <li><strong>Usuário:</strong> {$email}</li>
                    <li><strong>Senha Temporária:</strong> {$senha}</li>
                </ul>
                
                <p><strong>⚠️ IMPORTANTE:</strong> Por segurança, você será solicitado a alterar sua senha no primeiro acesso.</p>
                
                <h3>🖨️ O que você pode fazer no portal:</h3>
                <ul>
                    <li>Cadastrar suas impressoras</li>
                    <li>Monitorar níveis de suprimentos (toner)</li>
                    <li>Visualizar contadores de páginas</li>
                    <li>Receber alertas de toner baixo</li>
                    <li>Gerar relatórios</li>
                </ul>
                
                <p>Se tiver dúvidas, entre em contato conosco!</p>
                
                <p>Atenciosamente,<br>Equipe SGQ OTI DJ</p>
            ";

            EmailService::enviar($email, $assunto, $mensagem);

        } catch (\Exception $e) {
            error_log("Erro ao enviar email de credenciais: " . $e->getMessage());
        }
    }
}
