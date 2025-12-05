-- =====================================================
-- MÓDULO: MONITORAMENTO DE SUPRIMENTOS E CONTADORES
-- Banco de Dados Completo
-- =====================================================
-- Banco: u230868210_djsgqpro
-- Data: Dezembro 2024
-- =====================================================

-- =====================================================
-- 1. TABELA: monitoramento_clientes
-- Armazena os clientes que terão portal de monitoramento
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da empresa cliente',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email do cliente (usado como login)',
    telefone VARCHAR(20) COMMENT 'Telefone de contato',
    cnpj VARCHAR(18) COMMENT 'CNPJ da empresa',
    endereco TEXT COMMENT 'Endereço completo',
    portal_token VARCHAR(64) UNIQUE NOT NULL COMMENT 'Token único para acesso ao portal',
    senha_hash VARCHAR(255) NOT NULL COMMENT 'Hash da senha do cliente',
    senha_temporaria BOOLEAN DEFAULT 1 COMMENT 'Se TRUE, força troca de senha no próximo login',
    ativo BOOLEAN DEFAULT 1 COMMENT 'Se o portal está ativo',
    limite_impressoras INT DEFAULT 10 COMMENT 'Limite de impressoras que o cliente pode cadastrar',
    alerta_toner_percentual INT DEFAULT 20 COMMENT 'Percentual mínimo de toner para gerar alerta',
    dia_envio_contador INT DEFAULT 1 COMMENT 'Dia do mês para envio automático de contadores (1-31)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_portal_token (portal_token),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes com portal de monitoramento';

-- =====================================================
-- 2. TABELA: monitoramento_impressoras
-- Impressoras cadastradas pelos clientes
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_impressoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL COMMENT 'ID do cliente proprietário',
    numero_serie VARCHAR(100) NOT NULL COMMENT 'Número de série da impressora',
    modelo VARCHAR(255) NOT NULL COMMENT 'Modelo da impressora',
    ip_address VARCHAR(45) NOT NULL COMMENT 'Endereço IP da impressora na rede',
    tipo_impressora ENUM('monocromatica', 'colorida') DEFAULT 'monocromatica' COMMENT 'Tipo de impressora',
    tem_contador_pb BOOLEAN DEFAULT 1 COMMENT 'Se possui contador P&B',
    tem_contador_color BOOLEAN DEFAULT 0 COMMENT 'Se possui contador Color',
    envio_automatico BOOLEAN DEFAULT 0 COMMENT 'Se o envio de contador é automático',
    dia_envio INT DEFAULT 1 COMMENT 'Dia do mês para envio automático (1-31)',
    ativa BOOLEAN DEFAULT 1 COMMENT 'Se a impressora está ativa no monitoramento',
    ultima_leitura_snmp TIMESTAMP NULL COMMENT 'Data/hora da última leitura SNMP bem-sucedida',
    status_conexao ENUM('online', 'offline', 'erro') DEFAULT 'offline' COMMENT 'Status da última tentativa de conexão',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES monitoramento_clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_numero_serie (numero_serie),
    INDEX idx_ip_address (ip_address),
    INDEX idx_ativa (ativa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Impressoras cadastradas para monitoramento';

-- =====================================================
-- 3. TABELA: monitoramento_contadores
-- Histórico de leituras de contadores
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_contadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL COMMENT 'ID da impressora',
    contador_pb INT DEFAULT 0 COMMENT 'Contador de páginas P&B',
    contador_color INT DEFAULT 0 COMMENT 'Contador de páginas Color',
    data_leitura TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da leitura',
    tipo_envio ENUM('automatico', 'manual') DEFAULT 'manual' COMMENT 'Como foi enviado',
    enviado_por INT COMMENT 'ID do usuário que enviou (se manual)',
    observacoes TEXT COMMENT 'Observações sobre a leitura',
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    FOREIGN KEY (enviado_por) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_impressora (impressora_id),
    INDEX idx_data_leitura (data_leitura),
    INDEX idx_tipo_envio (tipo_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de leituras de contadores';

-- =====================================================
-- 4. TABELA: monitoramento_suprimentos
-- Histórico de níveis de suprimentos (toner)
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_suprimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL COMMENT 'ID da impressora',
    toner_preto INT DEFAULT 100 COMMENT 'Nível de toner preto (%)',
    toner_ciano INT DEFAULT NULL COMMENT 'Nível de toner ciano (%) - NULL se não aplicável',
    toner_magenta INT DEFAULT NULL COMMENT 'Nível de toner magenta (%) - NULL se não aplicável',
    toner_amarelo INT DEFAULT NULL COMMENT 'Nível de toner amarelo (%) - NULL se não aplicável',
    data_leitura TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da leitura',
    alerta_enviado BOOLEAN DEFAULT 0 COMMENT 'Se já foi enviado alerta para esta leitura',
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    INDEX idx_impressora (impressora_id),
    INDEX idx_data_leitura (data_leitura),
    INDEX idx_alerta_enviado (alerta_enviado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de níveis de suprimentos';

-- =====================================================
-- 5. TABELA: monitoramento_alertas
-- Alertas gerados pelo sistema
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impressora_id INT NOT NULL COMMENT 'ID da impressora',
    tipo_alerta ENUM('toner_baixo', 'contador_limite', 'impressora_offline', 'outro') NOT NULL COMMENT 'Tipo do alerta',
    mensagem TEXT NOT NULL COMMENT 'Mensagem do alerta',
    nivel ENUM('info', 'warning', 'critical') DEFAULT 'warning' COMMENT 'Nível de severidade',
    lido BOOLEAN DEFAULT 0 COMMENT 'Se o alerta já foi lido',
    lido_por INT COMMENT 'ID do usuário que marcou como lido',
    lido_em TIMESTAMP NULL COMMENT 'Data/hora que foi marcado como lido',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    FOREIGN KEY (lido_por) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_impressora (impressora_id),
    INDEX idx_lido (lido),
    INDEX idx_nivel (nivel),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alertas do sistema de monitoramento';

-- =====================================================
-- 6. TABELA: monitoramento_logs
-- Logs de ações no sistema de monitoramento
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT COMMENT 'ID do cliente (se aplicável)',
    impressora_id INT COMMENT 'ID da impressora (se aplicável)',
    usuario_id INT COMMENT 'ID do usuário que executou a ação',
    acao VARCHAR(255) NOT NULL COMMENT 'Descrição da ação',
    detalhes TEXT COMMENT 'Detalhes adicionais em JSON',
    ip_origem VARCHAR(45) COMMENT 'IP de onde partiu a ação',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES monitoramento_clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (impressora_id) REFERENCES monitoramento_impressoras(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_cliente (cliente_id),
    INDEX idx_impressora (impressora_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de ações do sistema de monitoramento';

-- =====================================================
-- 7. TABELA: monitoramento_licencas
-- Controle de licenças do módulo
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_licencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clientes_inclusos INT DEFAULT 2 COMMENT 'Quantidade de clientes inclusos na licença base',
    clientes_adicionais INT DEFAULT 0 COMMENT 'Quantidade de clientes adicionais contratados',
    valor_adicional DECIMAL(10,2) DEFAULT 100.00 COMMENT 'Valor por cliente adicional (R$)',
    total_clientes_permitidos INT GENERATED ALWAYS AS (clientes_inclusos + clientes_adicionais) STORED COMMENT 'Total de clientes permitidos',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_total_clientes (total_clientes_permitidos)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Controle de licenças do módulo de monitoramento';

-- =====================================================
-- 8. TABELA: monitoramento_oids
-- Biblioteca de OIDs SNMP por fabricante/modelo
-- =====================================================

CREATE TABLE IF NOT EXISTS monitoramento_oids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fabricante VARCHAR(100) NOT NULL COMMENT 'Fabricante da impressora (HP, Canon, Xerox, etc)',
    modelo VARCHAR(255) COMMENT 'Modelo específico (NULL = vale para todos do fabricante)',
    tipo_dado ENUM('contador_pb', 'contador_color', 'toner_preto', 'toner_ciano', 'toner_magenta', 'toner_amarelo', 'capacidade_toner', 'status', 'modelo', 'numero_serie') NOT NULL COMMENT 'Tipo de dado que o OID retorna',
    oid VARCHAR(255) NOT NULL COMMENT 'OID SNMP',
    descricao TEXT COMMENT 'Descrição do OID',
    ativo BOOLEAN DEFAULT 1 COMMENT 'Se o OID está ativo para uso',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_fabricante (fabricante),
    INDEX idx_tipo_dado (tipo_dado),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Biblioteca de OIDs SNMP por fabricante';

-- =====================================================
-- INSERIR LICENÇA PADRÃO
-- =====================================================

INSERT INTO monitoramento_licencas (clientes_inclusos, clientes_adicionais, valor_adicional) 
VALUES (2, 0, 100.00)
ON DUPLICATE KEY UPDATE id=id;

-- =====================================================
-- INSERIR OIDs PADRÃO (Genéricos - Funcionam na maioria)
-- =====================================================

INSERT INTO monitoramento_oids (fabricante, modelo, tipo_dado, oid, descricao) VALUES
-- Contadores (Padrão RFC 3805)
('Genérico', NULL, 'contador_pb', '1.3.6.1.2.1.43.10.2.1.4.1.1', 'Contador total de páginas P&B'),
('Genérico', NULL, 'contador_color', '1.3.6.1.2.1.43.10.2.1.4.1.2', 'Contador total de páginas Color'),

-- Níveis de Toner (Padrão RFC 3805)
('Genérico', NULL, 'toner_preto', '1.3.6.1.2.1.43.11.1.1.9.1.1', 'Nível atual de toner preto'),
('Genérico', NULL, 'toner_ciano', '1.3.6.1.2.1.43.11.1.1.9.1.2', 'Nível atual de toner ciano'),
('Genérico', NULL, 'toner_magenta', '1.3.6.1.2.1.43.11.1.1.9.1.3', 'Nível atual de toner magenta'),
('Genérico', NULL, 'toner_amarelo', '1.3.6.1.2.1.43.11.1.1.9.1.4', 'Nível atual de toner amarelo'),

-- Capacidade Máxima de Toner
('Genérico', NULL, 'capacidade_toner', '1.3.6.1.2.1.43.11.1.1.8.1.1', 'Capacidade máxima do toner preto'),

-- Informações da Impressora
('Genérico', NULL, 'modelo', '1.3.6.1.2.1.25.3.2.1.3.1', 'Modelo da impressora'),
('Genérico', NULL, 'numero_serie', '1.3.6.1.2.1.43.5.1.1.17.1', 'Número de série da impressora'),
('Genérico', NULL, 'status', '1.3.6.1.2.1.25.3.2.1.5.1', 'Status da impressora')

ON DUPLICATE KEY UPDATE id=id;

-- =====================================================
-- VERIFICAÇÃO: Listar todas as tabelas criadas
-- =====================================================

SHOW TABLES LIKE 'monitoramento_%';

-- =====================================================
-- VERIFICAÇÃO: Contar registros iniciais
-- =====================================================

SELECT 'monitoramento_clientes' as tabela, COUNT(*) as registros FROM monitoramento_clientes
UNION ALL
SELECT 'monitoramento_impressoras', COUNT(*) FROM monitoramento_impressoras
UNION ALL
SELECT 'monitoramento_contadores', COUNT(*) FROM monitoramento_contadores
UNION ALL
SELECT 'monitoramento_suprimentos', COUNT(*) FROM monitoramento_suprimentos
UNION ALL
SELECT 'monitoramento_alertas', COUNT(*) FROM monitoramento_alertas
UNION ALL
SELECT 'monitoramento_logs', COUNT(*) FROM monitoramento_logs
UNION ALL
SELECT 'monitoramento_licencas', COUNT(*) FROM monitoramento_licencas
UNION ALL
SELECT 'monitoramento_oids', COUNT(*) FROM monitoramento_oids;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
