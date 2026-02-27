<?php

namespace App\Controllers;

use App\Config\Database;

class ChatController
{
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
        if ($contactId <= 0) {
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

        try {
            $this->touchPresence($userId);

            $stmt = $this->db->prepare("SELECT
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.message,
                    m.payload_json,
                    m.created_at,
                    m.read_at,
                    u.name AS sender_name,
                    CASE WHEN u.profile_photo IS NOT NULL THEN 1 ELSE 0 END AS sender_has_photo
                FROM chat_messages m
                LEFT JOIN users u ON u.id = m.sender_id
                WHERE m.receiver_id = 0
                ORDER BY m.id DESC
                LIMIT 150");
            $stmt->execute();
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
            echo json_encode(['success' => false, 'message' => 'Erro ao carregar mensagens da sala geral']);
        }
    }

    public function sendMessage(): void
    {
        $userId = $this->requireAuthJson();
        if ($userId === null) {
            return;
        }

        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message = trim((string)($_POST['message'] ?? ''));

        if ($receiverId <= 0 || $message === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Destinatário e mensagem são obrigatórios']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Mensagem muito longa (máximo 2000 caracteres)']);
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

        $message = trim((string)($_POST['message'] ?? ''));
        if ($message === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Mensagem é obrigatória']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Mensagem muito longa (máximo 2000 caracteres)']);
            return;
        }

        try {
            $payload = [
                'text' => $message,
                'format' => 'plain_text',
                'version' => 1,
                'chat_type' => 'global',
                'sent_at' => date('c')
            ];

            $stmt = $this->db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, payload_json, created_at)
                VALUES (?, 0, ?, ?, NOW())");
            $stmt->execute([$userId, $message, json_encode($payload, JSON_UNESCAPED_UNICODE)]);

            $this->touchPresence($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Mensagem enviada na sala geral',
                'data' => [
                    'id' => (int)$this->db->lastInsertId(),
                    'sender_id' => $userId,
                    'receiver_id' => 0,
                    'sender_name' => $_SESSION['user_name'] ?? 'Você',
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem na sala geral']);
        }
    }

    private function touchPresence(int $userId): void
    {
        $stmt = $this->db->prepare("INSERT INTO chat_user_presence (user_id, last_seen)
            VALUES (?, NOW())
            ON DUPLICATE KEY UPDATE last_seen = NOW()");
        $stmt->execute([$userId]);
    }
}
