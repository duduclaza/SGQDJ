<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;

class MonitoramentoPortalController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Página de login do portal
     */
    public function login()
    {
        // Se já estiver logado, redireciona para dashboard
        if (isset($_SESSION['portal_cliente_id'])) {
            header('Location: /portal/dashboard');
            exit;
        }

        $title = 'Portal do Cliente - Login';
        $viewFile = __DIR__ . '/../../views/monitoramento/portal/login.php';
        include __DIR__ . '/../../views/layouts/portal_layout.php';
    }

    /**
     * Processar login do portal
     */
    public function processarLogin()
    {
        ob_start();
        header('Content-Type: application/json');

        try {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (empty($email) || empty($senha)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
                exit;
            }

            // Buscar cliente
            $stmt = $this->db->prepare("
                SELECT * FROM monitoramento_clientes 
                WHERE email = ? AND ativo = 1
            ");
            $stmt->execute([$email]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos']);
                exit;
            }

            // Verificar senha
            if (!password_verify($senha, $cliente['senha_hash'])) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos']);
                exit;
            }

            // Login bem-sucedido
            $_SESSION['portal_cliente_id'] = $cliente['id'];
            $_SESSION['portal_cliente_nome'] = $cliente['nome'];
            $_SESSION['portal_cliente_email'] = $cliente['email'];
            $_SESSION['portal_senha_temporaria'] = $cliente['senha_temporaria'];

            // Log de acesso
            $this->registrarLog($cliente['id'], null, 'login', 'Login realizado com sucesso');

            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'senha_temporaria' => (bool)$cliente['senha_temporaria']
            ]);

        } catch (\Exception $e) {
            error_log("Erro no login do portal: " . $e->getMessage());
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Erro ao processar login']);
        }
        exit;
    }

    /**
     * Dashboard do portal
     */
    public function dashboard()
    {
        $this->verificarAutenticacao();

        $clienteId = $_SESSION['portal_cliente_id'];

        // Buscar dados do cliente
        $stmt = $this->db->prepare("SELECT * FROM monitoramento_clientes WHERE id = ?");
        $stmt->execute([$clienteId]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Buscar impressoras
        $stmt = $this->db->prepare("
            SELECT * FROM monitoramento_impressoras 
            WHERE cliente_id = ? AND ativa = 1
            ORDER BY modelo
        ");
        $stmt->execute([$clienteId]);
        $impressoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar alertas não lidos
        $stmt = $this->db->prepare("
            SELECT a.*, i.modelo, i.numero_serie
            FROM monitoramento_alertas a
            INNER JOIN monitoramento_impressoras i ON a.impressora_id = i.id
            WHERE i.cliente_id = ? AND a.lido = 0
            ORDER BY a.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$clienteId]);
        $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Estatísticas
        $totalImpressoras = count($impressoras);
        $impressorasOnline = count(array_filter($impressoras, fn($i) => $i['status_conexao'] === 'online'));
        $totalAlertas = count($alertas);

        $title = 'Dashboard - Portal do Cliente';
        $viewFile = __DIR__ . '/../../views/monitoramento/portal/dashboard.php';
        include __DIR__ . '/../../views/layouts/portal_layout.php';
    }

    /**
     * Trocar senha (obrigatório no primeiro acesso)
     */
    public function trocarSenha()
    {
        ob_start();
        header('Content-Type: application/json');

        try {
            $this->verificarAutenticacao();

            $senhaAtual = $_POST['senha_atual'] ?? '';
            $senhaNova = $_POST['senha_nova'] ?? '';
            $senhaConfirma = $_POST['senha_confirma'] ?? '';

            if (empty($senhaAtual) || empty($senhaNova) || empty($senhaConfirma)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
                exit;
            }

            if ($senhaNova !== $senhaConfirma) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'As senhas não conferem']);
                exit;
            }

            if (strlen($senhaNova) < 6) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres']);
                exit;
            }

            $clienteId = $_SESSION['portal_cliente_id'];

            // Verificar senha atual
            $stmt = $this->db->prepare("SELECT senha_hash FROM monitoramento_clientes WHERE id = ?");
            $stmt->execute([$clienteId]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($senhaAtual, $cliente['senha_hash'])) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
                exit;
            }

            // Atualizar senha
            $senhaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE monitoramento_clientes 
                SET senha_hash = ?, senha_temporaria = 0, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$senhaHash, $clienteId]);

            $_SESSION['portal_senha_temporaria'] = false;

            $this->registrarLog($clienteId, null, 'troca_senha', 'Senha alterada com sucesso');

            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);

        } catch (\Exception $e) {
            error_log("Erro ao trocar senha: " . $e->getMessage());
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Erro ao trocar senha']);
        }
        exit;
    }

    /**
     * Logout do portal
     */
    public function logout()
    {
        if (isset($_SESSION['portal_cliente_id'])) {
            $this->registrarLog($_SESSION['portal_cliente_id'], null, 'logout', 'Logout realizado');
        }

        unset($_SESSION['portal_cliente_id']);
        unset($_SESSION['portal_cliente_nome']);
        unset($_SESSION['portal_cliente_email']);
        unset($_SESSION['portal_senha_temporaria']);

        header('Location: /portal/login');
        exit;
    }

    /**
     * Verificar autenticação
     */
    private function verificarAutenticacao()
    {
        if (!isset($_SESSION['portal_cliente_id'])) {
            header('Location: /portal/login');
            exit;
        }
    }

    /**
     * Registrar log de ação
     */
    private function registrarLog($clienteId, $impressoraId, $acao, $detalhes)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO monitoramento_logs 
                (cliente_id, impressora_id, acao, detalhes, ip_origem, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $clienteId,
                $impressoraId,
                $acao,
                $detalhes,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
}
