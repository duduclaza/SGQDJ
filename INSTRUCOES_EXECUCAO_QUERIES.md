# 🔧 INSTRUÇÕES DE EXECUÇÃO - QUERIES SQL

**Data:** 20/03/2026  
**Total de Queries:** 3  
**Tempo Estimado:** 5 minutos

---

## 📋 ARQUIVOS SQL CRIADOS

### **Query 1: Criar Tabela de Clientes**
📁 **Localização:** `database/migrations/2026_03_20_create_clientes_table.sql`

```sql
CREATE TABLE clientes (
  ✓ id, nome_cliente, cnpj, email, telefone
  ✓ endereco, cidade, estado, status
  ✓ created_at, updated_at
);
```

**Tabelai NOVA** - Nenhuma dependência

---

### **Query 2: Expandir Tabela Homologações**
📁 **Localização:** `database/migrations/2026_03_20_expand_homologacoes_part2.sql`

```sql
ALTER TABLE homologacoes ADD COLUMNS (
  ✓ dias_avisar_antecedencia
  ✓ tipo_homologacao_v2
  ✓ cliente_id (FK)
  ✓ data_instalacao_v2
  ✓ observacao_fase_teste
  ✓ produto_atendeu_expectativas
  ✓ data_finalizacao_teste
  ✓ parecer_final_tecnico
  ✓ finalizado_por
  ✓ finalizado_at
);
```

**TOTAL: 10 colunas novas**  
**Dependência:** Query 1 deve ser executada ANTES

---

### **Query 3: Criar Log de Movimentação**
📁 **Localização:** `database/migrations/2026_03_20_create_movimentacao_table.sql`

```sql
CREATE TABLE homologacoes_movimentacao (
  ✓ id, homologacao_id, status_antigo, status_novo
  ✓ usuario_id, usuario_nome
  ✓ data_movimentacao, observacao
);
```

**Tabela NOVA** - Apenas FK para homologacoes e users

---

## ⚙️ COMO EXECUTAR

### **Opção 1: Via MySQL Client (Linha de Comando)**

```bash
# Abrir MySQL Client
mysql -u seu_usuario -p sua_senha seu_banco

# Executar as queries em ORDEM
source database/migrations/2026_03_20_create_clientes_table.sql;
source database/migrations/2026_03_20_expand_homologacoes_part2.sql;
source database/migrations/2026_03_20_create_movimentacao_table.sql;

# Verificar
SHOW TABLES;
DESC clientes;
DESC homologacoes;
DESC homologacoes_movimentacao;
```

---

### **Opção 2: Via phpMyAdmin**

1. Acesse phpMyAdmin
2. Selecione seu banco de dados
3. Vá para "SQL" (aba)
4. **Copie Query 1** inteira → Cole → Clique em "Executar"
5. **Copie Query 2** inteira → Cole → Clique em "Executar"
6. **Copie Query 3** inteira → Cole → Clique em "Executar"
7. Aguarde confirmação de cada uma

---

### **Opção 3: Via Script PHP**

```php
<?php
$conn = new mysqli('localhost', 'usuario', 'senha', 'banco');

if ($conn->connect_error) {
    die('Erro: ' . $conn->connect_error);
}

$queries = [
    file_get_contents('database/migrations/2026_03_20_create_clientes_table.sql'),
    file_get_contents('database/migrations/2026_03_20_expand_homologacoes_part2.sql'),
    file_get_contents('database/migrations/2026_03_20_create_movimentacao_table.sql')
];

foreach ($queries as $sql) {
    if ($conn->multi_query($sql)) {
        echo "✅ Query executada com sucesso\n";
        while ($conn->more_results()) {
            $conn->next_result();
        }
    } else {
        echo "❌ Erro: " . $conn->error . "\n";
    }
}

$conn->close();
?>
```

---

## ✅ VERIFICAÇÃO APÓS EXECUÇÃO

### **Verificar se tudo foi criado:**

```sql
-- Ver tabelas novas
SHOW TABLES LIKE 'clientes';
SHOW TABLES LIKE 'homologacoes_movimentacao';

-- Contar colunas em homologacoes
SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'homologacoes';

-- Ver estrutura de clientes
DESC clientes;

-- Ver estrutura de movimentação
DESC homologacoes_movimentacao;

-- Ver novas colunas em homologacoes
DESC homologacoes;
```

---

## ⚠️ PONTOS IMPORTANTES

### **Ordem de Execução:**
1. ✅ Query 1 (clientes)
2. ✅ Query 2 (homologacoes - depende de clientes criada)
3. ✅ Query 3 (movimentacao)

### **Segurança:**
- Não há DROP TABLE (seguro)
- Apenas ADD COLUMN (seguro)
- Foreign Keys com ON DELETE SET NULL (preserva dados)

### **Performance:**
- Índices foram adicionados automaticamente
- FULLTEXT INDEX em nome_cliente (para busca rápida)
- Nenhum impacto em queries existentes

### **Compatibilidade:**
- Código anterior continua funcionando (novas colunas são NULL)
- Sem mudanças em colunas existentes
- Sem rename de colunas

---

## 🚨 SE ALGO DER ERRO

### **Erro: "Table clientes already exists"**
- Significa que você já tem a tabela → Pule a Query 1

### **Erro: "Can't add foreign key constraint"**
- Query 1 não foi executada ainda → Execute Query 1 primeiro
- OU cliente_id não é UNSIGNED INT → Verifique Query 1

### **Erro: "Duplicate column name"**
- Coluna já existe → Execute apenas as queries que faltam

### **Erro: "Unknown column in field list"**
- Dependência não satisfeita → Volte alguns passos

---

## 📞 PRÓXIMOS PASSOS APÓS EXECUÇÃO

Após executar as 3 queries com SUCESSO:

1. ✅ **Validar no BD** - Verifique estruturas
2. ⏳ **Aguardar** - Vou criar as alterações no Controller
3. ⏳ **Aguardar** - Vou criar a View reformulada
4. ⏳ **Aguardar** - Vou criar a API de clientes

---

## 📊 RESUMO DO QUE FOI CRIADO

| Objeto | Tipo | Colunas | Status |
|--------|------|---------|--------|
| `clientes` | Tabela | 10 | ✨ Novo |
| `homologacoes` (exp) | ALTER | +10 | 📝 Modificado |
| `homologacoes_movimentacao` | Tabela | 8 | ✨ Novo |

**Total de Mudanças:** 28 colunas (10 novas no clientes + 10 novas em homologacoes + 8 em movimentacao)

---

**Avance para a próxima etapa quando tiver confirmado a execução das 3 queries!** ✅

