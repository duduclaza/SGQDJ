# 🛠️ RECOMENDAÇÕES TÉCNICAS PRÁTICAS - SGQ OTI DJ

**Data:** 04/12/2025  
**Para:** Equipe de Desenvolvimento  
**Objetivo:** Guia prático de melhorias implementáveis

---

## 🎯 PRIORIZAÇÃO DE AÇÕES

### Legenda de Prioridades
- 🔴 **P0 - CRÍTICO:** Ação imediata (1-2 semanas)
- 🟡 **P1 - ALTO:** Curto prazo (1 mês)
- 🟢 **P2 - MÉDIO:** Médio prazo (2-3 meses)
- ⚪ **P3 - BAIXO:** Longo prazo (3-6 meses)

---

## 🔴 PRIORIDADE 0 - CRÍTICO (1-2 SEMANAS)

### 1. Remover Debug Mode em Produção

**Problema:**
```php
// public/index.php, linha 26-28
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    $isDebug = true;  // ⚠️ PERIGO!
}
```

**Solução:**
```php
// Remover completamente ou limitar por IP
$allowedDebugIPs = ['127.0.0.1', '::1'];
$isDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

if ($isDebug && !in_array($_SERVER['REMOTE_ADDR'], $allowedDebugIPs)) {
    $isDebug = false;
}
```

**Impacto:** High - Segurança  
**Esforço:** 30 minutos  
**Responsável:** Dev Lead

---

### 2. Proteger .env no Git

**Problema:**
- `.env` pode estar no repositório Git
- Contém credenciais sensíveis

**Solução:**
```bash
# Verificar se .env está no git
git status

# Se estiver, remover do histórico
git rm --cached .env

# Garantir que .gitignore tem:
echo ".env" >> .gitignore
git add .gitignore
git commit -m "Proteger .env"
```

**Criar .env.example:**
```env
APP_NAME="SGQ OTI DJ"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://djbr.sgqoti.com.br

DB_HOST=your_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_HOST=your_smtp_host
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

**Impacto:** High - Segurança  
**Esforço:** 15 minutos  
**Responsável:** DevOps

---

### 3. Implementar CSRF Protection

**Problema:**
- Sem proteção contra CSRF
- Formulários vulneráveis

**Solução - Adicionar em helpers.php:**
```php
/**
 * Gera token CSRF
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 */
function csrf_validate($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Campo hidden de CSRF
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}
```

**Uso nos formulários:**
```html
<form method="POST" action="/homologacoes/store">
    <?= csrf_field() ?>
    <!-- resto do formulário -->
</form>
```

**Validação nos controllers:**
```php
// Início de cada método POST
public function store() {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        return json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    }
    // continua...
}
```

**Impacto:** High - Segurança  
**Esforço:** 4 horas  
**Responsável:** Dev Senior

---

## 🟡 PRIORIDADE 1 - ALTO (1 MÊS)

### 4. Refatorar index.php

**Problema:**
- 708 linhas em um único arquivo
- Dificulta manutenção

**Solução - Criar estrutura de rotas:**

```
src/
└── Routes/
    ├── web.php           # Rotas públicas
    ├── api.php           # Rotas de API
    ├── admin.php         # Rotas admin
    ├── modules/
    │   ├── toners.php
    │   ├── homologacoes.php
    │   ├── pops-its.php
    │   └── ...
    └── RouteServiceProvider.php
```

**RouteServiceProvider.php:**
```php
<?php
namespace App\Routes;

use App\Core\Router;

class RouteServiceProvider
{
    public static function register(Router $router): void
    {
        // Carregar rotas em ordem
        self::loadRoutes($router, 'web.php');
        self::loadRoutes($router, 'api.php');
        self::loadRoutes($router, 'admin.php');
        
        // Módulos
        $modules = glob(__DIR__ . '/modules/*.php');
        foreach ($modules as $module) {
            require $module;
        }
    }
    
    private static function loadRoutes(Router $router, string $file): void
    {
        $routeFile = __DIR__ . '/' . $file;
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
```

**modules/homologacoes.php:**
```php
<?php
// Homologações routes
$router->get('/homologacoes', [App\Controllers\HomologacoesKanbanController::class, 'index']);
$router->post('/homologacoes/store', [App\Controllers\HomologacoesKanbanController::class, 'store']);
$router->post('/homologacoes/update-status', [App\Controllers\HomologacoesKanbanController::class, 'updateStatus']);
// ...
```

**Novo public/index.php:**
```php
<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Routes\RouteServiceProvider;
use App\Middleware\PermissionMiddleware;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Error reporting
$isDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
if ($isDebug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// Create router
$router = new Router(__DIR__);

// Register all routes
RouteServiceProvider::register($router);

// Dispatch
try {
    $currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    $isPublicRoute = /* ... lógica de rotas públicas ... */;
    
    if (!$isPublicRoute) {
        PermissionMiddleware::handle($currentRoute, $method);
    }
    
    $router->dispatch();
    
} catch (\Exception $e) {
    // Error handling
}
```

**Impacto:** High - Manutenibilidade  
**Esforço:** 16 horas  
**Responsável:** Dev Senior + Dev Pleno

---

### 5. Quebrar AdminController

**Problema:**
- 133 KB em um único arquivo
- ~4000 linhas de código

**Solução - Dividir em múltiplos controllers:**

```
src/Controllers/Admin/
├── DashboardController.php      # Dashboard e métricas
├── UsersController.php           # CRUD de usuários
├── ProfilesController.php        # Gestão de perfis
├── PermissionsController.php     # Permissões
├── InvitationsController.php     # Convites
└── DiagnosticsController.php     # Diagnósticos
```

**Exemplo - DashboardController.php:**
```php
<?php
namespace App\Controllers\Admin;

use App\Config\Database;

class DashboardController
{
    public function index()
    {
        // Lógica do dashboard
        $title = 'Dashboard - SGQ OTI DJ';
        $viewFile = __DIR__ . '/../../views/admin/dashboard.php';
        include __DIR__ . '/../../views/layouts/main.php';
    }
    
    public function getDashboardData()
    {
        // Retorna JSON com dados do dashboard
        $db = Database::getInstance();
        
        $data = [
            'toners' => $this->getTonerStats($db),
            'homologacoes' => $this->getHomologacoesStats($db),
            // ...
        ];
        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    private function getTonerStats($db)
    {
        // Lógica específica
    }
}
```

**Atualizar rotas:**
```php
// Antes
$router->get('/admin/dashboard/data', [App\Controllers\AdminController::class, 'getDashboardData']);

// Depois
$router->get('/admin/dashboard/data', [App\Controllers\Admin\DashboardController::class, 'getDashboardData']);
```

**Impacto:** High - Manutenibilidade  
**Esforço:** 24 horas  
**Responsável:** 2 Devs Seniores

---

### 6. Implementar Testes Unitários Básicos

**Instalar PHPUnit:**
```bash
composer require --dev phpunit/phpunit
```

**phpunit.xml:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         verbose="true"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Estrutura de testes:**
```
tests/
├── Unit/
│   ├── Services/
│   │   ├── PermissionServiceTest.php
│   │   └── EmailServiceTest.php
│   └── Core/
│       └── RouterTest.php
└── Feature/
    ├── Auth/
    │   ├── LoginTest.php
    │   └── RegisterTest.php
    └── Homologacoes/
        └── HomologacoesTest.php
```

**Exemplo - PermissionServiceTest.php:**
```php
<?php
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PermissionService;

class PermissionServiceTest extends TestCase
{
    public function testHasPermissionReturnsTrueForAdmin()
    {
        // Arrange
        $userId = 1; // Admin
        $module = 'dashboard';
        $action = 'view';
        
        // Act
        $result = PermissionService::hasPermission($userId, $module, $action);
        
        // Assert
        $this->assertTrue($result);
    }
    
    public function testHasPermissionReturnsFalseForUnauthorizedUser()
    {
        // Arrange
        $userId = 999; // Usuário sem permissões
        $module = 'admin_panel';
        $action = 'edit';
        
        // Act
        $result = PermissionService::hasPermission($userId, $module, $action);
        
        // Assert
        $this->assertFalse($result);
    }
}
```

**Rodar testes:**
```bash
vendor/bin/phpunit
```

**Meta de Cobertura:**
- Fase 1: 30% coverage
- Fase 2: 50% coverage
- Fase 3: 70% coverage

**Impacto:** High - Qualidade  
**Esforço:** 40 horas  
**Responsável:** Equipe completa

---

## 🟢 PRIORIDADE 2 - MÉDIO (2-3 MESES)

### 7. Implementar Sistema de Migrations

**Instalar Phinx:**
```bash
composer require robmorgan/phinx
vendor/bin/phinx init
```

**phinx.php:**
```php
<?php
return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations',
        'seeds' => __DIR__ . '/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'pass' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'],
            'charset' => 'utf8mb4',
        ],
        'development' => [
            // ...
        ]
    ]
];
```

**Criar migration:**
```bash
vendor/bin/phinx create CreateUsersTable
```

**Exemplo - 20251204_create_users_table.php:**
```php
<?php
use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password', 'string', ['limit' => 255])
              ->addColumn('profile_id', 'integer')
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['email'], ['unique' => true])
              ->addForeignKey('profile_id', 'profiles', 'id', [
                  'delete' => 'RESTRICT',
                  'update' => 'CASCADE'
              ])
              ->create();
    }
}
```

**Rodar migrations:**
```bash
vendor/bin/phinx migrate
```

**Impacto:** Medium - Manutenibilidade  
**Esforço:** 32 horas  
**Responsável:** Dev Senior

---

### 8. Compilar Tailwind CSS Localmente

**Instalar dependências:**
```bash
npm init -y
npm install -D tailwindcss@latest postcss autoprefixer
npx tailwindcss init
```

**tailwind.config.js:**
```javascript
module.exports = {
  content: [
    "./views/**/*.php",
    "./public/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'sgq-blue': '#1e40af',
        'sgq-green': '#10b981',
      }
    },
  },
  plugins: [],
}
```

**public/assets/css/input.css:**
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
.btn-primary {
  @apply bg-sgq-blue text-white px-4 py-2 rounded hover:bg-blue-700;
}
```

**package.json:**
```json
{
  "scripts": {
    "build:css": "tailwindcss -i public/assets/css/input.css -o public/assets/css/output.css --minify",
    "watch:css": "tailwindcss -i public/assets/css/input.css -o public/assets/css/output.css --watch"
  }
}
```

**Build:**
```bash
npm run build:css
```

**Layout - Substituir CDN:**
```html
<!-- Antes -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Depois -->
<link rel="stylesheet" href="/assets/css/output.css">
```

**Impacto:** Medium - Performance  
**Esforço:** 8 horas  
**Responsável:** Frontend Dev

---

### 9. Implementar Cache com Redis

**Instalar Redis:**
```bash
# No servidor
sudo apt install redis-server
sudo systemctl enable redis
sudo systemctl start redis
```

**Instalar cliente PHP:**
```bash
composer require predis/predis
```

**CacheService.php:**
```php
<?php
namespace App\Services;

use Predis\Client;

class CacheService
{
    private static ?Client $redis = null;
    
    private static function getClient(): Client
    {
        if (self::$redis === null) {
            self::$redis = new Client([
                'scheme' => 'tcp',
                'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port'   => $_ENV['REDIS_PORT'] ?? 6379,
            ]);
        }
        return self::$redis;
    }
    
    public static function get(string $key)
    {
        $value = self::getClient()->get($key);
        return $value ? json_decode($value, true) : null;
    }
    
    public static function set(string $key, $value, int $ttl = 3600): void
    {
        self::getClient()->setex($key, $ttl, json_encode($value));
    }
    
    public static function forget(string $key): void
    {
        self::getClient()->del($key);
    }
    
    public static function flush(): void
    {
        self::getClient()->flushdb();
    }
}
```

**Uso nos controllers:**
```php
public function getDashboardData()
{
    $cacheKey = 'dashboard_data_user_' . $_SESSION['user_id'];
    
    // Tentar cache primeiro
    $data = CacheService::get($cacheKey);
    
    if ($data === null) {
        // Cache miss - buscar do banco
        $db = Database::getInstance();
        $data = [
            'toners' => $this->getTonerStats($db),
            'homologacoes' => $this->getHomologacoesStats($db),
        ];
        
        // Salvar no cache (1 hora)
        CacheService::set($cacheKey, $data, 3600);
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
}
```

**Impacto:** High - Performance  
**Esforço:** 16 horas  
**Responsável:** Backend Dev

---

## ⚪ PRIORIDADE 3 - BAIXO (3-6 MESES)

### 10. Implementar CI/CD com GitHub Actions

**.github/workflows/ci.yml:**
```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
          extensions: mbstring, pdo, pdo_mysql
          
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
        
      - name: Run tests
        run: vendor/bin/phpunit
        
      - name: Check code style
        run: vendor/bin/phpcs --standard=PSR12 src/

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v2
      
      - name: Deploy to production
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            cd /var/www/sgq
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php vendor/bin/phinx migrate
```

**Impacto:** High - Produtividade  
**Esforço:** 24 horas  
**Responsável:** DevOps + Dev Lead

---

### 11. Implementar APM (Sentry)

**Instalar Sentry:**
```bash
composer require sentry/sentry
```

**Configurar .env:**
```env
SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id
```

**Adicionar no index.php:**
```php
use Sentry\State\Scope;

\Sentry\init([
    'dsn' => $_ENV['SENTRY_DSN'],
    'environment' => $_ENV['APP_ENV'],
    'traces_sample_rate' => 1.0,
]);

// No catch de exceções
catch (\Exception $e) {
    \Sentry\captureException($e);
    // resto do error handling
}
```

**Capturar contexto de usuário:**
```php
\Sentry\configureScope(function (Scope $scope): void {
    $scope->setUser([
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
    ]);
});
```

**Impacto:** High - Monitoramento  
**Esforço:** 8 horas  
**Responsável:** DevOps

---

## 📋 CHECKLIST DE IMPLEMENTAÇÃO

### Sprint 1 (Semana 1-2) - CRÍTICO
- [ ] Remover debug mode em produção
- [ ] Proteger .env no git
- [ ] Implementar CSRF protection em formulários principais
- [ ] Adicionar sanitização de inputs (htmlspecialchars)
- [ ] Revisar e documentar variáveis de ambiente

### Sprint 2 (Semana 3-4) - REFATORAÇÃO INICIAL
- [ ] Criar estrutura de rotas separadas
- [ ] Migrar rotas de toners para módulo separado
- [ ] Migrar rotas de homologações
- [ ] Refatorar index.php para usar RouteServiceProvider
- [ ] Testar todas as rotas migradas

### Sprint 3 (Semana 5-8) - TESTES E CONTROLLERS
- [ ] Configurar PHPUnit
- [ ] Escrever testes para PermissionService
- [ ] Escrever testes para Router
- [ ] Iniciar quebra de AdminController
- [ ] Criar DashboardController separado
- [ ] Criar UsersController separado

### Sprint 4 (Mês 2) - PERFORMANCE E QUALIDADE
- [ ] Compilar Tailwind CSS localmente
- [ ] Configurar Redis em dev
- [ ] Implementar cache em endpoints do dashboard
- [ ] Atingir 30% code coverage
- [ ] Documentar APIs principais

### Sprint 5 (Mês 3) - INFRAESTRUTURA
- [ ] Implementar sistema de migrations
- [ ] Migrar scripts SQL para Phinx
- [ ] Configurar CI/CD básico
- [ ] Deploy Redis em produção
- [ ] Configurar Sentry

---

## 📊 MÉTRICAS DE SUCESSO

### KPIs por Sprint

| Sprint | Métrica | Meta |
|--------|---------|------|
| **1** | Vulnerabilidades críticas | 0 |
| **2** | Linhas em index.php | < 200 |
| **3** | Code coverage | 30% |
| **4** | Performance homepage | < 500ms |
| **5** | Deploy time | < 5min |

---

## 💰 ESTIMATIVA DE CUSTOS

### Recursos Humanos

| Papel | Horas | Custo/h | Total |
|-------|-------|---------|-------|
| Dev Senior | 100h | R$ 100 | R$ 10.000 |
| Dev Pleno | 60h | R$ 70 | R$ 4.200 |
| DevOps | 40h | R$ 90 | R$ 3.600 |
| Frontend | 20h | R$ 60 | R$ 1.200 |
| **TOTAL** | **220h** | - | **R$ 19.000** |

### Infraestrutura

| Serviço | Custo Mensal |
|---------|--------------|
| Redis | R$ 50 |
| Sentry (Basic) | R$ 100 |
| CI/CD (GitHub) | R$ 0 (free) |
| **TOTAL** | **R$ 150/mês** |

**ROI Esperado:**
- Redução de 80% em bugs de produção
- Deploy 10x mais rápido
- Performance 2x melhor
- Satisfação do time +50%

---

## 📞 PRÓXIMOS PASSOS

1. **Reunião de alinhamento** com equipe (1h)
2. **Priorizar sprints** conforme capacidade do time
3. **Criar issues** no sistema de gestão de projetos
4. **Definir responsáveis** para cada tarefa
5. **Iniciar Sprint 1** imediatamente

---

**Documento preparado por:** Antigravity AI  
**Contato:** Para dúvidas, consultar dev lead  
**Atualização:** Revisar mensalmente

