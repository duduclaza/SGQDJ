<?php
$file = __DIR__ . '/../src/Controllers/TonersController.php';

if (!file_exists($file)) {
    die("File not found: $file\n");
}

$content = file_get_contents($file);

// Find the last occurrence of '}'
$pos = strrpos($content, '}');

if ($pos === false) {
    die("Closing brace not found!\n");
}

// Remove the last '}' (and any whitespace after it)
$content = substr($content, 0, $pos);

// Append the new methods
$newMethods = <<<'PHP'

    /**
     * Store Devolutiva (Feedback from Qualidade)
     * POST /toners/defeitos/devolutiva/store
     */
    public function storeDevolutiva(): void
    {
        header('Content-Type: application/json');
        
        $setor = $_SESSION['user_setor'] ?? '';
        $role  = $_SESSION['user_role'] ?? '';
        
        // Check permissions
        $isQualidade = stripos($setor, 'Qualidade') !== false;
        $isAdmin     = in_array($role, ['admin', 'super_admin']);
        
        if (!$isQualidade && !$isAdmin) {
             echo json_encode(['success' => false, 'message' => 'Acesso negado. Apenas Qualidade pode inserir.']);
             return;
        }

        try {
            $id = (int)($_POST['defeito_id'] ?? 0);
            $descricao = trim($_POST['devolutiva_descricao'] ?? '');
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }
            if (empty($descricao)) {
                echo json_encode(['success' => false, 'message' => 'Descrição obrigatória.']);
                return;
            }
            
            // Check if exists
            $stmt = $this->db->prepare("SELECT id FROM toners_defeitos WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Defeito não encontrado.']);
                return;
            }

            // Upload Fotos
            $fotos = [];
            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES["devolutiva_foto$i"]) && $_FILES["devolutiva_foto$i"]['error'] === UPLOAD_ERR_OK) {
                    $fotos[$i] = file_get_contents($_FILES["devolutiva_foto$i"]['tmp_name']);
                } else {
                    $fotos[$i] = null;
                }
            }

            // Update Query Dinâmica
            $sql = "UPDATE toners_defeitos SET 
                    devolutiva_descricao = ?, 
                    devolutiva_at = NOW(),
                    devolutiva_uid = ?";
            
            $params = [$descricao, $_SESSION['user_id'] ?? 0];
            
            for ($i = 1; $i <= 3; $i++) {
                if ($fotos[$i] !== null) {
                    $sql .= ", devolutiva_foto$i = ?";
                    $params[] = $fotos[$i];
                }
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true, 'message' => 'Devolutiva salva com sucesso!']);
            
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao salvar: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Devolutiva Photo
     * GET /toners/defeitos/{id}/devolutiva-foto/{n}
     */
    public function downloadFotoDevolutiva($id, $n): void
    {
        $n = (int)$n;
        if ($n < 1 || $n > 3) exit('Foto inválida');

        try {
            $stmt = $this->db->prepare("SELECT devolutiva_foto{$n} AS foto FROM toners_defeitos WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row || empty($row['foto'])) {
                http_response_code(404);
                exit('Foto não encontrada');
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($row['foto']);
            header("Content-Type: $mime");
            header("Content-Length: " . strlen($row['foto']));
            echo $row['foto'];
        } catch (\PDOException $e) {
            http_response_code(500);
            exit('Erro interno');
        }
    }
}
PHP;

$content .= $newMethods;

file_put_contents($file, $content);
echo "File patched successfully!\n";
