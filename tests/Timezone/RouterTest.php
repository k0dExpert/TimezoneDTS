<?php

namespace Tests\Timezone;

use Mirai\Timezone\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @covers \Mirai\Timezone\Router
     */
    public function testInvalidUrlException(): void
    {
        $current_url = 'https://';
        $config = ['controller' => 'classController'];

        $this->expectException(\Exception::class);
        $router = new Router($current_url, $config);

        $current_url = '//localhost';
        $this->expectException(\Exception::class);
        $router = new Router($current_url, $config);
    }

    /**
     * @covers \Mirai\Timezone\Router
     */
    public function testEmptyUrlException(): void
    {
        $current_url = 'https://localhost/';
        $config = ['controller' => 'classController'];

        $this->expectException(\Exception::class);
        new Router($current_url, $config);
    }

    /**
     * @covers \Mirai\Timezone\Router
     */
    public function testNotFoundControllerInConfigException(): void
    {
        $current_url = 'https://localhost/controller-test';
        $config_controller = ['controller' => 'classController'];

        $this->expectException(\Exception::class);
        new Router($current_url, $config_controller);
    }

    /**
     * @covers \Mirai\Timezone\Router
     */
    public function testConfigClassController(): void
    {
        $current_url = 'https://localhost/controller/method';
        $config_controller = ['controller' => 'classController'];

        $router = new Router($current_url, $config_controller);
        $this->assertEquals('classController', $router->getClassController());
    }

    /**
     * @covers \Mirai\Timezone\Router::setRoutes()
     */
    public function testMethodRoutes(): void
    {
        $current_url = 'https://localhost/controller/method/';
        $config_controller = ['controller' => 'classController'];
        $router = new Router($current_url, $config_controller);
        $routes = [ '/method' => 'getMethod' ];

        $router->setRoutes($routes);

        $this->assertEquals('getMethod', $router->getMethodController());

        $current_url = 'https://localhost/controller/method';
        $config_controller = ['controller' => 'classController'];
        $router = new Router($current_url, $config_controller);
        $routes = [ '/method' => 'getMethod' ];

        $router->setRoutes($routes);

        $this->assertEquals('getMethod', $router->getMethodController());

        $routes = [ '/method/' => 'getMethod' ];
        $router->setRoutes($routes);

        $this->assertEquals('getMethod', $router->getMethodController());
    }

    /**
     * @covers \Mirai\Timezone\Router::setRoutes()
     */
    public function testArgsRoutes(): void
    {
        $current_url = 'https://localhost/controller/method/param/12';
        $config_controller = ['controller' => 'classController'];
        $router = new Router($current_url, $config_controller);
        $routes = [ '/method/{arg1}/{arg2}' => 'getMethod' ];

        $router->setRoutes($routes);

        $this->assertEquals('getMethod', $router->getMethodController());
        $this->assertEquals(['arg1' => 'param', 'arg2' => '12'], $router->getArgs());
    }
}