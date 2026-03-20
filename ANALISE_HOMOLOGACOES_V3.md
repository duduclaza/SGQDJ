# 📋 ANÁLISE COMPLETA - REESTRUTURAÇÃO MÓDULO HOMOLOGAÇÕES v3.0

**Data:** 20/03/2026  
**Status:** 🔍 Análise Detalhada  
**Versão Anterior:** 2.0 (Tutorial + Responsáveis Granulares)

---

## 🎯 VISÃO GERAL DAS MUDANÇAS

O módulo será reestruturado em **2 SEÇÕES PRINCIPAIS** com permissões diferenciadas:

```
┌─────────────────────────────────────────────┐
│ FORMULÁRIO PARTE 1: CRIAÇÃO                 │
│ (Read-only após criação)                    │
│ Apenas ADMINS + COMPRAS podem criar         │
├─────────────────────────────────────────────┤
│ • Cód. Referência                           │
│ • Departamento Responsável (Funil)          │
│ • Pessoas Responsáveis                      │
│ • Descrição                                 │
│ • Data de Vencimento                        │
│ • Avisar X dias antes                       │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ FORMULÁRIO PARTE 2: DETALHES & PROGRESSO    │
│ (Editável em TODAS as etapas)               │
│ Visível para TODOS na homologação           │
├─────────────────────────────────────────────┤
│ • Tipo: Interna / Em Cliente                │
│ • Cliente (select com busca)                │
│ • Data Instalação                           │
│ • Observação                                │
│ • Produto Atendeu Expectativas?             │
│ • Data Finalização Teste                    │
│ • Parecer Final Técnico (obrigatório)       │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ LOGS DE MOVIMENTAÇÃO (READ-ONLY)            │
│ Quem, Quando, Para Onde                     │
└─────────────────────────────────────────────┘
```

---

## 📊 ESTRUTURA DO BANCO DE DADOS

### ✅ TABELAS EXISTENTES QUE SERÃO MODIFICADAS:

#### **1. `homologacoes` (AMPLIAÇÃO)**

**Colunas a ADICIONAR:**

```sql
-- Parte 1: Já existem
-- cod_referencia ✓
-- descricao ✓
-- departamento_resp_id ✓ (será renomeado)
-- created_by ✓
-- created_at ✓
-- data_vencimento ✓

-- NOVOS CAMPOS A ADICIONAR:
dias_avisar_antecedencia     INT DEFAULT 7          -- Avisar X dias antes
tipo_homologacao            ENUM('interna','cliente') NULL
cliente_id                  INT UNSIGNED NULL       -- FK para clientes
observacao_fase_teste       LONGTEXT NULL           -- Observação durante testes
data_finalizacao_teste      DATETIME NULL           -- Data fim do teste
produto_atendeu_expectativas ENUM('sim','nao') NULL -- Sim ou Não
parecer_final_tecnico       LONGTEXT NULL           -- Obrigatório ao finalizar
finalizado_por              INT UNSIGNED NULL       -- Quem finalizou
finalizado_at               DATETIME NULL           -- Quando finalizou
```

**Total de novas colunas:** 9

---

#### **2. `clientes` (NOVA TABELA - Se não existir)**

Preciso criar para suportar a seleção de clientes:

```sql
CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_cliente VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20) UNIQUE NULL,
    email VARCHAR(255) NULL,
    telefone VARCHAR(20) NULL,
    endereco TEXT NULL,
    cidade VARCHAR(100) NULL,
    estado VARCHAR(2) NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nome (nome_cliente),
    INDEX idx_status (status)
);
```

---

#### **3. `homologacoes_movimentacao` (NOVA TABELA)**

Para rastrear movimentações de cards (quem moveu, quando, para onde):

```sql
CREATE TABLE IF NOT EXISTS homologacoes_movimentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    homologacao_id INT NOT NULL,
    status_antigo VARCHAR(50) NOT NULL,
    status_novo VARCHAR(50) NOT NULL,
    usuario_id INT NOT NULL,
    usuario_nome VARCHAR(255) NOT NULL,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observacao TEXT NULL,
    
    INDEX idx_homologacao (homologacao_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_data (data_movimentacao),
    
    FOREIGN KEY (homologacao_id) REFERENCES homologacoes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

#### **4. `homologacoes_responsaveis` (JÁ EXISTE)**

Hash temos, apenas confirmar que está funcionando.

---

### 🔄 FLUXO DE DADOS

```
1. CRIAÇÃO (Parts 1 ONLY)
   └─ Admin/Compras preenche Parts 1
   └─ Sistema salva e BLOQUEIA Parts 1 (read-only)
   └─ Notifica responsáveis via email + sininho

2. PROGRESSO (Parts 2 + Logs)
   └─ Responsáveis editam Parts 2
   └─ Ao mover card → registra em movimentacao
   └─ Histórico atualiza automaticamente
   └─ Todos veem Part 1 (read-only) + Part 2 (dados em tempo real) + Logs

3. FINALIZAÇÃO
   └─ Preenche "Parecer Final Técnico" (obrigatório)
   └─ Clica "Finalizar Homologação"
   └─ Sistema envia email para todos ADMINS + CRIADOR
   └─ Card move para "Aprovado" ou "Reprovado"
```

---

## 🔐 MATRIZ DE PERMISSÕES

| Ação | Admin | Criador | Responsável | Outros |
|------|-------|---------|-------------|--------|
| **Criar Homologação** | ✅ | ❌ | ❌ | ❌ |
| **Ver Part 1** | ✅ | ✅ | ✅ | ✅ |
| **Editar Part 1** | ❌ | ❌ | ❌ | ❌ |
| **Ver Part 2** | ✅ | ✅ | ✅ | ✅ |
| **Editar Part 2** | ✅ | ✅ | ✅ | ❌ |
| **Mover Card** | ✅ | ✅ | ✅ | ❌ |
| **Excluir Card** | ✅ | ✅ | ❌ | ❌ |
| **Atualizar Card** | ✅ | ✅ | ✅ | ❌ |
| **Ver Logs** | ✅ | ✅ | ✅ | ✅ |

---

## 📝 DETALHES DOS CAMPOS PARTE 2

### **Tipo de Homologação**
- Radio: Interna / Em Cliente
- Se "Em Cliente" → Campo "Cliente" aparece (obrigatório)

### **Cliente**
- Select com busca (autocomplete)
- Busca por: nome, CNPJ
- Mostra: nome + CNPJ
- Apenas preenchido se tipo = 'cliente'

### **Data Instalação**
- Input date
- Opcional
- Mostra em relatório

### **Observação**
- Textarea
- Espaço para notas durante testes
- Opcional

### **Produto Atendeu Expectativas?**
- Sim / Não
- Obrigatório antes de finalizar
- Se "Não" → pode preencher observação específica

### **Data Finalização Teste**
- Input datetime
- Auto-preenche com "agora" ao finalizar
- Editável

### **Parecer Final Técnico**
- Textarea OBRIGATÓRIO
- Deve ser preenchido ANTES de clicar em "Finalizar"
- Validação frontend + backend
- Mínimo 20 caracteres recomendado

---

## 📧 FLUXO DE EMAIL - FINALIZAÇÃO

Quando a homologação é finalizada:

```
À: Lista de ADMINS + Email do criador
CC: -
Assunto: "🎉 Homologação #{id} - {cod_referencia} FINALIZADA"

Body:
- Código Referência
- Cliente (se aplicável)
- Status Final (Aprovado/Reprovado)
- Parecer Técnico (primeiras 500 chars)
- Link para visualizar detalhes
- Data/Hora de finalização
```

---

## 🔄 LOGS DE MOVIMENTAÇÃO

### Estrutura exibida no card:

```
┌─ HISTÓRICO DE MOVIMENTAÇÃO
│
├─ 2026-03-20 14:35 | João Silva → Em Análise
│  └─ "Iniciando testes do equipamento"
│
├─ 2026-03-20 10:15 | Maria Santos → Recebido
│  └─ "Material chegou em bom estado"
│
└─ 2026-03-20 09:00 | Admin Sistema → Aguardando Recebimento
   └─ "Homologação criada"
```

---

## 🛠️ RESUMO DAS QUERIES NECESSÁRIAS

### 1. **Criar Tabela de Clientes**
```sql
-- database/migrations/2026_03_20_create_clientes.sql
CREATE TABLE IF NOT EXISTS clientes (...)
```

### 2. **Alterar Tabela homologacoes**
```sql
-- database/migrations/2026_03_20_expand_homologacoes.sql
ALTER TABLE homologacoes ADD COLUMN (...)
```

### 3. **Criar Tabela de Movimentação**
```sql
-- database/migrations/2026_03_20_create_movimentacao.sql
CREATE TABLE homologacoes_movimentacao (...)
```

### 4. **Adicionar Foreign Key Cliente**
```sql
ALTER TABLE homologacoes
ADD CONSTRAINT fk_cliente 
FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL;
```

---

## 💾 PRÓXIMAS ETAPAS

### FASE 1: Banco de Dados
1. Validar se tabela `clientes` já existe
2. Gerar migrations SQL
3. Executar no BD

### FASE 2: Backend (Controller)
1. Métodos para CRUD de clientes
2. Endpoint para buscar clientes (autocomplete)
3. Validações de permissões (admin/criador/responsável)
4. Lógica de envio de email ao finalizar
5. Registro automático de movimentação

### FASE 3: Frontend (View)
1. Separar Part 1 (read-only com ícones 🔒)
2. Criar Part 2 editável com campos novos
3. Implementar select de clientes com autocomplete
4. Adicionar logs de movimentação
5. Botão "Finalizar Homologação" com confirmação

### FASE 4: JavaScript
1. Toggle para tipo homologação
2. Controle de edição por permissão
3. Validação do "Parecer Final Técnico"
4. Confirmação ao finalizar

---

## ✅ CHECKLIST

- [ ] Validar estrutura de clientes
- [ ] Gerar queries de migration
- [ ] Executar migrations no BD
- [ ] Atualizar controller
- [ ] Atualizar view parte 1 (read-only)
- [ ] Atualizar view parte 2 (editável)
- [ ] Implementar select de clientes
- [ ] Adicionar logs de movimentação
- [ ] Testar fluxo completo
- [ ] Testar permissões
- [ ] Testar email de finalização

---

## 📞 PRÓXIMO PASSO

Aguardo confirmação de:
1. ✅ **Esta análise faz sentido?**
2. ✅ **Tabela `clientes` já existe no seu BD?**
3. ✅ **Devo gerar as queries SQL agora?**

Após confirmação, montarei as **3 queries SQL** + **Modificações no Controller** + **Alterações na View**.

