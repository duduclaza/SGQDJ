# 🔧 SOLUÇÃO: Ranking de Clientes Vazio

**Problema:** Erro 1267 - Illegal mix of collations  
**Causa:** conflito entre `utf8mb4_general_ci` e `utf8mb4_unicode_ci`  
**Data:** 04/12/2025 21:36

---

## 🎯 SOLUÇÃO COMPLETA

Edite o arquivo `src/Controllers/AdminController.php` na linha **1504** (aproximadamente):

### Antes (COM ERRO):
```php
LEFT JOIN clientes c ON TRIM(LEADING '0' FROM r.codigo_cliente) = TRIM(LEADING '0' FROM c.codigo)
```

### Depois (CORRIGIDO):
```php
LEFT JOIN clientes c ON TRIM(LEADING '0' FROM r.codigo_cliente) COLLATE utf8mb4_unicode_ci = TRIM(LEADING '0' FROM c.codigo) COLLATE utf8mb4_unicode_ci
```

---

## 📍 LOCALIZAÇÃO EXATA

Método: `getRankingClientes()`  
Arquivo: `src/Controllers/AdminController.php`  
Linha: ~1504

```php
public function getRankingClientes()
{
    header('Content-Type: application/json');
    
    try {
        $filial = $_GET['filial'] ?? '';
        $destino = $_GET['destino'] ?? '';
        $dataInicial = $_GET['data_inicial'] ?? '';
        $dataFinal = $_GET['data_final'] ?? '';
        
        $dateColumn = $this->getDateColumn();
        $filialColumn = $this->getFilialColumn();
        $destinoColumn = $this->getDestinoColumn();
        
        $sql = "
            SELECT 
                TRIM(LEADING '0' FROM r.codigo_cliente) as codigo_cliente,
                MAX(c.nome) as nome_cliente,
                SUM(r.quantidade) as total_retornados
            FROM retornados r
            LEFT JOIN clientes c ON TRIM(LEADING '0' FROM r.codigo_cliente) COLLATE utf8mb4_unicode_ci = TRIM(LEADING '0' FROM c.codigo) COLLATE utf8mb4_unicode_ci
            WHERE r.codigo_cliente IS NOT NULL 
            AND r.codigo_cliente != ''
            AND r.codigo_cliente REGEXP '[0-9]'
            AND TRIM(LEADING '0' FROM r.codigo_cliente) NOT IN ('', '1', '2852')
        ";
        
       // ... resto do código continua igual
    }
}
```

---

## 🔍 COMO ENCONTRAR

1. Abra `src/Controllers/AdminController.php`
2. Busque por `getRankingClientes`
3. Encontre a linha com `LEFT JOIN clientes`
4. Adicione `COLLATE utf8mb4_unicode_ci` em ambos os lados do `=`

---

## ✅ TESTE

Após aplicar, recarregue o dashboard:
```
https://djbr.sgqoti.com.br/dashboard
```

O ranking deve aparecer com dados!

---

**Data:** 04/12/2025  
**Problema:** SQLSTATE[HY000]: General error: 1267  
**Solução:** Forçar collation uniforme no JOIN

