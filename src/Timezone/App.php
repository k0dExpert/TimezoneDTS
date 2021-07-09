<?php


namespace Mirai\Timezone;

use Mirai\Timezone\Config;
use Mirai\Timezone\Router;
use Mirai\Timezone\Http\Request;
use Mirai\Timezone\Controller\ControllerFactory;

class App
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function Run()
    {
        $request = Request::createFromGlobals();

        $url_prefix = $this->config->get('routes.url_prefix') ?? '';
        $router = new Router($request->getUrl(), $this->config->get('routes.controllers'), $url_prefix);

        $controller = ControllerFactory::build($router, $request);
        $controller->Run($this->config);
    }
}