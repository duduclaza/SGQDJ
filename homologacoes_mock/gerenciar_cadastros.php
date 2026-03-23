<?php
require_once __DIR__ . '/init.php';

// Tratar a troca de usuário no mock (Global para o módulo mock)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trocar_usuario'])) {
    $_SESSION['usuario_logado_id'] = (int)$_POST['usuario_logado_id'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$u = getUsuarioLogado();

// Apenas Compras ou Admin podem gerenciar
if ($u['perfil'] !== 'compras') {
    $title = "Acesso Negado - Homologações 2.0";
    $viewFile = __DIR__ . '/views/erro_acesso.php'; // Criaremos se não existir, ou tratamos na view
    require_once __DIR__ . '/../views/layouts/main.php';
    exit;
}

use App\Config\Database;
$db = Database::getInstance();

// --- LÓGICA DE PRODUTOS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_produto'])) {
    $nome = trim($_POST['nome_produto'] ?? '');
    if ($_POST['acao_produto'] === 'adicionar' && !empty($nome)) {
        $stmt = $db->prepare("INSERT INTO homologacao_tipos_produto (nome) VALUES (?)");
        $stmt->execute([$nome]);
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Tipo de produto '$nome' adicionado!"];
    } elseif ($_POST['acao_produto'] === 'excluir' && !empty($_POST['id_produto'])) {
        $stmt = $db->prepare("UPDATE homologacao_tipos_produto SET ativo = 0 WHERE id = ?");
        $stmt->execute([$_POST['id_produto']]);
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Tipo de produto removido!"];
    }
    header("Location: gerenciar_cadastros.php");
    exit;
}

// --- LÓGICA DE CHECKLISTS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_checklist'])) {
    if ($_POST['acao_checklist'] === 'adicionar') {
        $titulo = trim($_POST['titulo'] ?? '');
        $tipo_id = !empty($_POST['tipo_produto_id']) ? (int)$_POST['tipo_produto_id'] : null;
        $itens = $_POST['itens'] ?? [];

        if (!empty($titulo) && !empty($itens)) {
            $db->beginTransaction();
            try {
                $stmt = $db->prepare("INSERT INTO homologacao_checklists (titulo, tipo_produto_id, criado_por) VALUES (?, ?, ?)");
                $stmt->execute([$titulo, $tipo_id, $_SESSION['user_id'] ?? 1]);
                $checklist_id = $db->lastInsertId();

                $stmtItem = $db->prepare("INSERT INTO homologacao_checklist_itens (checklist_id, titulo, ordem, tipo_resposta) VALUES (?, ?, ?, ?)");
                foreach ($itens as $i => $item_titulo) {
                    if (!empty($item_titulo)) {
                        $stmtItem->execute([$checklist_id, $item_titulo, $i, 'sim_nao']);
                    }
                }
                $db->commit();
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Checklist '$titulo' criado!"];
            } catch (\Exception $e) {
                $db->rollBack();
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => "Erro ao criar checklist: " . $e->getMessage()];
            }
        }
    } elseif ($_POST['acao_checklist'] === 'excluir' && !empty($_POST['id_checklist'])) {
        $stmt = $db->prepare("UPDATE homologacao_checklists SET ativo = 0 WHERE id = ?");
        $stmt->execute([$_POST['id_checklist']]);
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Checklist removido!"];
    }
    header("Location: gerenciar_cadastros.php");
    exit;
}

// Buscar dados atuais do banco real
$stmtTipos = $db->query("SELECT * FROM homologacao_tipos_produto WHERE ativo = 1 ORDER BY nome ASC");
$tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

$stmtChecklists = $db->query("
    SELECT c.*, tp.nome as tipo_produto_nome 
    FROM homologacao_checklists c
    LEFT JOIN homologacao_tipos_produto tp ON c.tipo_produto_id = tp.id
    WHERE c.ativo = 1 
    ORDER BY c.criado_em DESC
");
$checklists = $stmtChecklists->fetchAll(PDO::FETCH_ASSOC);

$title = "Gerenciar Produtos & Checklists - Homologações 2.0";
$viewFile = __DIR__ . '/views/gerenciar_cadastros.php';
require_once __DIR__ . '/../views/layouts/main.php';
