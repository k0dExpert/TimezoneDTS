<?php

namespace Mirai\Timezone\Controller;

use Mirai\Timezone\Http\Response;
use Mirai\Timezone\RouterInterface;
use Mirai\Timezone\Http\RequestInterface;
use Mirai\Timezone\Http\ResponseInterface;
use Mirai\Timezone\Config;

abstract class Controller
{
    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @var RequestInterface $request
     */
    protected $request;

    /**
     * @var ResponseInterface $request
     */
    protected $response;


    /**
     * @var Config $config
     */
    protected $config;


    public function __construct(RouterInterface $router, RequestInterface $request)
    {
        $this->router = $router;
        $this->request = $request;

    }

    abstract public function getRoutes();

    protected function beforeRun()
    {

    }

    protected function afterRun()
    {

    }

    public function Run(Config $config)
    {
        $this->config = $config;
        $this->response = new Response();

        $this->beforeRun();
        $this->{$this->router->getMethodController()}();
        $this->afterRun();

        $this->response->send();


    }
}
