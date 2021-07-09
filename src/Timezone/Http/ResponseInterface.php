<?php

namespace Mirai\Timezone\Http;

interface ResponseInterface
{
    public function addParameters(array $parameters);

    public function addHeaders(array $headers);

    public function setStatusCode(int $statusCode);

    public function setError($statusCode, $errorDescription = null);

    public function getParameter($name);
}