<?php
namespace Eusi\Delivery;


use Eusi\Auth\BearerToken;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class HttpQuery
 *
 * @package Eusi\Delivery
 */
class HttpQuery implements HttpQueryInterface
{
    protected $targetEntity;

    protected $uri;

    protected $queryParams = [];

    protected $method;

    protected $headers = ['Content-Type' => 'application/json'];

    protected $body;

    /**
     * HttpQuery constructor.
     *
     * @param BearerToken $accessToken
     * @param array $options
     */
    public function __construct(BearerToken $accessToken, $options = [])
    {
        $this->headers['Authorization'] = $accessToken;

        if (isset($options['uri'])) {
            $this->setUri($options['uri']);
        }

        if (isset($options['method'])) {
            $this->setMethod($options['method']);
        }

        if (isset($options['headers'])) {
            $this->setHeaders($options['headers']);
        }

        if (isset($options['body'])) {
            $this->setBody($options['body']);
        }
    }

    public function setHeaders($headers = [], $replace = false) : HttpQuery
    {
        if ($replace) {
            $this->headers = array_merge($this->headers, $headers);
        } else {
            $this->headers += $headers;
        }
        return $this;
    }

    public function setBody($body = null) : HttpQuery
    {
        $this->body = $body;
        return $this;
    }

    public function setQueryParams(array $params) : HttpQuery
    {
        $this->queryParams = $params;
        return $this;
    }

    public function appendQueryParams(array $params) : HttpQuery
    {
        $this->queryParams[] = $params;
        return $this;
    }

    public function setUri(string $uri) : HttpQuery
    {
        $this->uri = $uri;
        return $this;
    }

    public function setMethod(string $method) : HttpQuery
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUri() : string
    {
        return $this->uri;
    }

    public function getQueryParams($asString = true)
    {
        if ($asString) {
            $queryString = [];

            if (!empty($this->queryParams)) {
                foreach ($this->queryParams as $i => $list) {
                    $queryString[] = http_build_query($list);
                }
                return '?'.implode('&', $queryString);
            }
            return '';
        }

        return $this->queryParams;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function request() : RequestInterface
    {
        return new Request(
            $this->getMethod(),
            ltrim($this->getUri(), '/') . $this->getQueryParams(),
            $this->getHeaders(),
            $this->getBody()
        );
    }
}