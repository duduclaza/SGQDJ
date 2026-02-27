<?php

namespace App\Controllers;

use App\Config\Database;

class ChatController
{
    private const AI_BOT_ID = -1000;
    private const AI_BOT_NAME = 'Daniel do Suporte';
    private const AI_BOT_EMAIL = 'daniel.suporte.ai@sgq.local';
    private const AI_BOT_AVATAR_URL = '/assets/daniel-suporte.svg';

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureChatTables();
        $this->purgeOldMessages();
    }

    private function ensureChatTables(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS chat_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            message TEXT NOT NULL,
            payload_json LONGTEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_chat_pair (sender_id, receiver_id),
            INDEX idx_chat_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        try {
            $checkColumn = $this->db->query("SHOW COLUMNS FROM chat_messages LIKE 'payload_json'");
            if (!$checkColumn || $checkColumn->rowCount() === 0) {
                $this->db->exec("ALTER TABLE chat_messages ADD COLUMN payload_json LONGTEXT NULL AFTER message");
            }
        } catch (\Throwable $e) {
            // manter compatibilidade sem interromper o chat
        }

        $this->db->exec("CREATE TABLE IF NOT EXISTS chat_user_presence (
            user_id INT PRIMARY KEY,
            last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_presence_last_seen (last_seen)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function purgeOldMessages(): void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM chat_messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->execute();
        } catch (\Throwable $e) {
            // não bloquear o uso do chat por falha de limpeza
        }
    }

    private function requireAuthJson(): ?int
    {
        header('Content-Type: application/json');

        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        if ($userId <= 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
            return null;
        }

        return $userId;
    }

    public function heartbeat(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO chat_user_presence (user_id, last_seen)
                VALUES (?, NOW())
                ON DUPLICATE KEY UPDATE last_seen = NOW()");
            $stmt->execute([$userId]);

            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar presença']);
        }
    }

    public function contacts(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        try {
            $this->touchPresence($userId);

            $sql = "SELECT
                        u.id,
                        u.name,
                        u.email,
                        CASE WHEN u.profile_photo IS NOT NULL THEN 1 ELSE 0 END AS has_photo,
                        CASE
                            WHEN p.last_seen IS NOT NULL AND p.last_seen >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)
                            THEN 1
                            ELSE 0
                        END AS is_online,
                        COALESCE(unread.total_unread, 0) AS unread_count,
                        p.last_seen
                    FROM users u
                    LEFT JOIN chat_user_presence p ON p.user_id = u.id
                    LEFT JOIN (
                        SELECT sender_id, COUNT(*) AS total_unread
                        FROM chat_messages
                        WHERE receiver_id = ? AND read_at IS NULL
                        GROUP BY sender_id
                    ) unread ON unread.sender_id = u.id
                    WHERE u.id <> ?
                    ORDER BY is_online DESC, unread_count DESC, u.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $userId]);
            $contacts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($contacts as &$contact) {
                $contact['is_ai'] = 0;
                $contact['avatar_url'] = null;
            }
            unset($contact);

            $botUnreadStmt = $this->db->prepare("SELECT COUNT(*) FROM chat_messages WHERE sender_id = ? AND receiver_id = ? AND read_at IS NULL");
            $botUnreadStmt->execute([self::AI_BOT_ID, $userId]);
            $botUnreadCount = (int)$botUnreadStmt->fetchColumn();

            array_unshift($contacts, [
                'id' => self::AI_BOT_ID,
                'name' => self::AI_BOT_NAME,
                'email' => self::AI_BOT_EMAIL,
                'has_photo' => 1,
                'is_online' => 1,
                'unread_count' => $botUnreadCount,
                'last_seen' => date('Y-m-d H:i:s'),
                'is_ai' => 1,
                'avatar_url' => self::AI_BOT_AVATAR_URL
            ]);

            echo json_encode([
                'success' => true,
                'contacts' => $contacts,
                'me' => [
                    'id' => $userId,
                    'name' => $_SESSION['user_name'] ?? 'Você'
                ]
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao carregar contatos']);
        }
    }

    public function getMessages($contactId): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        $contactId = (int)$contactId;
        if ($contactId <= 0 && $contactId !== self::AI_BOT_ID) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Contato inválido']);
            return;
        }

        try {
            $this->touchPresence($userId);

            $markReadStmt = $this->db->prepare("UPDATE chat_messages
                SET read_at = NOW()
                WHERE sender_id = ?
                  AND receiver_id = ?
                  AND read_at IS NULL");
            $markReadStmt->execute([$contactId, $userId]);

            $stmt = $this->db->prepare("SELECT
                    id,
                    sender_id,
                    receiver_id,
                    message,
                    payload_json,
                    created_at,
                    read_at
                FROM chat_messages
                WHERE (sender_id = ? AND receiver_id = ?)
                   OR (sender_id = ? AND receiver_id = ?)
                ORDER BY id DESC
                LIMIT 100");
            $stmt->execute([$userId, $contactId, $contactId, $userId]);
            $messages = array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC));

            foreach ($messages as &$msg) {
                if (!empty($msg['payload_json'])) {
                    $payload = json_decode((string)$msg['payload_json'], true);
                    if (is_array($payload) && isset($payload['text'])) {
                        $msg['message'] = (string)$payload['text'];
                    }
                }
                unset($msg['payload_json']);
            }
            unset($msg);

            echo json_encode(['success' => true, 'messages' => $messages]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao carregar mensagens']);
        }
    }

    public function getGlobalMessages(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        http_response_code(410);
        echo json_encode(['success' => false, 'message' => 'Sala geral desativada. Use conversas privadas.']);
    }

    public function sendMessage(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message = trim((string)($_POST['message'] ?? ''));

        if (($receiverId <= 0 && $receiverId !== self::AI_BOT_ID) || $message === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Destinatário e mensagem são obrigatórios']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Mensagem muito longa (máximo 2000 caracteres)']);
            return;
        }

        if ($receiverId === self::AI_BOT_ID) {
            $this->sendMessageToAi($userId, $message);
            return;
        }

        try {
            $userExistsStmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $userExistsStmt->execute([$receiverId]);
            if (!$userExistsStmt->fetchColumn()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Usuário de destino não encontrado']);
                return;
            }

            $payload = [
                'text' => $message,
                'format' => 'plain_text',
                'version' => 1,
                'chat_type' => 'direct',
                'sent_at' => date('c')
            ];

            $stmt = $this->db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, payload_json, created_at)
                VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$userId, $receiverId, $message, json_encode($payload, JSON_UNESCAPED_UNICODE)]);

            $this->touchPresence($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Mensagem enviada',
                'data' => [
                    'id' => (int)$this->db->lastInsertId(),
                    'sender_id' => $userId,
                    'receiver_id' => $receiverId,
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem']);
        }
    }

    public function sendGlobalMessage(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        http_response_code(410);
        echo json_encode(['success' => false, 'message' => 'Sala geral desativada. Use conversas privadas.']);
    }

    private function touchPresence(int $userId): void
    {
        $stmt = $this->db->prepare("INSERT INTO chat_user_presence (user_id, last_seen)
            VALUES (?, NOW())
            ON DUPLICATE KEY UPDATE last_seen = NOW()");
        $stmt->execute([$userId]);
    }

    private function sendMessageToAi(int $userId, string $message): void
    {
        try {
            $userPayload = [
                'text' => $message,
                'format' => 'plain_text',
                'version' => 1,
                'chat_type' => 'direct',
                'target' => 'ai_daniel',
                'sent_at' => date('c')
            ];

            $insertUserStmt = $this->db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, payload_json, created_at)
                VALUES (?, ?, ?, ?, NOW())");
            $insertUserStmt->execute([$userId, self::AI_BOT_ID, $message, json_encode($userPayload, JSON_UNESCAPED_UNICODE)]);

            $aiText = $this->generateAiResponse($userId, $message);

            $aiPayload = [
                'text' => $aiText,
                'format' => 'plain_text',
                'version' => 1,
                'chat_type' => 'direct',
                'source' => 'ai_daniel',
                'sent_at' => date('c')
            ];

            $insertAiStmt = $this->db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, payload_json, created_at)
                VALUES (?, ?, ?, ?, NOW())");
            $insertAiStmt->execute([self::AI_BOT_ID, $userId, $aiText, json_encode($aiPayload, JSON_UNESCAPED_UNICODE)]);

            echo json_encode([
                'success' => true,
                'message' => 'Mensagem enviada para Daniel do Suporte',
                'data' => [
                    'receiver_id' => self::AI_BOT_ID,
                    'receiver_name' => self::AI_BOT_NAME
                ]
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao conversar com Daniel do Suporte']);
        }
    }

    private function generateAiResponse(int $userId, string $message): string
    {
        if (!$this->isSupportedTopic($message)) {
            return 'Oi! Eu sou o Daniel do Suporte 🙂 Posso te ajudar com impressoras, toners, notebooks, notas fiscais, cálculos fiscais e dúvidas sobre os módulos do sistema. Se quiser, me manda sua dúvida nesses temas que eu te ajudo agora.';
        }

        $apiKey = trim((string)($_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: ''));
        if ($apiKey === '') {
            return 'Estou sem acesso à IA neste momento 😕. O administrador precisa configurar a variável GEMINI_API_KEY no ambiente para eu responder normalmente.';
        }

        if (!function_exists('curl_init')) {
            return 'Não consegui responder agora porque o servidor está sem suporte a cURL para acessar a IA.';
        }

        $history = $this->loadAiHistory($userId);
        $historyText = $history === '' ? '(sem histórico anterior)' : $history;

        $prompt = "Você é Daniel do Suporte, assistente de IA interno do sistema SGQ.\n"
            . "Fale sempre em português do Brasil com tom natural, humano e descontraído, como um colega prestativo do time de suporte.\n"
            . "Seja claro e direto; use frases curtas e exemplos práticos quando ajudar.\n"
            . "Demonstre empatia e cordialidade sem exageros.\n"
            . "Limite estrito de escopo: impressoras, toners, notebooks, notas fiscais, cálculos fiscais e dúvidas sobre módulos do sistema.\n"
            . "Se a pergunta sair desse escopo, recuse educadamente e convide a pessoa a perguntar sobre os temas permitidos.\n"
            . "Nunca invente acesso a banco de dados em tempo real. Nunca peça senha, token ou dados sensíveis.\n"
            . "Sempre que fizer sentido, termine com uma pergunta curta para continuar o atendimento (ex.: 'quer que eu te guie no passo a passo?').\n"
            . "Histórico recente:\n" . $historyText . "\n\n"
            . "Pergunta atual do usuário: " . $message;

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.75,
                'topP' => 0.9,
                'maxOutputTokens' => 500
            ]
        ];

        $models = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-2.0-flash',
            'gemini-2.0-flash-exp'
        ];

        $lastError = '';
        foreach ($models as $model) {
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode($apiKey);

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                $lastError = 'cURL (' . $model . '): ' . $curlError;
                continue;
            }

            $json = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                $text = trim((string)($json['candidates'][0]['content']['parts'][0]['text'] ?? ''));
                if ($text !== '') {
                    return $text;
                }

                $blockReason = (string)($json['promptFeedback']['blockReason'] ?? '');
                if ($blockReason !== '') {
                    return 'Não consegui responder essa mensagem do jeito que ela foi enviada. Pode reformular em uma pergunta mais objetiva sobre o sistema?';
                }

                $lastError = 'Resposta vazia (' . $model . ')';
                continue;
            }

            $apiError = trim((string)($json['error']['message'] ?? ''));
            $apiErrorUpper = strtoupper($apiError);
            if (strpos($apiErrorUpper, 'API_KEY_INVALID') !== false || strpos($apiErrorUpper, 'PERMISSION_DENIED') !== false) {
                return 'A chave do Gemini parece inválida ou sem permissão. Confirme a GEMINI_API_KEY no .env e se a API Generative Language está habilitada no Google Cloud.';
            }

            if (strpos($apiErrorUpper, 'QUOTA') !== false || strpos($apiErrorUpper, 'RATE LIMIT') !== false || $httpCode === 429) {
                return 'A cota da API do Gemini foi atingida no momento. Tente novamente em instantes.';
            }

            $lastError = 'HTTP ' . $httpCode . ' (' . $model . '): ' . ($apiError !== '' ? $apiError : 'erro desconhecido');
        }

        error_log('Daniel Gemini falhou: ' . $lastError);
        return 'Estou enfrentando instabilidade para responder agora. Tente novamente em alguns segundos.';
    }

    private function loadAiHistory(int $userId): string
    {
        try {
            $stmt = $this->db->prepare("SELECT sender_id, message
                FROM chat_messages
                WHERE (sender_id = ? AND receiver_id = ?)
                   OR (sender_id = ? AND receiver_id = ?)
                ORDER BY id DESC
                LIMIT 8");
            $stmt->execute([$userId, self::AI_BOT_ID, self::AI_BOT_ID, $userId]);
            $rows = array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC));

            $lines = [];
            foreach ($rows as $row) {
                $prefix = ((int)$row['sender_id'] === self::AI_BOT_ID) ? 'Daniel' : 'Usuario';
                $lines[] = $prefix . ': ' . trim((string)$row['message']);
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function isSupportedTopic(string $text): bool
    {
        $normalized = mb_strtolower($text, 'UTF-8');
        $keywords = [
            'impressora', 'toner', 'notebook', 'nf', 'nota fiscal', 'fiscal',
            'icms', 'ipi', 'pis', 'cofins', 'tribut', 'módulo', 'modulo',
            'sgq', 'sistema', 'retornado', 'garantia', 'cadastro', 'perfil',
            'relatório', 'relatorio', 'departamento', 'filial'
        ];

        foreach ($keywords as $keyword) {
            if (strpos($normalized, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
