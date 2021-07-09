<?php

namespace Mirai\Timezone\Http;

use InvalidArgumentException;

class Response implements ResponseInterface
{
    public $version;

    protected $statusCode = 200;

    protected $statusText;

    protected $headers = [];

    protected $body = '';

    protected $parameters = [];

    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    public function __construct($parameters = array(), $statusCode = 200, $headers = array())
    {
        $this->setParameters($parameters);
        $this->setStatusCode($statusCode);
        $this->setHeaders($headers);
        $this->version = '1.1';
    }

    public function setStatusCode(int $statusCode, $text = null)
    {
        $this->statusCode = $statusCode;
        if ($this->isInvalid()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $statusCode));
        }

        $this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    public function setHeader(string $name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function addParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name, $default = null)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : $default;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getStatusText()
    {
        return $this->statusText;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    public function send($format = 'json')
    {
        if (headers_sent()) {
            return;
        }

        if ($format == 'json') {
            $this->setHeader('Content-Type', 'application/json');
        }

        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

        foreach ($this->getHeaders() as $name => $header) {
            header(sprintf('%s: %s', $name, $header));
        }

        echo $this->getResponseBody($format);
    }

    public function getResponseBody($format = 'json')
    {
        if ($format == 'json') {
            return $this->parameters ? json_encode($this->parameters) : $this->body;
        }

        throw new InvalidArgumentException(sprintf('The format %s is not supported', $format));
    }

    public function setError($statusCode, $errorDescription = null)
    {
        $parameters = array(
            'status' => 'error',
            'description' => $errorDescription,
        );

        $httpHeaders = array(
            'Cache-Control' => 'no-store'
        );

        $this->setStatusCode($statusCode);
        $this->addParameters($parameters);
        $this->addHeaders($httpHeaders);

        if (!$this->isClientError() && !$this->isServerError()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code is not an error ("%s" given).',
                $statusCode));
        }
    }

    public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
}