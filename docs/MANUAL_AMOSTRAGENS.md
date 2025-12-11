# 📋 Manual de Amostragens 2.0 - Novo Fluxo de Registro

## 🎯 Visão Geral

O módulo de Amostragens foi redesenhado para ser mais intuitivo e evitar erros de preenchimento. Agora você preenche primeiro os dados do lote recebido e, no final do formulário, seleciona o **Status Final** clicando em um dos 4 botões.

---

## 📝 Como Preencher o Formulário

### **Passo 1: Dados Básicos do Lote**

Preencha as informações iniciais:
- **Número da NF** - Número da nota fiscal
- **Anexo da NF** - PDF ou foto da nota (opcional)
- **Tipo de Produto** - Toner, Peça ou Máquina
- **Código do Produto** - Selecione o produto cadastrado
- **Quantidade Recebida** - Total de unidades recebidas no lote
- **Fornecedor** - Fornecedor que enviou o lote
- **Responsáveis pelo Teste** - Quem realizou a amostragem
- **Observação** - Comentários adicionais (opcional)
- **Evidências** - Fotos da amostragem (opcional)

### **Passo 2: Status Final (Decisão)**

No final do formulário, você verá 4 botões grandes. Clique no botão correspondente ao resultado da amostragem:

---

## 🔘 Os 4 Status Disponíveis

### ⏳ **PENDENTE**
- **Quando usar:** O lote foi recebido mas ainda não foi analisado.
- **Campos exigidos:** Nenhum campo adicional.
- **Resultado:** 
  - Quantidade Testada: 0
  - Quantidade Aprovada: 0
  - Quantidade Reprovada: 0
- **Visual na listagem:** Linha aparece em **amarelo** aguardando decisão.

---

### ✅ **APROVADO**
- **Quando usar:** Todo o lote foi aprovado sem reprovações.
- **Campos exigidos:** Informar a **Quantidade Testada**.
- **Resultado automático:**
  - Quantidade Aprovada = Quantidade Recebida
  - Quantidade Reprovada = 0
- **Exemplo:** Recebeu 100 toners, testou 50, todos OK → 100 aprovados.

---

### 🔶 **APROVADO PARCIALMENTE**
- **Quando usar:** Houve reprovações durante o teste, mas parte foi aprovada.
- **Campos exigidos:**
  - **Quantidade Testada** - Quantos do lote foram realmente testados
  - **Quantidade Aprovada** - Quantos dos testados foram aprovados
- **Cálculos automáticos:**
  - **Quantidade Reprovada** = Testada - Aprovada
  - **Não Testadas** = Recebida - Testada (são consideradas aprovadas)
  - **Total Aprovadas** = Aprovadas do Teste + Não Testadas
  - **Total Reprovadas** = Reprovadas do Teste

#### 📊 Exemplo Prático:
```
Recebidas: 100 toners
Testadas: 50
Aprovadas no teste: 25
Reprovadas no teste: 25 (calculado: 50.25)

Resultado Final:
├── Não Testadas: 50 (100 - 50) → consideradas aprovadas
├── Total Aprovadas: 75 (25 + 50)
└── Total Reprovadas: 25
```

---

### ❌ **REPROVADO**
- **Quando usar:** Todo o lote foi reprovado.
- **Campos exigidos:** Informar a **Quantidade Testada**.
- **Resultado automático:**
  - Quantidade Aprovada = 0
  - Quantidade Reprovada = Quantidade Recebida
- **Exemplo:** Recebeu 100 toners, testou 30, todos com defeito → 100 reprovados.

---

## ✅ Validações do Sistema

O sistema impede automaticamente erros comuns:

| Validação | Comportamento |
|-----------|---------------|
| Testada > Recebida | Corrigido automaticamente para valor máximo |
| Aprovada > Testada | Corrigido automaticamente para valor máximo |
| Campos obrigatórios vazios | Alerta antes de salvar |
| Status não selecionado | Alerta para selecionar um dos 4 botões |

---

## 🏷️ Indicadores na Listagem

Após salvar, a amostragem aparece na listagem com indicadores visuais:

| Indicador | Significado |
|-----------|-------------|
| ✓ (verde) | Lote 100% aprovado |
| **PARCIAL** (amarelo) | Aprovação parcial |
| **LOTE** (vermelho) | Lote 100% reprovado |
| Linha amarela | Status Pendente |

---

## 🔄 Resumo do Fluxo

```
1. Preencher dados do lote (NF, produto, quantidade, etc.)
                    ↓
2. Anexar evidências (fotos) se necessário
                    ↓
3. Clicar no botão de STATUS FINAL:
   ┌──────────┬──────────┬──────────┬──────────┐
   │ Pendente │ Aprovado │ Parcial  │Reprovado │
   └──────────┴──────────┴──────────┴──────────┘
                    ↓
4. Preencher campos adicionais (se aparecerem)
                    ↓
5. Clicar em "💾 Salvar Amostragem"
```

---

## ❓ Dúvidas Frequentes

**P: Posso editar uma amostragem depois de salvar?**
R: Sim, clique no botão de edição na listagem.

**P: O que acontece com as unidades não testadas?**
R: No status "Aprovado Parcialmente", as unidades não testadas são automaticamente consideradas aprovadas.

**P: Preciso informar quantidade testada mesmo quando aprovo tudo?**
R: Sim, para manter o histórico de quantas unidades foram efetivamente verificadas.

---

*Última atualização: Dezembro/2024*
