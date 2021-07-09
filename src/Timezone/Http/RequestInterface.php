<?php


namespace Mirai\Timezone\Http;


interface RequestInterface
{
    public function getHttpMethod();

    public function getData();

    public function getUrl();

    public static function createFromGlobals();
}