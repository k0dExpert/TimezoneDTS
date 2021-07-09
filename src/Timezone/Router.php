<?php

namespace Mirai\Timezone;

use Mirai\Timezone\RouterInterface;

class Router implements RouterInterface
{
    private $class = '';
    private $method = '';
    private $args = [];

    private $url_parts;

    public function __construct(string $current_url, Array $configControllers, $url_prefix = '')
    {
        $urls = parse_url($current_url);

        if ($urls === false || !isset($urls["path"])) {
            throw new \Exception('Invalid url');
        }

        $this->url_parts = explode("/", trim($urls["path"], "/"));

        if ($url_prefix && $this->url_parts[0] == $url_prefix) {
            array_shift($this->url_parts);
        }

        if (!isset($this->url_parts[0])) {
            throw new \Exception('The controller is not specified in the url');
        }

        if (!isset($configControllers[$this->url_parts[0]])) {
            throw new \Exception('The requested controller was not found in the settings');
        }

        $this->class = $configControllers[$this->url_parts[0]];
        array_shift($this->url_parts);
    }

    private function initRoutes(): void
    {
        $this->method = '';
        $this->args = [];
    }

    public function setRoutes(Array $routes): void
    {
        $this->initRoutes();

        $url = implode('/', $this->url_parts);
        $url_parts = explode("/", trim($url, "/"));

        foreach ($routes as $route => $method) {
            $route_parts = explode("/", trim($route, "/"));
            if (count($route_parts) != count($url_parts)) {
                continue;
            }

            $args = [];
            $is_similar = true;
            foreach ($route_parts as $key => $_route) {
                if (preg_match('/^\{(.*)\}$/', $_route, $matches)) {
                    $args[$matches[1]] = $url_parts[$key];
                } elseif ($_route == $url_parts[$key]) {
                    continue;
                } else {
                    $is_similar = false;
                    break;
                }
            }

            if ($is_similar) {
                $this->method = $method;
                $this->args = $args;
                break;
            }
        }
    }

    public function getClassController(): string
    {
        if (empty($this->class)) {
            throw new \Exception('$router->class is not specified');
        }

        return $this->class;
    }

    public function getMethodController(): string
    {
        if (empty($this->method)) {
            throw new \Exception('$router->method is not specified');
        }

        return $this->method;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}