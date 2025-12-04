# 🧪 Testes - SGQ OTI DJ

## 📚 Estrutura de Testes

```
tests/
├── bootstrap.php                    # Bootstrap de inicialização
├── Unit/                           # Testes unitários
│   ├── Core/
│   │   └── RouterTest.php         # Testes do Router
│   ├── Services/
│   │   └── PermissionServiceTest.php
│   └── Middleware/
│       └── PermissionMiddlewareTest.php
└── Feature/                        # Testes de integração
    └── Auth/
        └── LoginTest.php           # Testes de autenticação
```

## 🚀 Como Rodar os Testes

### Todos os Testes
```bash
vendor/bin/phpunit
```

### Apenas Testes Unitários
```bash
vendor/bin/phpunit --testsuite Unit
```

### Apenas Testes de Feature
```bash
vendor/bin/phpunit --testsuite Feature
```

### Com Coverage (HTML)
```bash
vendor/bin/phpunit --coverage-html coverage
```

### Testes Específicos
```bash
vendor/bin/phpunit tests/Unit/Core/RouterTest.php
```

## 📊 Métricas de Cobertura

### Meta de Cobertura
- **Fase 1:** 30% (Atual)
- **Fase 2:** 50% (1 mês)
- **Fase 3:** 70% (3 meses)

### Cobertura Atual
Execute `vendor/bin/phpunit --coverage-text` para ver.

## 🎯 Boas Práticas

### Nomenclatura
- **Classes de teste:** Sufixo `Test` (Ex: `RouterTest`)
- **Métodos de teste:** Prefixo `test` (Ex: `testCanCreateRouter`)
- **Assertions:** Use nomes descritivos

### Estrutura de um Teste
```php
public function testSomeFeature()
{
    // Arrange (Preparar)
    $input = 'test';
    
    // Act (Agir)
    $result = someFunction($input);
    
    // Assert (Afirmar)
    $this->assertEquals('expected', $result);
}
```

### Grupos de Testes
Use annotations para agrupar testes:
```php
/**
 * @group database
 */
public function testDatabaseQuery()
{
    // ...
}
```

Rodar apenas grupo:
```bash
vendor/bin/phpunit --group database
```

## 🔧 Configuração

### phpunit.xml
Configuração principal dos testes está em `phpunit.xml`

### Variáveis de Ambiente
Testes usam variáveis de ambiente definidas em `phpunit.xml`:
- `APP_ENV=testing`
- `DB_DATABASE=sgqpro_test`

### Bootstrap
O arquivo `tests/bootstrap.php` é executado antes de todos os testes.

## 📝 Escrevendo Novos Testes

### 1. Criar arquivo de teste
```bash
# Unit test
touch tests/Unit/Services/NewServiceTest.php

# Feature test
touch tests/Feature/Homologacoes/HomologacoesTest.php
```

### 2. Template básico
```php
<?php
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class NewServiceTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
```

### 3. Rodar o novo teste
```bash
vendor/bin/phpunit tests/Unit/Services/NewServiceTest.php
```

## 🎓 Assertions Comuns

```php
$this->assertTrue($condition);
$this->assertFalse($condition);
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);  // Tipo também
$this->assertNull($value);
$this->assertNotNull($value);
$this->assertEmpty($value);
$this->assertCount(5, $array);
$this->assertInstanceOf(ClassName::class, $object);
$this->assertStringContainsString('needle', $haystack);
```

## 🚫 Testes a Evitar

- ❌ Testes que dependem de ordem de execução
- ❌ Testes que modificam estado global sem limpar
- ❌ Testes que dependem de dados externos (APIs, etc.)
- ❌ Testes muito lentos (use mocks para banco de dados)

## ✅ Checklist do PR

Antes de abrir um PR, garanta que:
- [ ] Todos os testes passam (`vendor/bin/phpunit`)
- [ ] Cobertura não diminuiu
- [ ] Novos métodos têm testes
- [ ] Testes são legíveis e mantêm o padrão

## 📚 Recursos

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Test Driven Development](https://martinfowler.com/bliki/TestDrivenDevelopment.html)
- [PHP Testing Best Practices](https://phpunit.de/manual/current/en/index.html)

---

**Última atualização:** 04/12/2025  
**Versão PHPUnit:** 9.x

