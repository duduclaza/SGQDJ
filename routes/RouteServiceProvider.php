<?php
/**
 * Route Service Provider
 * Carrega todos os arquivos de rotas do sistema de forma modular
 */

class RouteServiceProvider
{
    /**
     * Registra todas as rotas do sistema
     */
    public static function register($router): void
    {
        $basePath = __DIR__;
        
        // Rotas administrativas
        self::loadRouteFile($router, $basePath . '/admin.php');
        
        // Rotas de API
        self::loadRouteFile($router, $basePath . '/api.php');
        
        // Rotas diversas (web)
        self::loadRouteFile($router, $basePath . '/web.php');
        
        // Módulos do sistema
        self::loadModules($router, $basePath . '/modules');
    }
    
    /**
     * Carrega arquivo de rotas individual
     */
    private static function loadRouteFile($router, string $file): void
    {
        if (file_exists($file)) {
            require $file;
        }
    }
    
    /**
     * Carrega todos os módulos de rotas
     */
    private static function loadModules($router, string $modulesPath): void
    {
        if (!is_dir($modulesPath)) {
            return;
        }
        
        $modules = glob($modulesPath . '/*.php');
        
        foreach ($modules as $module) {
            require $module;
        }
    }
}
