<?php
namespace App\Routes;

use App\Core\Router;

/**
 * Route Service Provider
 * Carrega todos os arquivos de rotas do sistema
 */
class RouteServiceProvider
{
    /**
     * Registra todas as rotas do sistema
     */
    public static function register(Router $router): void
    {
        // Rotas públicas (sem autenticação)
        self::loadRouteFile($router, __DIR__ . '/web.php');
        
        // Rotas de API
        self::loadRouteFile($router, __DIR__ . '/api.php');
        
        // Rotas administrativas
        self::loadRouteFile($router, __DIR__ . '/admin.php');
        
        // Módulos do sistema
        self::loadModules($router);
    }
    
    /**
     * Carrega arquivo de rotas individual
     */
    private static function loadRouteFile(Router $router, string $file): void
    {
        if (file_exists($file)) {
            require $file;
        }
    }
    
    /**
     * Carrega todos os módulos de rotas
     */
    private static function loadModules(Router $router): void
    {
        $modulesPath = __DIR__ . '/modules';
        
        if (!is_dir($modulesPath)) {
            return;
        }
        
        $modules = glob($modulesPath . '/*.php');
        
        foreach ($modules as $module) {
            require $module;
        }
    }
}
