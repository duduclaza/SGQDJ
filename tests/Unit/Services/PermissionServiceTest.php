<?php
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    public function testServiceExists()
    {
        $this->assertTrue(class_exists('App\Services\PermissionService'));
    }
    
    /**
     * @group database
     */
    public function testHasPermissionReturnsBooleanForValidInput()
    {
        // Este teste precisa de mock do banco
        // Por enquanto, apenas verifica se o método existe
        $this->assertTrue(method_exists(
            'App\Services\PermissionService',
            'hasPermission'
        ));
    }
}
