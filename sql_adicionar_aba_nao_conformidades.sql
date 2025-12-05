-- =====================================================
-- SQL para adicionar a aba "Não Conformidades" 
-- no Dashboard - Permissões de Perfil
-- =====================================================
-- Execute este SQL no seu banco de dados MySQL
-- Banco: u230868210_djsgqpro
-- =====================================================

-- 1. Verificar se a tabela dashboard_tab_permissions existe
-- Se não existir, você precisará criá-la primeiro

-- 2. Adicionar a permissão da aba 'nao_conformidades' para TODOS os perfis existentes
-- Isso vai adicionar a permissão com can_view = 1 (habilitado) para todos os perfis

INSERT INTO dashboard_tab_permissions (profile_id, tab_name, can_view, created_at, updated_at)
SELECT DISTINCT 
    dtp.profile_id, 
    'nao_conformidades', 
    1,
    NOW(),
    NOW()
FROM dashboard_tab_permissions dtp
WHERE NOT EXISTS (
    SELECT 1 
    FROM dashboard_tab_permissions dtp2 
    WHERE dtp2.profile_id = dtp.profile_id 
    AND dtp2.tab_name = 'nao_conformidades'
)
GROUP BY dtp.profile_id;

-- =====================================================
-- ALTERNATIVA: Se preferir adicionar APENAS para perfis de Admin/Super Admin
-- =====================================================
-- Comente o SQL acima e descomente o SQL abaixo:

/*
INSERT INTO dashboard_tab_permissions (profile_id, tab_name, can_view, created_at, updated_at)
SELECT 
    p.id, 
    'nao_conformidades', 
    1,
    NOW(),
    NOW()
FROM profiles p
WHERE p.is_admin = 1
AND NOT EXISTS (
    SELECT 1 
    FROM dashboard_tab_permissions dtp 
    WHERE dtp.profile_id = p.id 
    AND dtp.tab_name = 'nao_conformidades'
);
*/

-- =====================================================
-- VERIFICAÇÃO: Conferir se foi adicionado corretamente
-- =====================================================

SELECT 
    p.id,
    p.name as perfil,
    dtp.tab_name as aba,
    dtp.can_view as pode_visualizar
FROM profiles p
LEFT JOIN dashboard_tab_permissions dtp ON p.id = dtp.profile_id
WHERE dtp.tab_name = 'nao_conformidades'
ORDER BY p.name;

-- =====================================================
-- OPCIONAL: Ver todas as permissões de abas de um perfil específico
-- =====================================================
-- Substitua o ID do perfil (exemplo: 1) pelo ID que você quer verificar

/*
SELECT 
    dtp.tab_name as aba,
    dtp.can_view as pode_visualizar,
    p.name as perfil
FROM dashboard_tab_permissions dtp
JOIN profiles p ON dtp.profile_id = p.id
WHERE dtp.profile_id = 1
ORDER BY dtp.tab_name;
*/

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
