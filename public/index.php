<?php
require '../vendor/autoload.php';

use Mirai\Timezone\Config;
use Mirai\Timezone\App;
use Mirai\Timezone\Http\Response;

date_default_timezone_set('UTC');

try {
    $config = require('../src/config.php');
    $config = new Config($config);

    $app = new App($config);
    $app->Run();

} catch (Exception $e) {

    $response = new Response();
    $response->setError(400, $e->getMessage());
    $response->send();

}