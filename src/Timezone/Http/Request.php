<?php

namespace Mirai\Timezone\Http;

class Request implements RequestInterface
{
    protected $params;
    protected $data;
    protected $body;

    protected $server;
    protected $files;
    protected $cookies;
    protected $headers;


    protected $method = null;
    protected $url = null;


    /**
     * Constructor.
     *
     * @param array $params - The GET parameters
     * @param array $data - The POST parameters
     * @param array $cookies - The COOKIE parameters
     * @param array $files - The FILES parameters
     * @param array $server - The SERVER parameters
     * @param string $body - The raw body data
     * @param array $headers - The headers
     *
     */
    public function __construct(
        array $params = array(),
        array $data = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $body = null,
        array $headers = null
    ) {
        $this->initialize($params, $data, $cookies, $files, $server, $body, $headers);
    }

    public function initialize(
        array $params = array(),
        array $data = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $body = null,
        array $headers = null
    ) {
        $this->params = $params;
        $this->data = $data;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->body = $body;

        if ($headers === null) {
            $headers = array();
        }

        $this->method = mb_strtolower($server['REQUEST_METHOD'] ?? 'GET');

        if (isset($server['HTTP_HOST']) && isset($server['REQUEST_URI'])) {
            $scheme = 'http://';
            if (isset($server['HTTPS']) && $server['HTTPS'] == 'on') {
                $scheme = 'https://';
            }
            $this->url = $scheme . $server['HTTP_HOST'] . $server['REQUEST_URI'];
        } else {
            $this->url = '';
        }

        $this->headers = $headers + $this->getHeadersFromServer($this->server);
    }

    public function getHttpMethod()
    {
        return $this->method;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getQueryParams()
    {
        return $this->params;
    }

    public function getHeadersFromServer($server)
    {
        $headers = array();
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[substr($key, 5)] = $value;
            } elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    public function getBody()
    {
        if (null === $this->body) {
            $this->body = file_get_contents('php://input');
        }

        return $this->body;
    }

    public static function createFromGlobals()
    {
        $class = get_called_class();

        /** @var Request $request */
        $request = new $class($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);

        $requestMethod = $request->method ?? 'GET';

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/x-www-form-urlencoded') === 0
            && in_array(strtoupper($requestMethod), array('PUT', 'DELETE'))) {
            parse_str($request->getBody(), $data);
            $request->data = $data;
        } elseif (strpos($contentType, 'application/json') === 0
            && in_array(strtoupper($requestMethod), array('POST', 'PUT', 'DELETE'))) {
            $data = json_decode($request->getBody(), true);
            $request->data = $data;
        }

        return $request;
    }
}