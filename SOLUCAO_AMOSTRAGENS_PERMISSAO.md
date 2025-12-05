# 🔧 SOLUÇÃO: Visualização de Amostragens para Admin/SuperAdmin

**Problema:** Admin e SuperAdmin não conseguem ver amost ragens no dashboard e módulo  
**Causa:** Verificação de role usando apenas `'admin'`, esquecendo `'superadmin'`  
**Data:** 04/12/2025 22:15

---

## 🎯 ARQUIVOS QUE PRECISAM SER CORRIGIDOS

### 1. `src/Controllers/Amostragens2Controller.php`

Procure e corrija em **3 lugares**:

#### ❌ LINHA ~29 (método `podeVisualizarAmostragem`)
```php
// ERRADO:
if ($userRole === 'admin') {

// CORRETO:
if (in_array($userRole, ['admin', 'superadmin'])) {
```

#### ❌ LINHA ~52 (método `index` - início)
```php
// ERRADO:
$isAdmin = $_SESSION['user_role'] === 'admin';

// CORRETO:
$isAdmin = in_array($_SESSION['user_role'], ['admin', 'superadmin']);
```

#### ❌ LINHA ~106 (método `index` - filtro de visualização)
```php
// ERRADO:
if ($userRole !== 'admin') {

// CORRETO:
if (!in_array($userRole, ['admin', 'superadmin'])) {
```

---

### 2. `src/Controllers/AdminController.php` (Dashboard)

Procure pelo método `getAmostragemsDashboardData` e verifique se também tem o problema:

```php
// Procurar por linhas como:
if ($userRole === 'admin') {

// E trocar por:
if (in_array($userRole, ['admin', 'superadmin'])) {
```

---

## 📋 COMO APLICAR

### Opção A: Edição Manual (RECOMENDADO)
1. Abra `src/Controllers/Amostragens2Controller.php`
2. Use Ctrl+F e procure por `=== 'admin'`
3. Substitua pelos códigos corretos acima
4. Salve

### Opção B: Comando Sed (se preferir)
```bash
# No diretório do projeto
sed -i "s/\$userRole === 'admin'/in_array(\$userRole, ['admin', 'superadmin'])/g" src/Controllers/Amostragens2Controller.php
```

---

## ✅ TESTE APÓS CORREÇÃO

1. Faça login como **superadmin**
2. Acesse `https://djbr.sgqoti.com.br/amostragens-2`
3. Deve ver TODAS as amostragens (não só as suas)
4. Acesse o **Dashboard > Aba Amostragens**
5. Os gráficos devem mostrar dados

---

## 🔍 OUTROS LUGARES PARA VERIFICAR

Procure em **TODOS os controllers** por padrões assim:

```php
// Padrão ERRADO:
$_SESSION['user_role'] === 'admin'
$userRole === 'admin'
$role === 'admin'

// Padrão CORRETO:
in_array($_SESSION['user_role'], ['admin', 'superadmin'])
in_array($userRole, ['admin', 'superadmin'])
in_array($role, ['admin', 'superadmin'])
```

Controllers para verificar:
- ✅ `AdminController.php`
- ✅ `Amostragens2Controller.php`
- ⚠️ `MelhoriaContinua2Controller.php` (já corrigido antes)
- ⚠️ `PopItsController.php`
- ⚠️ `FluxogramasController.php`
- ⚠️ `GarantiasController.php`

---

## 💡 SOLUÇÃO DEFINITIVA

Para evitar esse problema no futuro, crie um helper:

```php
// src/Support/helpers.php

function isAdmin(): bool {
    return in_array($_SESSION['user_role'] ?? '', ['admin', 'superadmin']);
}

function isSuperAdmin(): bool {
    return ($_SESSION['user_role'] ?? '') === 'superadmin';
}
```

Depois use assim:
```php
// Em vez de:
if ($userRole === 'admin') {

// Use:
if (isAdmin()) {
```

---

**Status:** ⚠️ **AGUARDANDO CORREÇÃO MANUAL**  
**Prioridade:** 🔴 **ALTA** (bloqueia visualização

 de superadmin)

