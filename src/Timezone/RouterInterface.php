<?php

namespace Mirai\Timezone;

interface RouterInterface
{
    public function getClassController(): string;

    public function getMethodController(): string;

    public function getArgs(): array;

    public function setRoutes(Array $routes): void;
}