-- =====================================================================
-- ADICIONAR MÓDULOS ESPECIAIS AO SISTEMA DE PERMISSÕES
-- Data: 01/12/2025
-- Módulos: Implantação, CRM, Logística, Área Técnica
-- =====================================================================

-- Verifica se a tabela modules existe e adiciona os novos módulos
-- Se a tabela não existir, os INSERT serão ignorados

-- =====================================================================
-- 🚀 MÓDULOS DE IMPLANTAÇÃO
-- =====================================================================
INSERT IGNORE INTO modules (name, description, created_at) VALUES
('implantacao_dpo', 'Implantação - DPO (Data de Prevista de Operação)', NOW()),
('implantacao_ordem_servicos', 'Implantação - Ordem de Serviços de Implantação', NOW()),
('implantacao_fluxo', 'Implantação - Fluxo de Implantação', NOW()),
('implantacao_relatorios', 'Implantação - Relatórios', NOW());

-- =====================================================================
-- 💼 MÓDULOS DE CRM
-- =====================================================================
INSERT IGNORE INTO modules (name, description, created_at) VALUES
('crm_prospeccao', 'CRM - Prospecção de Clientes', NOW()),
('crm_vendas', 'CRM - Gestão de Vendas', NOW()),
('crm_relacionamento', 'CRM - Relacionamento com Clientes', NOW()),
('crm_marketing', 'CRM - Marketing e Campanhas', NOW()),
('crm_relatorios', 'CRM - Relatórios', NOW()),
('crm_dashboards', 'CRM - Dashboards e Indicadores', NOW());

-- =====================================================================
-- 📦 MÓDULOS DE LOGÍSTICA (R$ 600/mês)
-- =====================================================================
INSERT IGNORE INTO modules (name, description, created_at) VALUES
('logistica_entrada_estoque', 'Logística - Entrada de Estoque', NOW()),
('logistica_entrada_almoxarifados', 'Logística - Entrada de Almoxarifados', NOW()),
('logistica_inventarios', 'Logística - Inventários', NOW()),
('logistica_consulta_estoque', 'Logística - Consulta de Estoque', NOW()),
('logistica_consulta_almoxarifado', 'Logística - Consulta de Almoxarifado', NOW()),
('logistica_transferencias_internas', 'Logística - Transferências Internas', NOW()),
('logistica_transferencias_externas', 'Logística - Transferências Externas', NOW()),
('logistica_estoque_tecnico', 'Logística - Estoque Técnico', NOW());

-- =====================================================================
-- 🔧 MÓDULOS DE ÁREA TÉCNICA (R$ 200/mês)
-- =====================================================================
INSERT IGNORE INTO modules (name, description, created_at) VALUES
('area_tecnica', 'Área Técnica - Visão Geral', NOW()),
('area_tecnica_checklist', 'Área Técnica - Checklist Virtual', NOW()),
('area_tecnica_consulta', 'Área Técnica - Consulta de Checklists', NOW());

-- =====================================================================
-- PERMISSÕES PARA O PERFIL ADMINISTRADOR (ID = 1)
-- Concede todas as permissões (view, edit, delete, import, export)
-- =====================================================================

-- Buscar o ID do perfil Administrador
SET @admin_profile_id = (SELECT id FROM profiles WHERE name LIKE '%Administrador%' LIMIT 1);

-- Se não encontrar, usar ID 1 como padrão
SET @admin_profile_id = IFNULL(@admin_profile_id, 1);

-- 🚀 Permissões de Implantação para Admin
INSERT IGNORE INTO profile_permissions (profile_id, module, can_view, can_edit, can_delete, can_import, can_export) VALUES
(@admin_profile_id, 'implantacao_dpo', 1, 1, 1, 1, 1),
(@admin_profile_id, 'implantacao_ordem_servicos', 1, 1, 1, 1, 1),
(@admin_profile_id, 'implantacao_fluxo', 1, 1, 1, 1, 1),
(@admin_profile_id, 'implantacao_relatorios', 1, 1, 1, 1, 1);

-- 💼 Permissões de CRM para Admin
INSERT IGNORE INTO profile_permissions (profile_id, module, can_view, can_edit, can_delete, can_import, can_export) VALUES
(@admin_profile_id, 'crm_prospeccao', 1, 1, 1, 1, 1),
(@admin_profile_id, 'crm_vendas', 1, 1, 1, 1, 1),
(@admin_profile_id, 'crm_relacionamento', 1, 1, 1, 1, 1),
(@admin_profile_id, 'crm_marketing', 1, 1, 1, 1, 1),
(@admin_profile_id, 'crm_relatorios', 1, 1, 1, 1, 1),
(@admin_profile_id, 'crm_dashboards', 1, 1, 1, 1, 1);

-- 📦 Permissões de Logística para Admin
INSERT IGNORE INTO profile_permissions (profile_id, module, can_view, can_edit, can_delete, can_import, can_export) VALUES
(@admin_profile_id, 'logistica_entrada_estoque', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_entrada_almoxarifados', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_inventarios', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_consulta_estoque', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_consulta_almoxarifado', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_transferencias_internas', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_transferencias_externas', 1, 1, 1, 1, 1),
(@admin_profile_id, 'logistica_estoque_tecnico', 1, 1, 1, 1, 1);

-- 🔧 Permissões de Área Técnica para Admin
INSERT IGNORE INTO profile_permissions (profile_id, module, can_view, can_edit, can_delete, can_import, can_export) VALUES
(@admin_profile_id, 'area_tecnica', 1, 1, 1, 1, 1),
(@admin_profile_id, 'area_tecnica_checklist', 1, 1, 1, 1, 1),
(@admin_profile_id, 'area_tecnica_consulta', 1, 1, 1, 1, 1);

-- =====================================================================
-- VERIFICAÇÃO: Listar módulos adicionados
-- =====================================================================
SELECT 
    'MÓDULOS ESPECIAIS ADICIONADOS:' as '📋 STATUS',
    (SELECT COUNT(*) FROM modules WHERE name LIKE 'implantacao_%') as '🚀 Implantação',
    (SELECT COUNT(*) FROM modules WHERE name LIKE 'crm_%') as '💼 CRM',
    (SELECT COUNT(*) FROM modules WHERE name LIKE 'logistica_%') as '📦 Logística',
    (SELECT COUNT(*) FROM modules WHERE name LIKE 'area_tecnica%') as '🔧 Área Técnica';

-- =====================================================================
-- VERIFICAÇÃO: Listar permissões do Admin
-- =====================================================================
SELECT 
    pp.module as 'Módulo',
    CASE WHEN pp.can_view = 1 THEN '✅' ELSE '❌' END as 'View',
    CASE WHEN pp.can_edit = 1 THEN '✅' ELSE '❌' END as 'Edit',
    CASE WHEN pp.can_delete = 1 THEN '✅' ELSE '❌' END as 'Delete',
    CASE WHEN pp.can_import = 1 THEN '✅' ELSE '❌' END as 'Import',
    CASE WHEN pp.can_export = 1 THEN '✅' ELSE '❌' END as 'Export'
FROM profile_permissions pp
WHERE pp.profile_id = @admin_profile_id
AND (pp.module LIKE 'implantacao_%' 
     OR pp.module LIKE 'crm_%' 
     OR pp.module LIKE 'logistica_%' 
     OR pp.module LIKE 'area_tecnica%')
ORDER BY pp.module;
