# 🗂️ MAPEAMENTO CAMPO → BANCO DE DADOS

**Arquivo:** Documentação técnica do mapeamento de campos do formulário para tabela homologacoes

---

## 📌 PARTE 1: CRIAÇÃO (Salva na primeira vez, NUNCA muda)

| # | 🎯 CAMPO NO FORMULÁRIO | 📊 COLUNA BD | TIPO | OBS. |
|---|-----------|------|------|------|
| 1 | **Cód. Referência** | `cod_referencia` | VARCHAR(20) | UNIQUE, gerado automaticamente ou manual |
| 2 | **Departamento Responsável** | `departamento_id` | INT UNSIGNED | FK para tabela departamentos |
| 3 | **Pessoas Responsáveis** *(não vai direto na tabela)* | `homologacoes_responsaveis` | M2M Table | Tabela de junção com user_id e email |
| 4 | **Descrição** | `descricao` | LONGTEXT | Campo original que já existe |
| 5 | **Data de Vencimento** | `data_vencimento` | DATE | Formato: YYYY-MM-DD |
| 6 | **Avisar com X dias** | `dias_avisar_antecedencia` | INT | Padrão: 7 dias (NEW - criado em migration) |
| 7 | **Notificar Logística** | `notificar_logistica` | BOOLEAN (TINYINT) | 1 = sim, 0 = não (NEW - criado em migration) |

**Tabela que será modificada:** `homologacoes`

**Campos já existentes a manter:**
- `id` - Auto increment
- `status` - ENUM('aguardando_recebimento', 'recebido', 'em_analise', 'em_homologacao', 'finalizado', 'aprovado', 'rejeitado')
- `created_by` - Quem criou (USER_ID)
- `created_at` - Quando criou
- `updated_at` - Última atualização
- `departamento_id` - Reutilizado!

---

## 📝 PARTE 2: DETALHES & PROGRESSO (Pode mudar sempre!)

| # | 🎯 CAMPO NO FORMULÁRIO | 📊 COLUNA BD | TIPO | OBS. |
|---|-----------|------|------|------|
| 1 | **Tipo de Homologação** | `tipo_homologacao_v2` | ENUM('interna','cliente') | NEW - criado em migration |
| 2 | **Cliente** *(condicional)* | `cliente_id` | INT UNSIGNED | FK para tabela `clientes` (NEW table) |
| 3 | **Data de Instalação** | `data_instalacao_v2` | DATE | Quando o equipamento foi instalado |
| 4 | **Observação da Fase de Teste** | `observacao_fase_teste` | LONGTEXT | Anotações durante os testes |
| 5 | **Produto Atendeu Expectativas?** | `produto_atendeu_expectativas` | ENUM('sim','nao') | Resposta direta |
| 6 | **Motivo** *(condicional)* | `motivo_nao_atendeu` | LONGTEXT | Apenas se resposta = 'nao' |
| 7 | **Data Finalização do Teste** | `data_finalizacao_teste` | DATETIME | AUTO-FILLED quando finaliza |
| 8 | **Parecer Final Técnico** | `parecer_final_tecnico` | LONGTEXT | OBRIGATÓRIO (>=20 chars) |

**Tabela que será expandida:** `homologacoes`

**Campos adicionais de auditoria:**
- `finalizado_por` - INT UNSIGNED (FK para users)
- `finalizado_at` - DATETIME

---

## 📋 LOGS DE MOVIMENTAÇÃO (Registrada automaticamente)

| # | 🎯 INFORMAÇÃO | 📊 COLUNA BD | TIPO | OBS. |
|---|-----------|------|------|------|
| 1 | ID do Log | `id` | INT UNSIGNED | Auto increment |
| 2 | Qual homologação? | `homologacao_id` | INT UNSIGNED | FK para homologacoes |
| 3 | Status ANTERIOR | `status_antigo` | VARCHAR(50) | Ex: 'em_analise' |
| 4 | Status NOVO | `status_novo` | VARCHAR(50) | Ex: 'em_homologacao' |
| 5 | Quem moveu? | `usuario_id` | INT UNSIGNED | FK para users |
| 6 | Nome do usuário | `usuario_nome` | VARCHAR(255) | Redundado para relatórios (snapshot) |
| 7 | QUANDO? | `data_movimentacao` | DATETIME | NOW() automático |
| 8 | Observação | `observacao` | LONGTEXT | Opcional - por que moveu? |

**Tabela nova:** `homologacoes_movimentacao`

**Quando registrar:**
- ✅ Qualquer mudança de status (via drag-drop ou button)
- ✅ Qualquer edição de PARTE 2
- ✅ Quando finaliza homologação
- ✅ Quando deleta (se permitido)

---

## 👥 CLIENTES (Nova tabela)

| # | 🎯 CAMPO | 📊 COLUNA BD | TIPO | OBS. |
|---|-----------|------|------|------|
| 1 | ID | `id` | INT UNSIGNED | Auto increment |
| 2 | Nome | `nome_cliente` | VARCHAR(255) | Índice UNIQUE |
| 3 | CNPJ | `cnpj` | VARCHAR(20) | Índice UNIQUE |
| 4 | Email | `email` | VARCHAR(255) | Para notificações |
| 5 | Telefone | `telefone` | VARCHAR(20) | Contato direto |
| 6 | Endereço | `endereco` | VARCHAR(500) | Completo |
| 7 | Cidade | `cidade` | VARCHAR(100) | Para filtros |
| 8 | Estado | `estado` | CHAR(2) | UF: SP, RJ, MG... |
| 9 | Status | `status` | ENUM('ativo','inativo') | Ativo por padrão |
| 10 | Criado em | `created_at` | DATETIME | Auto |
| 11 | Atualizado em | `updated_at` | DATETIME | Auto |

**Tabela nova:** `clientes`

**Índices especiais:**
- FULLTEXT index em `nome_cliente` para busca por substring
- Índice em `cnpj` para busca exata

---

## ✅ VALIDAÇÕES NO FORMULÁRIO

### **PARTE 1 - Validações obrigatórias:**
```javascript
// Cliente-side (JavaScript antes de submit)
if (!cod_referencia.trim()) error("Cód. Referência é obrigatório");
if (!departamento_id) error("Departamento é obrigatório");
if (!responsaveis.length) error("Selecione pelo menos uma pessoa responsável");
if (!descricao.trim()) error("Descrição obrigatória");
if (!data_vencimento) error("Data de vencimento obrigatória");

// Servidor-side (PHP - sempre validar)
if (empty($cod_referencia) || ...
```

### **PARTE 2 - Validações obrigatórias:**
```javascript
// Apenas parecer é obrigatório para FINALIZAR
if (status_novo === 'finalizado' || tipo_click === 'finalizar') {
    if (!parecer_final_tecnico.trim()) 
        error("Parecer Final é obrigatório");
    if (parecer_final_tecnico.length < 20) 
        error("Parecer deve ter no mínimo 20 caracteres");
}

// Se tipo='cliente', cliente é obrigatório
if (tipo_homologacao_v2 === 'cliente' && !cliente_id) 
    error("Selecione um cliente");

// Se atendeu='nao', motivo é obrigatório
if (produto_atendeu_expectativas === 'nao' && !motivo_nao_atendeu.trim()) 
    error("Explique o motivo da rejeição");
```

---

## 🔄 FLUXO DE SALVAMENTO

### **1️⃣ Ao CRIAR (POST /homologacoes/store)**

```php
// 1. Inserir em homologacoes (PARTE 1)
INSERT INTO homologacoes (
    cod_referencia,           // Valor do form
    departamento_id,          // Valor do form
    descricao,                // Valor do form
    data_vencimento,          // Valor do form
    dias_avisar_antecedencia, // Valor do form
    notificar_logistica,      // Valor do form (checkbox)
    status,                   // 'aguardando_recebimento'
    created_by,               // $_SESSION['user_id']
    created_at,               // NOW()
    updated_at                // NOW()
) VALUES (...)
RETURNING id AS homologacao_id; // Pega o ID gerado

// 2. Inserir responsáveis em homologacoes_responsaveis (PARTE 1 - Relação)
FOR EACH responsavel_id IN responsaveis_array {
    INSERT INTO homologacoes_responsaveis (
        homologacao_id,    // Gerado acima
        user_id,           // responsavel_id do loop
        email              // Buscar de users table
    ) VALUES (...)
}

// 3. Registrar movimento inicial em homologacoes_movimentacao (LOG)
INSERT INTO homologacoes_movimentacao (
    homologacao_id,      // Gerado
    status_antigo,       // NULL ou 'draft'
    status_novo,         // 'aguardando_recebimento'
    usuario_id,          // $_SESSION['user_id']
    usuario_nome,        // $_SESSION['user_name']
    data_movimentacao,   // NOW()
    observacao          // "Homologação criada"
) VALUES (...)

// 4. Enviar EMAILS + NOTIFICAÇÕES BELL
FOR EACH responsavel_id IN responsaveis_array {
    enviarEmail(user.email, 
        "Nova homologação: {cod_referencia} atribuída a você"
    )
    registrarNotificacaoBell(user_id, 
        "Você foi atribuído na homologação {cod_referencia}"
    )
}

// 5. Return JSON
return json_encode([
    'success' => true,
    'homologacao_id' => $id,
    'message' => 'Homologação criada com sucesso'
]);
```

### **2️⃣ Ao EDITAR PARTE 2 (PUT /homologacoes/{id}/update-part2)**

```php
// 1. Atualizar campos PARTE 2 em homologacoes
UPDATE homologacoes SET
    tipo_homologacao_v2 = $_POST['tipo'],           // Se informado
    cliente_id = $_POST['cliente_id'],               // Se tipo='cliente'
    data_instalacao_v2 = $_POST['data_instalacao'], // Se informado
    observacao_fase_teste = $_POST['observacao'],   // Se informado
    produto_atendeu_expectativas = $_POST['atendeu'], // Se informado
    motivo_nao_atendeu = $_POST['motivo'],          // Se atendeu='nao'
    // Não tocar em PARTE 1 nunca!
    updated_at = NOW()
WHERE id = $homologacao_id;

// 2. Registrar movimento (se mudou status)
IF (incoming_status != current_status) {
    INSERT INTO homologacoes_movimentacao (
        homologacao_id,
        status_antigo,      // current_status
        status_novo,        // incoming_status
        usuario_id,
        usuario_nome,
        data_movimentacao,
        observacao          // $_POST['obs'] se fornecido
    ) VALUES (...)
}

// 3. Se finalizando (parecer preenchido + status=finalized)
IF (action === 'finalize' && !empty(parecer)) {
    UPDATE homologacoes SET
        parecer_final_tecnico = $_POST['parecer'],
        data_finalizacao_teste = NOW(),
        finalizado_por = $_SESSION['user_id'],
        finalizado_at = NOW(),
        status = 'finalizado'
    WHERE id = $homologacao_id;
    
    // Enviar email para ADMINS + CRIADOR
    $admins = query("SELECT * FROM users WHERE role='admin'");
    $criador = query("SELECT email FROM users WHERE id=$created_by");
    
    FOR EACH admin {
        enviarEmail(admin.email, 
            "Homologação {cod_ref} foi finalizada com parecer"
        )
    }
    enviarEmail(criador.email,
        "Its homologação que você criou foi finalizada"
    )
}

// 4. Return JSON
return json_encode([
    'success' => true,
    'message' => 'Atualizado com sucesso'
]);
```

### **3️⃣ Ao MOVER CARD (PUT /homologacoes/{id}/move)**

```php
// 1. Obter status atual
$current = query("SELECT status FROM homologacoes WHERE id = $id")[0];

// 2. Atualizar status
UPDATE homologacoes SET
    status = $_POST['novo_status'],
    updated_at = NOW()
WHERE id = $id;

// 3. Registrar movimento OBRIGATORIAMENTE
INSERT INTO homologacoes_movimentacao (
    homologacao_id = $id,
    status_antigo = $current->status,
    status_novo = $_POST['novo_status'],
    usuario_id = $_SESSION['user_id'],
    usuario_nome = $_SESSION['user_name'],
    data_movimentacao = NOW(),
    observacao = $_POST['observacao'] ?? 'Movido via kanban'
) VALUES (...)

// 4. Return JSON
return json_encode([
    'success' => true,
    'novo_status' => $_POST['novo_status'],
    'hora' => date('d/m/Y H:i')
]);
```

---

## 🔍 CONSULTAS ÚTEIS PARA O BANCO

### **Buscar uma homologação com TUDO**
```sql
SELECT 
    h.*,
    d.nome_departamento,
    c.nome_cliente,
    u_creator.name as criado_por,
    u_finalizador.name as finalizado_por
FROM homologacoes h
LEFT JOIN departamentos d ON h.departamento_id = d.id
LEFT JOIN clientes c ON h.cliente_id = c.id
LEFT JOIN users u_creator ON h.created_by = u_creator.id
LEFT JOIN users u_finalizador ON h.finalizado_por = u_finalizador.id
WHERE h.id = 123;
```

### **Ver todos responsáveis de uma homologação**
```sql
SELECT 
    hr.user_id,
    u.name,
    u.email,
    hr.email as email_registered
FROM homologacoes_responsaveis hr
LEFT JOIN users u ON hr.user_id = u.id
WHERE hr.homologacao_id = 123;
```

### **Ver histórico de movimentação**
```sql
SELECT * FROM homologacoes_movimentacao
WHERE homologacao_id = 123
ORDER BY data_movimentacao DESC;
```

### **Buscar clientes com FULLTEXT**
```sql
SELECT * FROM clientes
WHERE MATCH(nome_cliente) AGAINST('Empresa ABC' IN BOOLEAN MODE)
AND status = 'ativo'
ORDER BY nome_cliente ASC;
```

### **Ver homologações que vão vencer em X dias**
```sql
SELECT 
    h.id,
    h.cod_referencia,
    h.data_vencimento,
    DATEDIFF(h.data_vencimento, CURDATE()) as dias_restantes
FROM homologacoes h
WHERE DATEDIFF(h.data_vencimento, CURDATE()) <= h.dias_avisar_antecedencia
AND DATEDIFF(h.data_vencimento, CURDATE()) > 0
AND h.notificar_logistica = 1
ORDER BY h.data_vencimento ASC;
```

---

## 📋 CHECKLIST DE COLUNNAS POR MIGRATION

### **Migration 1: CREATE TABLE clientes**
```
✅ id
✅ nome_cliente
✅ cnpj
✅ email
✅ telefone
✅ endereco
✅ cidade
✅ estado
✅ status (ENUM)
✅ created_at
✅ updated_at
```

### **Migration 2: ALTER TABLE homologacoes (Add columns)**
```
NEW:
✅ dias_avisar_antecedencia (INT, default 7)
✅ tipo_homologacao_v2 (ENUM)
✅ cliente_id (INT UNSIGNED, FK)
✅ data_instalacao_v2 (DATE, nullable)
✅ observacao_fase_teste (LONGTEXT)
✅ produto_atendeu_expectativas (ENUM)
✅ data_finalizacao_teste (DATETIME, nullable)
✅ parecer_final_tecnico (LONGTEXT)
✅ finalizado_por (INT UNSIGNED, nullable, FK)
✅ finalizado_at (DATETIME, nullable)
```

### **Migration 3: CREATE TABLE homologacoes_movimentacao**
```
✅ id
✅ homologacao_id (FK)
✅ status_antigo
✅ status_novo
✅ usuario_id (FK)
✅ usuario_nome
✅ data_movimentacao
✅ observacao
```

---

✅ Documento pronto para implementação!

