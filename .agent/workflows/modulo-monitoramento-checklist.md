# 📋 CHECKLIST - MÓDULO DE MONITORAMENTO
**Última atualização:** 05/12/2024 - 03:29

---

## 🎯 PROGRESSO GERAL: 60%

```
████████████████░░░░░░░░░░ 60%
```

---

## ✅ FASE 1: FUNDAÇÃO - COMPLETA (100%)

### Banco de Dados
- [x] Criar 8 tabelas no banco de dados
- [x] Inserir dados iniciais (licença + OIDs)
- [x] Testar integridade referencial
- [x] Verificar criação bem-sucedida

### Backend Core
- [x] Criar `MonitoramentoController.php` (Admin)
- [x] Criar `MonitoramentoPortalController.php` (Portal)
- [x] Implementar autenticação do portal
- [x] Implementar controle de licenças
- [x] Implementar sistema de logs

### Testes SNMP
- [x] Criar `teste_snmp.php` (CLI completo)
- [x] Criar `teste_snmp_simples.php` (CLI básico)
- [x] Criar `public/teste_snmp_web.php` (Web completo)
- [x] Criar `public/teste_snmp_simples.php` (Web básico)
- [x] Criar `simulador_impressora_snmp.php` (Simulador)

### Documentação
- [x] Criar planejamento completo em `.agent/workflows/modulo-monitoramento-planejamento.md`

---

## 🚧 FASE 2: PORTAL DO CLIENTE - EM ANDAMENTO (40%)

### Layout e Estrutura
- [x] Criar `views/layouts/portal_layout.php`
- [ ] Criar pasta `views/monitoramento/portal/`

### Páginas do Portal
- [ ] **Login** (`views/monitoramento/portal/login.php`)
  - [ ] Formulário de login
  - [ ] Validação de campos
  - [ ] Mensagens de erro
  - [ ] Link "Esqueci minha senha"

- [ ] **Dashboard** (`views/monitoramento/portal/dashboard.php`)
  - [ ] Cards de resumo (Total impressoras, Online, Alertas)
  - [ ] Lista de impressoras com status
  - [ ] Alertas recentes
  - [ ] Gráfico de evolução (opcional)

- [ ] **Gerenciar Impressoras** (`views/monitoramento/portal/impressoras.php`)
  - [ ] Lista de impressoras cadastradas
  - [ ] Botão "Nova Impressora"
  - [ ] Ações (Editar, Excluir, Testar SNMP)
  - [ ] Indicadores visuais de status

- [ ] **Modal Nova Impressora**
  - [ ] Formulário completo
  - [ ] Campos: Número Série, Modelo, IP, Tipo
  - [ ] Opções de contador (P&B, Color)
  - [ ] Envio automático/manual
  - [ ] Botão "Testar Conexão SNMP"

- [ ] **Modal Trocar Senha** (primeiro acesso)
  - [ ] Formulário de troca de senha
  - [ ] Validação de senha forte
  - [ ] Confirmação de senha

### JavaScript do Portal
- [ ] Criar `views/monitoramento/portal/scripts.js`
  - [ ] Função de login
  - [ ] Função de trocar senha
  - [ ] CRUD de impressoras
  - [ ] Teste de conexão SNMP
  - [ ] Atualização de status em tempo real

---

## ⏳ FASE 3: ADMIN - PENDENTE (20%)

### Interface Admin
- [ ] **Gerenciar Clientes** (`views/monitoramento/index.php`)
  - [ ] Lista de clientes
  - [ ] Botão "Novo Cliente"
  - [ ] Indicador de licença (X/Y clientes)
  - [ ] Ações (Ver Portal, Editar, Desativar)

- [ ] **Modal Novo Cliente**
  - [ ] Formulário completo
  - [ ] Campos: Nome, Email, Telefone, CNPJ
  - [ ] Validação de limite de licença
  - [ ] Envio automático de credenciais

- [ ] **Monitor de Suprimentos** (`views/monitoramento/suprimentos.php`)
  - [ ] Lista de todas as impressoras
  - [ ] Filtros (Cliente, Status, Modelo)
  - [ ] Indicadores visuais de toner
  - [ ] Alertas críticos em destaque

- [ ] **Contadores** (`views/monitoramento/contadores.php`)
  - [ ] Lista de contadores recebidos
  - [ ] Filtros (Cliente, Período, Impressora)
  - [ ] Gráfico de evolução
  - [ ] Exportar para Excel

### Controller Admin
- [ ] Implementar método `suprimentos()`
- [ ] Implementar método `contadores()`
- [ ] Implementar método `editarCliente()`
- [ ] Implementar método `desativarCliente()`

---

## ⏳ FASE 4: AUTOMAÇÃO - PENDENTE (0%)

### Coleta Automática SNMP
- [ ] Criar `src/Services/SnmpCollectorService.php`
  - [ ] Método para coletar dados de uma impressora
  - [ ] Método para processar múltiplas impressoras
  - [ ] Tratamento de erros e timeouts
  - [ ] Atualização de status (online/offline)

### Cron Jobs
- [ ] Criar `cron/coletar_suprimentos.php`
  - [ ] Executar a cada 30 minutos
  - [ ] Coletar níveis de toner
  - [ ] Gerar alertas se necessário

- [ ] Criar `cron/coletar_contadores.php`
  - [ ] Executar diariamente
  - [ ] Verificar impressoras com envio automático
  - [ ] Coletar contadores no dia configurado

### Sistema de Alertas
- [ ] Criar `src/Services/AlertaService.php`
  - [ ] Verificar níveis de toner
  - [ ] Criar alertas automáticos
  - [ ] Enviar emails de notificação
  - [ ] Marcar alertas como lidos

---

## 🔗 ROTAS - PENDENTE

### Adicionar em `public/index.php`:

```php
// Portal do Cliente
$router->get('/portal', [MonitoramentoPortalController::class, 'login']);
$router->get('/portal/login', [MonitoramentoPortalController::class, 'login']);
$router->post('/portal/processar-login', [MonitoramentoPortalController::class, 'processarLogin']);
$router->get('/portal/dashboard', [MonitoramentoPortalController::class, 'dashboard']);
$router->post('/portal/trocar-senha', [MonitoramentoPortalController::class, 'trocarSenha']);
$router->get('/portal/logout', [MonitoramentoPortalController::class, 'logout']);

// Admin - Monitoramento
$router->get('/monitoramento', [MonitoramentoController::class, 'index']);
$router->post('/monitoramento/criar-cliente', [MonitoramentoController::class, 'criarCliente']);
$router->get('/monitoramento/suprimentos', [MonitoramentoController::class, 'suprimentos']);
$router->get('/monitoramento/contadores', [MonitoramentoController::class, 'contadores']);
```

---

## 🧪 TESTES FINAIS

### Testes de Funcionalidade
- [ ] Criar cliente via admin
- [ ] Receber email com credenciais
- [ ] Fazer login no portal
- [ ] Trocar senha obrigatória
- [ ] Cadastrar impressora
- [ ] Testar conexão SNMP
- [ ] Visualizar dados no dashboard
- [ ] Gerar alerta de toner baixo
- [ ] Enviar contador manual
- [ ] Verificar logs

### Testes com Impressora Real
- [ ] Conectar impressora física
- [ ] Habilitar SNMP
- [ ] Testar coleta de dados
- [ ] Validar OIDs por fabricante
- [ ] Ajustar OIDs se necessário

---

## 📦 ARQUIVOS CRIADOS ATÉ AGORA

### SQL
- [x] `sql_modulo_monitoramento_completo.sql`

### Controllers
- [x] `src/Controllers/MonitoramentoController.php`
- [x] `src/Controllers/MonitoramentoPortalController.php`

### Views
- [x] `views/layouts/portal_layout.php`

### Testes
- [x] `teste_snmp.php`
- [x] `teste_snmp_simples.php`
- [x] `public/teste_snmp_web.php`
- [x] `public/teste_snmp_simples.php`
- [x] `simulador_impressora_snmp.php`

### Documentação
- [x] `.agent/workflows/modulo-monitoramento-planejamento.md`
- [x] `.agent/workflows/modulo-monitoramento-checklist.md` (este arquivo)

---

## 🎯 PRÓXIMA SESSÃO - PRIORIDADES

### 1. Criar Views do Portal (30-40 min)
- [ ] Login
- [ ] Dashboard
- [ ] Gerenciar Impressoras

### 2. Adicionar Rotas (10 min)
- [ ] Configurar todas as rotas no `public/index.php`

### 3. Testar Fluxo Completo (15 min)
- [ ] Criar cliente
- [ ] Login no portal
- [ ] Cadastrar impressora
- [ ] Visualizar dados

---

## 💡 OBSERVAÇÕES IMPORTANTES

1. **SNMP:** Extensão instalada ✅, mas sem impressora física para testar
2. **Email:** Verificar se `EmailService` está configurado
3. **Licença:** Padrão de 2 clientes inclusos, R$ 100/cliente adicional
4. **Senha Padrão:** `mudar@123` (obrigatório trocar no primeiro acesso)
5. **Token:** Gerado automaticamente para cada cliente

---

## 🚀 ESTIMATIVA DE CONCLUSÃO

- **Fase 2 (Portal):** 2-3 horas
- **Fase 3 (Admin):** 2-3 horas
- **Fase 4 (Automação):** 3-4 horas

**Total restante:** 7-10 horas de desenvolvimento

---

**Desenvolvido por:** SGQ OTI DJ  
**Início:** 05/12/2024  
**Status:** Em desenvolvimento (60% completo)
