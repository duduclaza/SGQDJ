# 🎨 MOCKUP VISUAL - FORMULÁRIO PARTE 1 & PARTE 2

**Referência:** Nova estrutura do formulário de homologações

---

## 📋 PARTE 1: CRIAÇÃO (READ-ONLY após criação)

```
┌─────────────────────────────────────────────────────────────┐
│ ✨ NOVA SOLICITAÇÃO DE HOMOLOGAÇÃO                          │
│                                                             │
│ 📋 INFORMAÇÕES BÁSICAS (Read-only após criação) 🔒          │
│                                                             │
│  Cód. Referência *                                          │
│  ┌─────────────────────────────────────────────┐           │
│  │ PROD-001                                    │ 🔒        │
│  └─────────────────────────────────────────────┘           │
│                                                             │
│  Departamento Responsável *                                │
│  ┌─────────────────────────────────────────────┐           │
│  │ ▼ Qualidade                                 │ 🔒        │
│  └─────────────────────────────────────────────┘           │
│  (Apenas determina o funil de roteamento)                  │
│                                                             │
│  Pessoas Responsáveis * (Serão notificadas)                │
│  ┌─────────────────────────────────────────────┐           │
│  │ ✓ João Silva (Qualidade)                    │           │
│  │ ✓ Maria Santos (Qualidade)                  │           │
│  │ ☐ Pedro Costa (Qualidade)                   │           │
│  └─────────────────────────────────────────────┘           │
│  💡 Segure Ctrl+Click para múltiplos                       │
│                                                             │
│  Descrição *                                               │
│  ┌─────────────────────────────────────────────┐           │
│  │ Homologação do equipamento XYZ modelo 2026 │ 🔒        │
│  │ para uso em linhas de produção.             │           │
│  └─────────────────────────────────────────────┘           │
│                                                             │
│  Data de Vencimento *                                      │
│  ┌────────────────┐  Avisar com: ┌──────┐ dias            │
│  │ 31/05/2026     │ 🔒            │ 7    │               │
│  └────────────────┘               └──────┘               │
│                                                             │
│  ✓ Notificar Logística sobre chegada                       │
│                                                             │
│  ─────────────────────────────────────────────────────     │
│  [Cancelar]                          [Criar Homologação]   │
│                                                             │
│  ÍCONES DE STATUS:                                         │
│  🔒 = Read-only (Bloqueado para edição)                    │
│  ✓ = Usuario pode editar                                  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📝 PARTE 2: DETALHES & PROGRESSO (EDITÁVEL em todas as etapas)

```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 DETALHES DA HOMOLOGAÇÃO (Visível sempre, editável)      │
│                                                             │
│                  ─────────────────────────                 │
│                                                             │
│  Tipo de Homologação:                                      │
│  ◉ Interna  ◯ Em Cliente                                   │
│                                                             │
│  [Se "Em Cliente"]                                         │
│  Cliente *                                                 │
│  ┌──────────────────────────────┐                          │
│  │ 🔍 Buscar cliente...         │                          │
│  │                              │                          │
│  │ ► Empresa ABC Ltda (CNPJ AB) │                          │
│  │ ► Cliente XYZ S/A (CNPJ XY)  │                          │
│  │ ► Mais...                    │                          │
│  └──────────────────────────────┘                          │
│  💡 Digite para buscar nome ou CNPJ                        │
│                                                             │
│  Data de Instalação:                                       │
│  ┌────────────────┐                                        │
│  │ 15/04/2026     │                                        │
│  └────────────────┘                                        │
│                                                             │
│  Observação:                                               │
│  ┌──────────────────────────────────┐                      │
│  │ Equipamento em teste na linha 3  │                      │
│  │ Aguardando validação de testes.  │                      │
│  └──────────────────────────────────┘                      │
│                                                             │
│  Produto Atendeu as Expectativas?                          │
│  ◉ Sim  ◯ Não                                              │
│                                                             │
│  [Se "Não" - mostrar campo observação específica]          │
│  Motivo:                                                   │
│  ┌──────────────────────────────────┐                      │
│  │ Produto não apresenta...         │                      │
│  └──────────────────────────────────┘                      │
│                                                             │
│  Data Finalização do Teste:                                │
│  ┌────────────────────────────┐                            │
│  │ 20/04/2026 14:30           │                            │
│  └────────────────────────────┘                            │
│  💡 Preenchida automaticamente ao finalizar                │
│                                                             │
│  Parecer Final Técnico * (OBRIGATÓRIO)                     │
│  ┌──────────────────────────────────┐                      │
│  │ Equipamento aprovado com ressalvas │                    │
│  │ Recomenda-se uso em linha 3 apenas │                    │
│  │ Desempenho: 95% do esperado       │                    │
│  │ Manutenção necessária em 6 meses  │                    │
│  │                                    │                    │
│  │ Assinado: João Silva (Qual.)      │                    │
│  │ Data: 20/04/2026                  │                    │
│  └──────────────────────────────────┘                      │
│  ⚠️ Mínimo 20 caracteres                                   │
│                                                             │
│  ───────────────────────────────────────────              │
│  [Atualizar]  [Mover para...]  [Finalizar Homologação]  │
│                                                             │
│  LEGENDA DE PERMISSÕES:                                    │
│  ✓ = Todos os responsáveis podem editar                   │
│  🔓 = Admins e criador podem movimentar                   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 LOGS DE MOVIMENTAÇÃO (READ-ONLY)

```
┌─────────────────────────────────────────────────────────────┐
│ 📜 HISTÓRICO DE MOVIMENTAÇÃO                               │
│                                                             │
│ ┌───────────────────────────────────────────────────────┐  │
│ │ 2026-04-20 14:35  João Silva                          │  │
│ │ ➜ APROVADO                                            │  │
│ │ "Equipamento aprovado conforme parecer técnico"       │  │
│ └───────────────────────────────────────────────────────┘  │
│                                                             │
│ ┌───────────────────────────────────────────────────────┐  │
│ │ 2026-04-18 10:15  Maria Santos                        │  │
│ │ ➜ EM HOMOLOGAÇÃO                                      │  │
│ │ "Iniciando fase de testes com cliente"                │  │
│ └───────────────────────────────────────────────────────┘  │
│                                                             │
│ ┌───────────────────────────────────────────────────────┐  │
│ │ 2026-04-15 09:30  Pedro Costa                         │  │
│ │ ➜ EM ANÁLISE                                          │  │
│ │ "Análise técnica iniciada"                            │  │
│ └───────────────────────────────────────────────────────┘  │
│                                                             │
│ ┌───────────────────────────────────────────────────────┐  │
│ │ 2026-04-12 08:00  Admin Sistema                       │  │
│ │ ➜ RECEBIDO                                            │  │
│ │ "Material recebido e armazenado"                      │  │
│ └───────────────────────────────────────────────────────┘  │
│                                                             │
│ ┌───────────────────────────────────────────────────────┐  │
│ │ 2026-04-10 16:45  João Silva (Compras)               │  │
│ │ ➜ AGUARDANDO RECEBIMENTO                              │  │
│ │ "Homologação criada - #{1} PROD-001"                 │  │
│ └───────────────────────────────────────────────────────┘  │
│                                                             │
│ 💡 Todos os usuários podem VER este histórico              │
│ 🔒 Apenas sistema registra mudanças automaticamente        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 FLUXO DE INTERAÇÃO COMPLETO

```
1. CRIAR HOMOLOGAÇÃO
   └─ Preenche PARTE 1
   └─ Sistema bloqueia PARTE 1 (READ-ONLY)
   └─ Notifica responsáveis

2. ACOMPANHAMENTO (Responsáveis)
   └─ Editam PARTE 2 conforme progresso
   └─ Movimentam card (sistema registra)
   └─ Veem PARTE 1 (informações de origem)

3. FINALIZAÇÃO
   └─ Preenche "Parecer Final Técnico"
   └─ Clica "Finalizar Homologação"
   └─ Sistema valida Parecer (obrigatório)
   └─ Envia email para: ADMINS + CRIADOR
   └─ Bloqueia card para nova edição (opcional)

4. VISUALIZAÇÃO PERMANENTE
   └─ Qualquer um pode ver:
      ├─ PARTE 1 (origem - read-only)
      ├─ PARTE 2 (progresso - editável por responsáveis)
      └─ LOGS (quem moveu quando)
```

---

## 🔐 MATRIZ DE PERMISSÕES NA UI

```
┌─────────────────────────────────────────────────────────────┐
│ AÇÃO                    │ ADMIN │ CRIADOR │ RESPONSÁVEL │    │
├─────────────────────────────────────────────────────────────┤
│ Criar                   │  ✅   │   ❌    │     ❌      │    │
│ Ver PARTE 1             │  ✅   │   ✅    │     ✅      │    │
│ Editar PARTE 1          │  ❌   │   ❌    │     ❌      │    │
│ Ver PARTE 2             │  ✅   │   ✅    │     ✅      │    │
│ Editar PARTE 2          │  ✅   │   ✅    │     ✅      │    │
│ Mover Card              │  ✅   │   ✅    │     ✅      │    │
│ Excluir Card            │  ✅   │   ✅    │     ❌      │    │
│ Atualizar Card          │  ✅   │   ✅    │     ✅      │    │
│ Finalizar               │  ✅   │   ✅    │     ✅      │    │
│ Ver Logs                │  ✅   │   ✅    │     ✅      │    │
│ Excluir Logs            │  ❌   │   ❌    │     ❌ (*) │    │
│                                                             │
│ (*) = Logs nunca são deletados por segurança              │
└─────────────────────────────────────────────────────────────┘
```

---

## ⚙️ COMPORTAMENTOS ESPECIAIS

### **Tipo de Homologação & Cliente**
```
Se PARTE 1 diz tipo = 'cliente'
└─ PARTE 2 campo "Cliente" é OBRIGATÓRIO
└─ User vê select + search de clientes cadastrados
└─ Se PARTE 1 diz tipo = 'interna'
   └─ PARTE 2 campo "Cliente" fica DESABILITADO
```

### **Parecer Final Técnico**
```
Quando user clica "Finalizar Homologação"
└─ Sistema valida se Parecer está preenchido
└─ Se vazio: mostra erro "Parecer é obrigatório"
└─ Se preenchido: 
   ├─ Salva hora em "data_finalizacao_teste"
   ├─ Registra "finalizado_por" = user_id
   ├─ Registra "finalizado_at" = agora
   └─ Envia email para ADMINS + CRIADOR
```

### **Movimentação de Cards**
```
Quando user arrasta card ou clica "Mover para..."
└─ Sistema registra em homologacoes_movimentacao:
   ├─ homologacao_id
   ├─ status_antigo (de)
   ├─ status_novo (para)
   ├─ usuario_id
   ├─ usuario_nome
   ├─ data_movimentacao (NOW())
   └─ observacao (opcional)
└─ Imediatamente visível no LOGS
```

---

## 📧 EXEMPLO DE EMAIL AO FINALIZAR

```
Subject: 🎉 Homologação #001 - PROD-001 FINALIZADA

─────────────────────────────────────────────

Homologação Finalizada com Sucesso!

Código: PROD-001
Cliente: Empresa ABC Ltda (se aplicável)
Status Final: Aprovado

PARECER TÉCNICO:
"Equipamento aprovado conforme testes realizados...
Recomenda-se uso em linha 3..."

Finalizado em: 20/04/2026 14:35
Por: João Silva (Qualidade)

LINK: https://seu-dominio/homologacoes (card #001)

─────────────────────────────────────────────

Destinatários: TODOS OS ADMINS + Criador da homologação
```

---

Essa é a estrutura visual completa! Cada parte tem seu propósito claro e as permissões garantem segurança sem perder flexibilidade. ✅

