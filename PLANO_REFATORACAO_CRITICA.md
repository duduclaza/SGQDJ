# 🔴 PLANO DE REFATORAÇÃO CRÍTICA - SGQ OTI DJ

**Data de Início:** 04/12/2025  
**Status:** 🚧 EM ANDAMENTO  
**Prioridade:** CRÍTICA

---

## 🎯 OBJETIVO

Resolver os **5 problemas críticos** identificados na análise:

1. ✅ **Debug em produção** - Remover `?debug=1`
2. ✅ **PHPUnit** - Configurar testes automatizados
3. 🚧 **index.php (708 linhas)** - Refatorar para arquivos separados
4. ⏳ **EmailService (108KB)** - Modularizar por tipo
5. ⏳ **Controllers grandes** - Quebrar em services

---

## 📊 PROGRESSO GERAL

```
[████░░░░░░] 20% - 1/5 tarefas concluídas
```

**Tempo Estimado Total:** 8-10 horas  
**Tempo Gasto:** 0 horas  
**Tempo Restante:** 8-10 horas

---

## 🔴 TAREFA 1: REMOVER DEBUG MODE EM PRODUÇÃO

**Status:** ⏳ A FAZER  
**Prioridade:** 🔴 CRÍTICA  
**Esforço:** 15 minutos  
**Impacto:** Alto - Segurança

### Problema Atual
```php
// public/index.php, linha 26-28
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    $isDebug = true;  // ⚠️ PERIGO EM PRODUÇÃO!
}
```

### Solução Implementada
- [ ] Criar middleware de debug com whitelist de IPs
- [ ] Remover possibilidade de ativação via query string
- [ ] Testar em ambiente de desenvolvimento
- [ ] Deploy em produção

### Arquivos Afetados
- `public/index.php`

### Checklist
- [ ] Código implementado
- [ ] Testado localmente
- [ ] Revisado por peer
- [ ] Deploy em produção
- [ ] Validado em produção

---

## 🔴 TAREFA 2: CONFIGURAR PHPUNIT

**Status:** ⏳ A FAZER  
**Prioridade:** 🔴 CRÍTICA  
**Esforço:** 30 minutos  
**Impacto:** Alto - Qualidade

### Estrutura de Testes
```
tests/
├── Unit/
│   ├── Services/
│   │   ├── PermissionServiceTest.php
│   │   └── EmailServiceTest.php
│   ├── Core/
│   │   └── RouterTest.php
│   └── Middleware/
│       └── PermissionMiddlewareTest.php
├── Feature/
│   ├── Auth/
│   │   └── LoginTest.php
│   └── Homologacoes/
│       └── HomologacoesTest.php
└── bootstrap.php
```

### Checklist
- [ ] Instalar PHPUnit via Composer
- [ ] Criar phpunit.xml
- [ ] Criar estrutura de diretórios
- [ ] Criar bootstrap.php
- [ ] Escrever 5 testes básicos
- [ ] Configurar CI (futuro)

---

## 🔴 TAREFA 3: REFATORAR INDEX.PHP

**Status:** ⏳ A FAZER  
**Prioridade:** 🔴 CRÍTICA  
**Esforço:** 2-3 horas  
**Impacto:** Alto - Manutenibilidade

### Estrutura Nova
```
src/Routes/
├── RouteServiceProvider.php
├── web.php                    # Rotas públicas
├── api.php                    # APIs
├── admin.php                  # Admin
└── modules/
    ├── auth.php
    ├── toners.php
    ├── homologacoes.php
    ├── pops-its.php
    ├── fluxogramas.php
    ├── garantias.php
    ├── amostragens.php
    ├── nps.php
    └── ... (15+ módulos)
```

### Fases de Implementação
- [ ] **Fase 1:** Criar estrutura de diretórios
- [ ] **Fase 2:** Criar RouteServiceProvider
- [ ] **Fase 3:** Migrar rotas de autenticação (auth.php)
- [ ] **Fase 4:** Migrar rotas de módulo por módulo
- [ ] **Fase 5:** Refatorar index.php para usar provider
- [ ] **Fase 6:** Testar todas as rotas

### Checklist de Migração

#### Módulos a Migrar (20+)
- [ ] auth.php - Autenticação e registro
- [ ] toners.php - Toners cadastro e retornados
- [ ] homologacoes.php - Homologações Kanban
- [ ] pops-its.php - POPs e ITs
- [ ] fluxogramas.php - Fluxogramas
- [ ] garantias.php - Garantias
- [ ] amostragens.php - Amostragens 2.0
- [ ] nps.php - Net Promoter Score
- [ ] controle-rc.php - Controle de RC
- [ ] controle-descartes.php - Controle de Descartes
- [ ] nao-conformidades.php - Não Conformidades
- [ ] 5w2h.php - Planos de Ação
- [ ] auditorias.php - Auditorias
- [ ] fmea.php - FMEA
- [ ] certificados.php - Certificados
- [ ] registros.php - Registros gerais
- [ ] suporte.php - Suporte
- [ ] area-tecnica.php - Área Técnica
- [ ] admin.php - Rotas admin
- [ ] api.php - APIs externas

---

## 🔴 TAREFA 4: MODULARIZAR EMAILSERVICE

**Status:** ⏳ A FAZER  
**Prioridade:** 🔴 CRÍTICA  
**Esforço:** 2-3 horas  
**Impacto:** Alto - Manutenibilidade

### Estrutura Nova
```
src/Services/Email/
├── EmailService.php              # Service principal
├── BaseMailer.php                # Classe base
├── Templates/
│   ├── AuthMailer.php           # Emails de autenticação
│   ├── NotificationMailer.php   # Notificações gerais
│   ├── ApprovalMailer.php       # Aprovações/Reprovações
│   ├── AlertMailer.php          # Alertas de sistema
│   └── PasswordResetMailer.php  # Recuperação de senha
└── config/
    └── templates.php             # Templates HTML
```

### Tipos de Email Identificados
1. **Autenticação**
   - Credenciais de novo usuário
   - Confirmação de cadastro

2. **Notificações**
   - POPs/ITs aprovados/reprovados
   - Fluxogramas aprovados/reprovados
   - Homologações atualizadas

3. **Alertas**
   - Controle de descartes
   - Prazos vencendo

4. **Recuperação de Senha**
   - Código de verificação
   - Senha redefinida

### Checklist
- [ ] Criar estrutura de diretórios
- [ ] Criar BaseMailer com lógica comum
- [ ] Criar AuthMailer
- [ ] Criar NotificationMailer
- [ ] Criar ApprovalMailer
- [ ] Criar AlertMailer
- [ ] Criar PasswordResetMailer
- [ ] Refatorar controllers para usar novos mailers
- [ ] Testar envio de cada tipo

---

## 🔴 TAREFA 5: QUEBRAR CONTROLLERS GRANDES

**Status:** ⏳ A FAZER  
**Prioridade:** 🔴 CRÍTICA  
**Esforço:** 3-4 horas  
**Impacto:** Alto - Manutenibilidade

### Controllers Críticos

#### 1. AdminController (133 KB)
**Quebrar em:**
```
src/Controllers/Admin/
├── DashboardController.php       # Dashboard e métricas
├── UsersController.php           # CRUD de usuários
├── ProfilesController.php        # Gestão de perfis
├── PermissionsController.php     # Permissões
├── InvitationsController.php     # Convites
└── DiagnosticsController.php     # Diagnósticos
```

#### 2. PopItsController (113 KB)
**Quebrar em:**
```
src/Controllers/PopIts/
├── PopItsController.php          # Controller principal
├── TitulosController.php         # Gestão de títulos
├── RegistrosController.php       # Meus registros
├── ApprovalController.php        # Aprovação/Reprovação
├── VisualizacaoController.php    # Visualização
└── SolicitacoesController.php    # Solicitações de exclusão
```

#### 3. FluxogramasController (73 KB)
**Quebrar em:**
```
src/Controllers/Fluxogramas/
├── FluxogramasController.php
├── TitulosController.php
├── RegistrosController.php
└── ApprovalController.php
```

#### 4. GarantiasController (75 KB)
**Criar services:**
```
src/Services/Garantias/
├── GarantiaService.php
├── RequisicaoService.php
└── TicketService.php
```

### Checklist
- [ ] AdminController → Admin/*
- [ ] PopItsController → PopIts/*
- [ ] FluxogramasController → Fluxogramas/*
- [ ] GarantiasController → Services
- [ ] Atualizar rotas
- [ ] Testar todas as funcionalidades

---

## 📈 MÉTRICAS DE SUCESSO

### Antes da Refatoração
| Métrica | Valor Atual |
|---------|-------------|
| Linhas em index.php | 708 |
| Tamanho AdminController | 133 KB |
| Tamanho EmailService | 108 KB |
| Code Coverage | 0% |
| Debug em Produção | ⚠️ Possível |

### Depois da Refatoração (Meta)
| Métrica | Valor Meta |
|---------|------------|
| Linhas em index.php | < 100 |
| Maior controller | < 30 KB |
| EmailService modularizado | < 20 KB/arquivo |
| Code Coverage | > 30% |
| Debug em Produção | ❌ Impossível |

---

## 🚀 ORDEM DE EXECUÇÃO

### Sprint 1 - SEGURANÇA E ESTRUTURA (0.5h)
1. ✅ Remover debug mode (15 min)
2. ✅ Configurar PHPUnit (30 min)

### Sprint 2 - REFATORAÇÃO BASE (3h)
3. 🚧 Refatorar index.php
   - Criar estrutura de rotas
   - Migrar módulo por módulo
   - Testar

### Sprint 3 - MODULARIZAÇÃO (3h)
4. 🚧 Modularizar EmailService
   - Criar mailers especializados
   - Refatorar controllers

### Sprint 4 - CONTROLLERS (4h)
5. 🚧 Quebrar controllers grandes
   - AdminController
   - PopItsController
   - Outros

---

## ⚠️ RISCOS E MITIGAÇÕES

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| Quebrar rotas existentes | Alta | Alto | Testar cada módulo após migração |
| Controllers não encontrados | Média | Alto | Atualizar todas referências |
| Emails pararem de funcionar | Baixa | Alto | Manter EmailService original como fallback |
| Testes falhando | Alta | Baixo | Corrigir incrementalmente |

---

## 📝 NOTAS DE IMPLEMENTAÇÃO

### Boas Práticas
- ✅ Fazer backup antes de cada mudança
- ✅ Testar em ambiente local primeiro
- ✅ Commitar pequenas mudanças incrementais
- ✅ Manter código original comentado (temporariamente)
- ✅ Documentar cada mudança no git

### Comandos Git Recomendados
```bash
# Criar branch de refatoração
git checkout -b refactor/critical-improvements

# Commitar cada tarefa separadamente
git add -p
git commit -m "fix: remover debug mode em produção"
git commit -m "feat: adicionar PHPUnit e testes básicos"
git commit -m "refactor: separar rotas em arquivos modulares"
# ...

# Após testes completos, merge
git checkout main
git merge refactor/critical-improvements
```

---

## 🎯 PRÓXIMOS PASSOS

### Agora (Próxima 1h)
1. ✅ Remover debug mode
2. ✅ Configurar PHPUnit
3. 🚧 Iniciar refatoração de index.php

### Hoje (Próximas 8h)
4. Completar refatoração de rotas
5. Modularizar EmailService
6. Iniciar quebra de AdminController

### Esta Semana
7. Finalizar quebra de todos os controllers
8. Escrever 20+ testes
9. Documentar mudanças
10. Deploy em produção

---

**Última Atualização:** 04/12/2025 19:53  
**Responsável:** Equipe de Desenvolvimento  
**Status:** 🚧 EM PROGRESSO

---

