<?php

namespace Mirai\Timezone\Controller;

use Mirai\Timezone\Http\RequestInterface;
use Mirai\Timezone\RouterInterface;

class ControllerFactory
{
    public static function build(RouterInterface $router, RequestInterface $request): Controller
    {
        /**
         * @var Controller $controller
         */
        $classController = $router->getClassController();
        if (!class_exists($classController)) {
            throw new \Exception('Class controller not found');
        }

        $controller = new $classController($router, $request);
        $arHttpRoutes = $controller->getRoutes();
        $arRoutes = $arHttpRoutes[$request->getHttpMethod()] ?? [];

        $router->setRoutes($arRoutes);
        $method = $router->getMethodController();
        if (!method_exists($controller, $method)) {
            throw new \Exception('The method obtained in routing was not found in the class');
        }

        return $controller;
    }
}