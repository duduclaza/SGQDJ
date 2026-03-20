<?php
/**
 * Script para executar queries V3.0 de homologações
 * Acesse via navegador: http://seusite.com/executar_v3_queries.php
 */

require_once __DIR__ . '/src/Config/Database.php';

$db = \App\Config\Database::getInstance();

echo "<h1>🚀 Executando Queries V3.0</h1>";
echo "<hr>";

try {
    // QUERY 2: Expandir homologacoes
    echo "<h2>📊 QUERY 2: Expandindo tabela homologacoes...</h2>";
    
    $queries2 = [
        "ALTER TABLE `homologacoes` ADD COLUMN `tipo_homologacao_v2` ENUM('interna','cliente') DEFAULT NULL AFTER `dias_avisar_antecedencia`",
        "ALTER TABLE `homologacoes` ADD COLUMN `cliente_id` INT(10) UNSIGNED DEFAULT NULL AFTER `tipo_homologacao_v2`",
        "ALTER TABLE `homologacoes` ADD COLUMN `data_instalacao_v2` DATE DEFAULT NULL AFTER `cliente_id`",
        "ALTER TABLE `homologacoes` ADD COLUMN `observacao_fase_teste` LONGTEXT DEFAULT NULL AFTER `data_instalacao_v2`",
        "ALTER TABLE `homologacoes` ADD COLUMN `produto_atendeu_expectativas` ENUM('sim','nao') DEFAULT NULL AFTER `observacao_fase_teste`",
        "ALTER TABLE `homologacoes` ADD COLUMN `data_finalizacao_teste` DATETIME DEFAULT NULL AFTER `produto_atendeu_expectativas`",
        "ALTER TABLE `homologacoes` ADD COLUMN `parecer_final_tecnico` LONGTEXT DEFAULT NULL AFTER `data_finalizacao_teste`",
        "ALTER TABLE `homologacoes` ADD COLUMN `finalizado_por` INT(10) UNSIGNED DEFAULT NULL AFTER `parecer_final_tecnico`",
        "ALTER TABLE `homologacoes` ADD COLUMN `finalizado_at` DATETIME DEFAULT NULL AFTER `finalizado_por`",
        "ALTER TABLE `homologacoes` ADD KEY `idx_tipo_homologacao_v2` (`tipo_homologacao_v2`)",
        "ALTER TABLE `homologacoes` ADD KEY `idx_cliente_id` (`cliente_id`)",
        "ALTER TABLE `homologacoes` ADD KEY `idx_data_finalizacao_teste` (`data_finalizacao_teste`)",
        "ALTER TABLE `homologacoes` ADD KEY `idx_finalizado_por` (`finalizado_por`)",
        "ALTER TABLE `homologacoes` ADD KEY `idx_product_atendeu` (`produto_atendeu_expectativas`)",
        "ALTER TABLE `homologacoes` ADD CONSTRAINT `fk_homologacoes_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL"
    ];
    
    $contador = 0;
    foreach ($queries2 as $i => $query) {
        try {
            $db->exec($query);
            echo "✅ Query 2." . ($i + 1) . " OK<br>";
            $contador++;
        } catch (Exception $e) {
            // Ignorar erros de coluna duplicada ou constraint já existe
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'Constraint') !== false) {
                echo "⚠️ Query 2." . ($i + 1) . " (já existe, ignorado)<br>";
            } else {
                echo "❌ Query 2." . ($i + 1) . " ERRO: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<p><strong>Query 2 executadas com sucesso: $contador/15 comandos</strong></p>";
    echo "<hr>";
    
    // QUERY 3: Criar tabela movimentacao
    echo "<h2>📋 QUERY 3: Criando tabela homologacoes_movimentacao...</h2>";
    
    try {
        $query3_create = "CREATE TABLE IF NOT EXISTS `homologacoes_movimentacao` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `homologacao_id` int(10) unsigned NOT NULL,
          `status_antigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `status_novo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
          `usuario_id` int(10) unsigned DEFAULT NULL,
          `usuario_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Sistema',
          `data_movimentacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `observacao` longtext COLLATE utf8mb4_unicode_ci,
          PRIMARY KEY (`id`),
          KEY `idx_homologacao_id` (`homologacao_id`),
          KEY `idx_usuario_id` (`usuario_id`),
          KEY `idx_data_movimentacao` (`data_movimentacao`),
          KEY `idx_status_novo` (`status_novo`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($query3_create);
        echo "✅ Query 3.1 (CREATE TABLE) OK<br>";
    } catch (Exception $e) {
        echo "❌ Query 3.1 ERRO: " . $e->getMessage() . "<br>";
    }
    
    try {
        $query3_fk = "ALTER TABLE `homologacoes_movimentacao` ADD CONSTRAINT `fk_movimentacao_homologacao` FOREIGN KEY (`homologacao_id`) REFERENCES `homologacoes` (`id`) ON DELETE CASCADE";
        $db->exec($query3_fk);
        echo "✅ Query 3.2 (FOREIGN KEY) OK<br>";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Constraint') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "⚠️ Query 3.2 (FK já existe, ignorado)<br>";
        } else {
            echo "❌ Query 3.2 ERRO: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 SUCESSO!</h2>";
    echo "<p>Todas as queries foram executadas. Verifique com:</p>";
    echo "<pre>DESC homologacoes;
DESC homologacoes_movimentacao;</pre>";
    
} catch (Exception $e) {
    echo "<h2>❌ ERRO CRÍTICO</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    die();
}

echo "<hr>";
echo "<p><a href='javascript:location.reload()'>🔄 Executar novamente</a></p>";
?>
