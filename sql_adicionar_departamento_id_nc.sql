-- =====================================================
-- SQL para adicionar a coluna departamento_id 
-- na tabela nao_conformidades
-- =====================================================
-- Execute este SQL no seu banco de dados MySQL
-- Banco: u230868210_djsgqpro
-- =====================================================

-- 1. Verificar se a coluna já existe (para evitar erro)
-- Se retornar algum resultado, a coluna já existe e você não precisa executar o ALTER TABLE

SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'u230868210_djsgqpro' 
  AND TABLE_NAME = 'nao_conformidades' 
  AND COLUMN_NAME = 'departamento_id';

-- =====================================================
-- 2. Adicionar a coluna departamento_id
-- =====================================================

ALTER TABLE nao_conformidades 
ADD COLUMN departamento_id INT(11) NULL AFTER usuario_responsavel_id,
ADD INDEX idx_departamento_id (departamento_id),
ADD CONSTRAINT fk_nc_departamento 
    FOREIGN KEY (departamento_id) 
    REFERENCES departamentos(id) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

-- =====================================================
-- EXPLICAÇÃO:
-- =====================================================
-- - departamento_id INT(11) NULL: Permite valores nulos para NCs antigas
-- - AFTER usuario_responsavel_id: Posiciona a coluna após o responsável
-- - INDEX: Cria índice para melhorar performance nas consultas
-- - FOREIGN KEY: Garante integridade referencial com a tabela departamentos
-- - ON DELETE SET NULL: Se o departamento for excluído, a NC não é excluída, apenas o departamento_id vira NULL
-- - ON UPDATE CASCADE: Se o ID do departamento mudar, atualiza automaticamente

-- =====================================================
-- 3. VERIFICAÇÃO: Conferir se foi adicionado corretamente
-- =====================================================

DESCRIBE nao_conformidades;

-- =====================================================
-- 4. OPCIONAL: Atualizar NCs antigas sem departamento
-- =====================================================
-- Se você quiser atribuir um departamento padrão para as NCs antigas:

/*
-- Exemplo: Atribuir o departamento "Qualidade" (ID 1) para todas as NCs sem departamento
UPDATE nao_conformidades 
SET departamento_id = 1 
WHERE departamento_id IS NULL;
*/

-- OU

/*
-- Atribuir o departamento do responsável (se a tabela users tiver departamento_id)
UPDATE nao_conformidades nc
INNER JOIN users u ON nc.usuario_responsavel_id = u.id
SET nc.departamento_id = u.departamento_id
WHERE nc.departamento_id IS NULL 
  AND u.departamento_id IS NOT NULL;
*/

-- =====================================================
-- 5. VERIFICAÇÃO FINAL: Ver NCs com seus departamentos
-- =====================================================

SELECT 
    nc.id,
    nc.titulo,
    d.nome as departamento,
    u.name as responsavel
FROM nao_conformidades nc
LEFT JOIN departamentos d ON nc.departamento_id = d.id
LEFT JOIN users u ON nc.usuario_responsavel_id = u.id
ORDER BY nc.created_at DESC
LIMIT 10;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
