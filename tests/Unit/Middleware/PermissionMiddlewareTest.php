<?php
namespace Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    public function testMiddlewareClassExists()
    {
        $this->assertTrue(class_exists('App\Middleware\PermissionMiddleware'));
    }
    
    public function testCheckRoutePermissionMethodExists()
    {
        $this->assertTrue(method_exists(
            'App\Middleware\PermissionMiddleware',
            'checkRoutePermission'
        ));
    }
    
    public function testHandleMethodExists()
    {
        $this->assertTrue(method_exists(
            'App\Middleware\PermissionMiddleware',
            'handle'
        ));
    }
}
