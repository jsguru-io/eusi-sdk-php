<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/16/18
 * Time: 10:20 PM
 */

namespace Eusi\Delivery;


use Psr\Http\Message\RequestInterface;

interface HttpQueryInterface
{
    public function request() : RequestInterface;

    public function setUri(string $uri) : HttpQuery;

    public function setQueryParams(array $params) : HttpQuery;

    public function appendQueryParams(array $params) : HttpQuery;

    public function setBody($body = null) : HttpQuery;

    public function setMethod(string $method) : HttpQuery;

    public function setHeaders($headers = [], $replace = false) : HttpQuery;

    public function getHeaders() : array;

    public function getMethod() : string;

    public function getUri() : string;

    public function getQueryParams($asString = true);

    public function getBody();
}