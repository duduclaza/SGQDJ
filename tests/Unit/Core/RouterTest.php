<?php
namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
    private Router $router;
    
    protected function setUp(): void
    {
        $this->router = new Router(__DIR__);
    }
    
    public function testCanCreateRouterInstance()
    {
        $this->assertInstanceOf(Router::class, $this->router);
    }
    
    public function testCanRegisterGetRoute()
    {
        $this->router->get('/test', function() {
            return 'test';
        });
        
        // Router foi criado sem erros
        $this->assertTrue(true);
    }
    
    public function testCanRegisterPostRoute()
    {
        $this->router->post('/test', function() {
            return 'test';
        });
        
        $this->assertTrue(true);
    }
    
    public function testCanRegisterDeleteRoute()
    {
        $this->router->delete('/test/{id}', function($id) {
            return 'deleted: ' . $id;
        });
        
        $this->assertTrue(true);
    }
}
