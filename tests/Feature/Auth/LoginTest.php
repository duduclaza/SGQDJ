<?php
namespace Tests\Feature\Auth;

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public function testLoginPageExists()
    {
        // Teste básico: verifica se o controller de login existe
        $this->assertTrue(class_exists('App\Controllers\AuthController'));
        $this->assertTrue(method_exists('App\Controllers\AuthController', 'login'));
    }
    
    public function testAuthenticateMethodExists()
    {
        $this->assertTrue(method_exists('App\Controllers\AuthController', 'authenticate'));
    }
    
    public function testLogoutMethodExists()
    {
        $this->assertTrue(method_exists('App\Controllers\AuthController', 'logout'));
    }
}
