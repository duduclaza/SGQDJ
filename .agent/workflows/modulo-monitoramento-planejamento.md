# 🚀 MÓDULO: MONITORAMENTO DE SUPRIMENTOS E CONTADORES
## Plano de Implementação Completo

---

## 🎯 VISÃO GERAL

**Diferencial Competitivo:** Sistema de monitoramento SEM necessidade de instalar agentes nos computadores dos clientes.

**Solução:** Portal web individual para cada cliente onde eles mesmos cadastram e monitoram suas impressoras via SNMP.

**Limite:** 2 clientes inclusos. Clientes adicionais: R$ 100,00/mês cada.

---

## 📋 ESTRUTURA DO MÓDULO

### 1. MENU PRINCIPAL: "Monitoramento"

#### 1.1 Sub-menu: Portal de Clientes
- **Função:** Gerenciar clientes e seus portais
- **Ações:**
  - ✅ Adicionar novo cliente
  - ✅ Criar portal individual
  - ✅ Enviar credenciais por e-mail
  - ✅ Visualizar status dos portais
  - ✅ Desativar/Reativar portais

#### 1.2 Sub-menu: Contadores
- **Função:** Visualizar dados de contadores recebidos dos clientes
- **Dados exibidos:**
  - Cliente
  - Impressora (Modelo, Número de Série, IP)
  - Contador P&B
  - Contador Color (se aplicável)
  - Última atualização
  - Histórico de leituras

#### 1.3 Sub-menu: Monitor de Suprimentos
- **Função:** Monitoramento automático de níveis de toner
- **Dados exibidos:**
  - Cliente
  - Impressora
  - Níveis de toner (%, por cor)
  - Alertas (quando abaixo do limite configurado)
  - Última atualização
  - Status (OK, Atenção, Crítico)

---

## 🏗️ ESTRUTURA DO BANCO DE DADOS

### Tabela: `monitoramento_clientes`
```sql
CREATE TABLE monitoramento_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    cnpj VARCHAR(18),
    endereco TEXT,
    portal_token VARCHAR(64) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    senha_temporaria BOOLEAN DEFAULT 1,
    ativo BOOLEAN DEFAULT 1,
    limite_impressoras INT DEFAULT 10,
    alerta_toner_percentual INT DEFAULT 20,
    dia_envio_contador INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_portal_token (portal_token)
);
```

### Tabela: `monitoramento_impressoras`
```sql
CREATE TABLE monitoramento_impressoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    numero_serie VARCHAR(100) NOT NULL,
    modelo VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    tipo_impressora ENUM('monocromatica', 'colorida') DEFAULT 'monocromatica',
    tem_contador_pb BOOLEAN DEFAULT 1,
    tem_contador_color BOOLEAN DEFAULT 0,
    envio_automatico BOOLEAN DEFAULT 0,
    dia_envio INT DEFAULT 1,
    ativa BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES monitoramento_clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_numero_serie (numero_serie)
);
```

### Tabela: `monitoramento_contadores`
```sql
CREATE TABLE monitoramento_contadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL,
    contador_pb INT DEFAULT 0,
    contador_color INT DEFAULT 0,
    data_leitura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_envio ENUM('automatico', 'manual') DEFAULT 'manual',
    enviado_por INT,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    FOREIGN KEY (enviado_por) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_impressora (impressora_id),
    INDEX idx_data_leitura (data_leitura)
);
```

### Tabela: `monitoramento_suprimentos`
```sql
CREATE TABLE monitoramento_suprimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL,
    toner_preto INT DEFAULT 100,
    toner_ciano INT DEFAULT NULL,
    toner_magenta INT DEFAULT NULL,
    toner_amarelo INT DEFAULT NULL,
    data_leitura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    alerta_enviado BOOLEAN DEFAULT 0,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    INDEX idx_impressora (impressora_id),
    INDEX idx_data_leitura (data_leitura)
);
```

### Tabela: `monitoramento_alertas`
```sql
CREATE TABLE monitoramento_alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL,
    tipo_alerta ENUM('toner_baixo', 'contador_limite', 'impressora_offline') NOT NULL,
    mensagem TEXT NOT NULL,
    nivel ENUM('info', 'warning', 'critical') DEFAULT 'warning',
    lido BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    INDEX idx_impressora (impressora_id),
    INDEX idx_lido (lido)
);
```

### Tabela: `monitoramento_logs`
```sql
CREATE TABLE monitoramento_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    impressora_id INT,
    acao VARCHAR(255) NOT NULL,
    detalhes TEXT,
    ip_origem VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES monitoramento_clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_created_at (created_at)
);
```

---

## 🔐 FLUXO DE AUTENTICAÇÃO DO PORTAL

### 1. Criação do Portal
```
1. Admin adiciona cliente no sistema
2. Sistema gera:
   - Token único (portal_token)
   - Senha temporária: mudar@123
   - Link único: https://sgqoti.com.br/portal/{token}
3. E-mail enviado ao cliente com:
   - Link do portal
   - Usuário: email@cliente.com
   - Senha: mudar@123
```

### 2. Primeiro Acesso
```
1. Cliente acessa link único
2. Faz login com email + senha temporária
3. Sistema força alteração de senha
4. Redireciona para dashboard do portal
```

### 3. Recuperação de Senha
```
1. Cliente clica "Esqueci minha senha"
2. Informa e-mail
3. Recebe link de redefinição
4. Define nova senha
5. Acessa portal normalmente
```

---

## 🖥️ PORTAL DO CLIENTE

### Dashboard Principal
- **Cards de Resumo:**
  - Total de impressoras cadastradas
  - Impressoras com alerta de toner
  - Próximo envio de contador
  - Última atualização

- **Gráficos:**
  - Evolução de contadores (últimos 6 meses)
  - Status de suprimentos (gauge charts)
  - Impressoras por status

### Gerenciar Impressoras
- **Lista de Impressoras:**
  - Modelo
  - Número de Série
  - IP
  - Status (Online/Offline)
  - Níveis de toner
  - Contadores
  - Ações (Editar, Excluir, Testar Conexão)

- **Adicionar Nova Impressora:**
  ```
  Formulário:
  - Número de Série *
  - Modelo *
  - IP da Impressora *
  - Tipo (Monocromática/Colorida)
  - Contadores disponíveis (P&B, Color)
  - Envio de Contador:
    ○ Manual
    ○ Automático (dia do mês: 1-31)
  - [Botão: Testar Conexão SNMP]
  - [Botão: Salvar]
  ```

### Relatórios
- **Contador Manual:**
  - Selecionar impressora
  - Visualizar dados atuais
  - Imprimir relatório
  - Enviar para sistema

- **Histórico:**
  - Histórico de contadores
  - Histórico de suprimentos
  - Histórico de alertas

---

## 🔧 TECNOLOGIA: SNMP

### Extensão PHP SNMP
```php
// Verificar se SNMP está instalado
if (!extension_loaded('snmp')) {
    die('Extensão SNMP não está instalada');
}

// Configuração SNMP
$host = '192.168.1.100';
$community = 'public';
$oid_contador_pb = '1.3.6.1.2.1.43.10.2.1.4.1.1';
$oid_toner_preto = '1.3.6.1.2.1.43.11.1.1.9.1.1';

// Ler contador
$contador = snmpget($host, $community, $oid_contador_pb);

// Ler nível de toner
$toner = snmpget($host, $community, $oid_toner_preto);
```

### OIDs Comuns (SNMP)
```
Contadores:
- Contador Total P&B: 1.3.6.1.2.1.43.10.2.1.4.1.1
- Contador Total Color: 1.3.6.1.2.1.43.10.2.1.4.1.2

Suprimentos (Toner):
- Toner Preto: 1.3.6.1.2.1.43.11.1.1.9.1.1
- Toner Ciano: 1.3.6.1.2.1.43.11.1.1.9.1.2
- Toner Magenta: 1.3.6.1.2.1.43.11.1.1.9.1.3
- Toner Amarelo: 1.3.6.1.2.1.43.11.1.1.9.1.4

Capacidade Máxima:
- Capacidade Toner Preto: 1.3.6.1.2.1.43.11.1.1.8.1.1
- Capacidade Toner Ciano: 1.3.6.1.2.1.43.11.1.1.8.1.2
- Capacidade Toner Magenta: 1.3.6.1.2.1.43.11.1.1.8.1.3
- Capacidade Toner Amarelo: 1.3.6.1.2.1.43.11.1.1.8.1.4

Informações da Impressora:
- Modelo: 1.3.6.1.2.1.25.3.2.1.3.1
- Número de Série: 1.3.6.1.2.1.43.5.1.1.17.1
- Status: 1.3.6.1.2.1.25.3.2.1.5.1
```

---

## 📧 SISTEMA DE ALERTAS

### Alerta de Toner Baixo
```
Quando: Nível de toner < percentual configurado
Para: Equipe responsável pelo monitoramento
Conteúdo:
- Cliente
- Impressora (Modelo, Série, IP)
- Cor do toner
- Nível atual (%)
- Ação sugerida: "Enviar novo suprimento"
```

### Alerta de Contador
```
Quando: Dia configurado para envio automático
Para: Sistema (registro automático)
Conteúdo:
- Leitura automática via SNMP
- Registro no banco de dados
- Notificação para equipe (se houver variação anormal)
```

---

## 💰 CONTROLE DE LICENÇAS

### Tabela: `monitoramento_licencas`
```sql
CREATE TABLE monitoramento_licencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientes_inclusos INT DEFAULT 2,
    clientes_adicionais INT DEFAULT 0,
    valor_adicional DECIMAL(10,2) DEFAULT 100.00,
    total_clientes_permitidos INT GENERATED ALWAYS AS (clientes_inclusos + clientes_adicionais) STORED,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir licença padrão
INSERT INTO monitoramento_licencas (clientes_inclusos, clientes_adicionais) 
VALUES (2, 0);
```

### Validação ao Adicionar Cliente
```php
// Verificar limite de clientes
$stmt = $db->query("SELECT total_clientes_permitidos FROM monitoramento_licencas LIMIT 1");
$licenca = $stmt->fetch();

$stmt = $db->query("SELECT COUNT(*) as total FROM monitoramento_clientes WHERE ativo = 1");
$clientesAtivos = $stmt->fetch()['total'];

if ($clientesAtivos >= $licenca['total_clientes_permitidos']) {
    throw new Exception("Limite de clientes atingido. Entre em contato para adicionar mais clientes.");
}
```

---

## 📱 INTERFACE DO SISTEMA (Admin)

### Portal de Clientes
```
┌─────────────────────────────────────────────────────────┐
│ 🏢 Portal de Clientes                    [+ Novo Cliente]│
├─────────────────────────────────────────────────────────┤
│                                                           │
│ 📊 Resumo:                                               │
│ ┌──────────┬──────────┬──────────┬──────────┐          │
│ │ Clientes │Impressoras│ Alertas  │ Licença  │          │
│ │    5     │    23     │    3     │  2/7     │          │
│ └──────────┴──────────┴──────────┴──────────┘          │
│                                                           │
│ 📋 Lista de Clientes:                                    │
│ ┌───┬─────────────┬──────────────┬────────┬─────────┐  │
│ │ # │ Cliente     │ Email        │ Status │ Ações   │  │
│ ├───┼─────────────┼──────────────┼────────┼─────────┤  │
│ │ 1 │ Empresa A   │ email@a.com  │ 🟢Ativo│ 👁️ ✏️ 🗑️│  │
│ │ 2 │ Empresa B   │ email@b.com  │ 🟢Ativo│ 👁️ ✏️ 🗑️│  │
│ └───┴─────────────┴──────────────┴────────┴─────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Monitor de Suprimentos
```
┌─────────────────────────────────────────────────────────┐
│ 🖨️ Monitor de Suprimentos                    [Atualizar]│
├─────────────────────────────────────────────────────────┤
│                                                           │
│ 🔴 Alertas Críticos (3)                                  │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ ⚠️ Empresa A - HP LaserJet Pro - Toner Preto: 5%   │ │
│ │ ⚠️ Empresa B - Canon iR - Toner Ciano: 8%          │ │
│ │ ⚠️ Empresa C - Xerox WorkCentre - Toner Magenta: 3%│ │
│ └─────────────────────────────────────────────────────┘ │
│                                                           │
│ 📊 Todas as Impressoras:                                 │
│ [Filtros: Cliente ▼ | Status ▼ | Modelo ▼]              │
│ ┌────────────────────────────────────────────────────┐  │
│ │ Cliente  │ Modelo      │ P  C  M  A │ Atualização │  │
│ ├──────────┼─────────────┼────────────┼─────────────┤  │
│ │ Empresa A│ HP LJ Pro   │95% - - -   │ há 2 min    │  │
│ │ Empresa A│ Canon iR    │80%70%65%72%│ há 5 min    │  │
│ └────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 🚀 ROADMAP DE IMPLEMENTAÇÃO

### Fase 1: Estrutura Base (Semana 1-2)
- [ ] Criar tabelas no banco de dados
- [ ] Criar controllers e models
- [ ] Implementar autenticação do portal
- [ ] Sistema de envio de e-mails

### Fase 2: Portal do Cliente (Semana 3-4)
- [ ] Dashboard do cliente
- [ ] CRUD de impressoras
- [ ] Teste de conexão SNMP
- [ ] Interface de relatórios

### Fase 3: Monitoramento (Semana 5-6)
- [ ] Integração SNMP
- [ ] Coleta automática de dados
- [ ] Sistema de alertas
- [ ] Histórico de leituras

### Fase 4: Admin (Semana 7-8)
- [ ] Gerenciamento de clientes
- [ ] Monitor de suprimentos
- [ ] Visualização de contadores
- [ ] Controle de licenças

### Fase 5: Testes e Ajustes (Semana 9-10)
- [ ] Testes com impressoras reais
- [ ] Ajustes de OIDs por fabricante
- [ ] Otimização de performance
- [ ] Documentação

---

## 📝 NOTAS IMPORTANTES

1. **SNMP deve estar habilitado nas impressoras**
2. **Community string padrão: "public"** (pode ser configurável)
3. **Firewall deve permitir porta 161 (SNMP)**
4. **OIDs podem variar por fabricante** (criar biblioteca de OIDs)
5. **Implementar retry em caso de falha de conexão**
6. **Cache de dados para evitar sobrecarga**
7. **Logs detalhados de todas as operações**

---

## 🎨 DESIGN PREMIUM

- Interface moderna e intuitiva
- Gráficos interativos (Chart.js)
- Animações suaves
- Responsivo (mobile-first)
- Dark mode opcional
- Notificações em tempo real

---

**Desenvolvido por:** SGQ OTI DJ
**Versão:** 1.0.0
**Data:** Dezembro 2024
