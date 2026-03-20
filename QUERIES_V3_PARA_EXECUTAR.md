# 📊 QUERIES SQL V3.0 - PRONTO PARA EXECUTAR

**Data:** 20 de março de 2026  
**Modificações realizadas no PHP:** ✅ COMPLETO  
**Status:** Aguardando execução das queries

---

## 🚀 RESUMO DO QUE FOI MODIFICADO NO PHP

### ✅ Controller (HomologacoesKanbanController.php)

**Novos Métodos de Permissão:**
- `isAdmin()` - Verifica se user é admin
- `isCreatorOrAdmin()` - Verifica se user é criador ou admin
- `isResponsible()` - Verifica se user é responsável
- `canEditPart2()` - ADMIN, CRIADOR ou RESPONSÁVEL podem editar Parte 2
- `canDeleteCard()` - Apenas ADMIN e CRIADOR podem deletar
- `canMoveCard()` - ADMIN, CRIADOR e RESPONSÁVEL podem mover

**Novos Métodos:**
- `updatePart2()` - POST para atualizar APENAS Parte 2 (com permissões)
- `registrarMovimentacao()` - Registra movimento automático em homologacoes_movimentacao
- `enviarEmailFinalizacao()` - Envia email para ADMINS + CRIADOR quando finaliza
- `buscarClientes()` - GET para autocomplete de clientes (novo endpoint API)

**Métodos Modificados:**
- `updateStatusById()` - Agora chama `registrarMovimentacao()`automaticamente
- `delete()` - Agora valida com `canDeleteCard()`

### ✅ Routes (routes/modules/homologacoes.php)

**Novas Rotas:**
- `POST /homologacoes/{id}/update-part2` → updatePart2()
- `GET /homologacoes/api/clientes` → buscarClientes()

---

## 📝 QUERIES SQL PARA EXECUTAR (EM ORDEM)

Execute as 3 queries abaixo **NESTA ORDEM EXATA**. Copie e paste no seu MySQL/phpMyAdmin.

### **QUERY 1: Criar Tabela CLIENTES**

```sql
-- Tabela de clientes para busca/seleção em homologações
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome_cliente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nome_cliente` (`nome_cliente`),
  UNIQUE KEY `unique_cnpj` (`cnpj`),
  KEY `idx_status` (`status`),
  KEY `idx_cidade` (`cidade`),
  FULLTEXT KEY `ft_nome_cliente` (`nome_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**O que faz:** Cria a tabela de clientes com campos de busca (FULLTEXT index) para autocomplete.

---

### **QUERY 2: Expandir Tabela HOMOLOGACOES com PARTE 2**

```sql
-- Adicionar colunas da PARTE 2 - Detalhes & Progresso (v3.0)
ALTER TABLE `homologacoes` ADD COLUMN `dias_avisar_antecedencia` INT DEFAULT 7 AFTER `data_vencimento`;
ALTER TABLE `homologacoes` ADD COLUMN `tipo_homologacao_v2` ENUM('interna','cliente') DEFAULT NULL AFTER `dias_avisar_antecedencia`;
ALTER TABLE `homologacoes` ADD COLUMN `cliente_id` INT(10) UNSIGNED DEFAULT NULL AFTER `tipo_homologacao_v2`;
ALTER TABLE `homologacoes` ADD COLUMN `data_instalacao_v2` DATE DEFAULT NULL AFTER `cliente_id`;
ALTER TABLE `homologacoes` ADD COLUMN `observacao_fase_teste` LONGTEXT DEFAULT NULL AFTER `data_instalacao_v2`;
ALTER TABLE `homologacoes` ADD COLUMN `produto_atendeu_expectativas` ENUM('sim','nao') DEFAULT NULL AFTER `observacao_fase_teste`;
ALTER TABLE `homologacoes` ADD COLUMN `data_finalizacao_teste` DATETIME DEFAULT NULL AFTER `produto_atendeu_expectativas`;
ALTER TABLE `homologacoes` ADD COLUMN `parecer_final_tecnico` LONGTEXT DEFAULT NULL AFTER `data_finalizacao_teste`;
ALTER TABLE `homologacoes` ADD COLUMN `finalizado_por` INT(10) UNSIGNED DEFAULT NULL AFTER `parecer_final_tecnico`;
ALTER TABLE `homologacoes` ADD COLUMN `finalizado_at` DATETIME DEFAULT NULL AFTER `finalizado_por`;

-- Adicionar índices para performance
ALTER TABLE `homologacoes` ADD KEY `idx_tipo_homologacao_v2` (`tipo_homologacao_v2`);
ALTER TABLE `homologacoes` ADD KEY `idx_cliente_id` (`cliente_id`);
ALTER TABLE `homologacoes` ADD KEY `idx_data_finalizacao_teste` (`data_finalizacao_teste`);
ALTER TABLE `homologacoes` ADD KEY `idx_finalizado_por` (`finalizado_por`);
ALTER TABLE `homologacoes` ADD KEY `idx_product_atendeu` (`produto_atendeu_expectativas`);

-- Adicionar Foreign Key para tabela clientes
ALTER TABLE `homologacoes` 
ADD CONSTRAINT `fk_homologacoes_clientes` 
FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

-- Se deseja adicionar FK para finalizado_por (opcional)
-- ALTER TABLE `homologacoes` 
-- ADD CONSTRAINT `fk_homologacoes_finalizado_por` 
-- FOREIGN KEY (`finalizado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL;
```

**O que faz:** Adiciona todos os campos da PARTE 2 (detalhes, progresso, parecer técnico, etc) à tabela homologacoes.

---

### **QUERY 3: Criar Tabela HOMOLOGACOES_MOVIMENTACAO**

```sql
-- Tabela de LOG de Movimentos - Rastrear todos os movimentos dos cards
CREATE TABLE IF NOT EXISTS `homologacoes_movimentacao` (
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
  KEY `idx_status_novo` (`status_novo`),
  CONSTRAINT `fk_movimentacao_homologacao` FOREIGN KEY (`homologacao_id`) REFERENCES `homologacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**O que faz:** Cria a tabela de logs de movimentação com FK CASCADE para garantir integridade.

---

## ✅ PASSO A PASSO DE EXECUÇÃO

### **Opção 1: phpMyAdmin (Recomendado para iniciantes)**

1. Abra phpMyAdmin
2. Selecione seu banco de dados SGQDJ
3. Clique na aba **"SQL"**
4. Cole a **QUERY 1** completa
5. Clique em **"Executar"** (botão azul)
6. Aguarde a mensagem de sucesso: ✅ **1 tabela criada**
7. Repita os passos 3-5 para **QUERY 2**
8. Aguarde a mensagem de sucesso: ✅ **14 alterações + 1 FK adicionados**
9. Repita os passos 3-5 para **QUERY 3**
10. Aguarde a mensagem de sucesso: ✅ **1 tabela criada**

### **Opção 2: Terminal/PowerShell (Para usuários avançados)**

```bash
# Conectar ao MySQL
mysql -u seu_usuario -p seu_banco_de_dados

# Cole uma query por vez, pressionando Enter após cada uma
# Query 1...
mysql> [COLE QUERY 1 AQUI]
Query OK, 1 row affected

# Query 2...
mysql> [COLE QUERY 2 AQUI]
Query OK, 14 rows affected

# Query 3...
mysql> [COLE QUERY 3 AQUI]
Query OK, 1 row affected

# Sair
mysql> EXIT;
```

### **Opção 3: Script PHP (Se quiser automatizar)**

Crie um arquivo `executar_queries_v3.php` na raiz do projeto:

```php
<?php
// executar_queries_v3.php

require_once __DIR__ . '/src/Config/Database.php';

$db = \App\Config\Database::getInstance();

$queries = [
    // Query 1
    "CREATE TABLE IF NOT EXISTS `clientes` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        ... [QUERY 1 COMPLETA] ...
    )",
    
    // Query 2
    "ALTER TABLE `homologacoes` ADD COLUMN `dias_avisar_antecedencia` INT DEFAULT 7 ...",
    
    // Query 3
    "CREATE TABLE IF NOT EXISTS `homologacoes_movimentacao` ( ... [QUERY 3 COMPLETA] ... )"
];

try {
    foreach ($queries as $i => $query) {
        $db->exec($query);
        echo "✅ Query " . ($i + 1) . " executada com sucesso!\n";
    }
    echo "\n🎉 Todas as queries foram executadas com sucesso!\n";
} catch (Exception $e) {
    echo "❌ Erro ao executar query: " . $e->getMessage();
}
?>
```

---

## ✓ VERIFICAÇÃO PÓS-EXECUÇÃO

### **1. Verificar se tabela CLIENTES foi criada:**
```sql
DESC clientes;
-- Deve mostrar: id, nome_cliente, cnpj, email, telefone, endereco, cidade, estado, status, created_at, updated_at
```

### **2. Verificar se colunas foram adicionadas em HOMOLOGACOES:**
```sql
DESC homologacoes;
-- Procure por: dias_avisar_antecedencia, tipo_homologacao_v2, cliente_id, data_instalacao_v2, observacao_fase_teste, produto_atendeu_expectativas, data_finalizacao_teste, parecer_final_tecnico, finalizado_por, finalizado_at
```

### **3. Verificar se tabela MOVIMENTACAO foi criada:**
```sql
DESC homologacoes_movimentacao;
-- Deve mostrar: id, homologacao_id, status_antigo, status_novo, usuario_id, usuario_nome, data_movimentacao, observacao
```

### **4. Verificar Foreign Keys:**
```sql
SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'homologacoes' AND COLUMN_NAME LIKE '%cliente%';
-- Deve retornar: fk_homologacoes_clientes
```

---

## 📋 PRÓXIMOS PASSOS (Após executar as queries)

1. ✅ **Você executa as 3 queries acima**
2. ✅ **Você verifica que tudo foi criado** (usar queries de verificação acima)
3. ⏳ **Você confirma para mim** que as queries executaram com sucesso
4. ⏳ **Eu modifico a view** (kanban.php) com os campos PARTE 2, validações JS, logs de movimentação
5. ⏳ **Você testa o sistema** completo end-to-end

---

## ⚠️ IMPORTANTE

- **NÃO execute as queries** fora da ordem!
- **Se errarem as queries**, não se preocupe - as FKs CASCADE garantem limpeza automática
- **Faça backup do banco** antes de executar (recomendado)
- **Qualquer erro?** Envie a mensagem de erro completa do MySQL para eu ajudar

---

## 📞 PRÓXIMA ETAPA

**Após executar as 3 queries com sucesso**, me informe:

```
✅ Query 1 - Tabela clientes criada
✅ Query 2 - Colunas adicionadas em homologacoes
✅ Query 3 - Tabela homologacoes_movimentacao criada
```

Então vou criar a próxima etapa: **Modificar a view kanban.php** com:
- Separação visual PARTE 1 (read-only 🔒) 
- PARTE 2 completa (editável)
- Campos novos v3
- Logs de movimentação
- Autocomplete de clientes
- Validações JavaScript

---

**Sucesso! 🚀**
