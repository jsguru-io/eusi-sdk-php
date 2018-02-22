<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/10/18
 * Time: 8:34 PM
 */

namespace Eusi;


use Eusi\Exceptions\EusiSDKException;
use Eusi\Utils\Config;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;

/**
 * Base Client Class
 *
 * @package Eusi
 */
abstract class Client
{
    use ContentType;

    /**
     * @var string
     */
    private $bucketId;

    /**
     * @var string
     */
    private $bucketSecret;

    /**
     * @var string|null
     */
    protected $host;

    /**
     * @var string|null
     */
    protected $apiVersion;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    public $defaultHeaders = ['Content-Type' => 'application/json'];

    /**
     * @var array
     */
    protected $config;

    /**
     * Client constructor.
     *
     * @param string $bucketId
     * @param string $bucketSecret
     * @param null $host
     * @param null $apiVersion
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct(string $bucketId, string $bucketSecret, $host = null, $apiVersion = null, HttpClientInterface $httpClient = null)
    {
        $this->bucketId = $bucketId;

        $this->bucketSecret = $bucketSecret;

        $this->apiVersion = $apiVersion ?? 'v1';

        $this->host = $host ?? 'https://delivery.eusi.cloud';

        $this->setHttpClient($httpClient ?? new HttpClient([
            'base_uri' => $this->getBaseUrl($bucketId),
            'timeout' => 60
        ]));
    }

    /**
     * @param $bucketId
     * @return string
     */
    public function getBaseUrl($bucketId)
    {
        return "{$this->host}/api/{$this->apiVersion}/$bucketId/";
    }

    /**
     * @return string
     */
    public function getApiVerison()
    {
        return $this->apiVersion;
    }

    /**
     * @return string
     */
    public function getBucketId()
    {
        return $this->bucketId;
    }

    /**
     * @return string
     */
    public function getBucketSecret()
    {
        return $this->bucketSecret;
    }

    /**
     * Set SDK Http client
     *
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Make request object
     *
     * @param $method
     * @param $endpoint
     * @param array $headers
     * @param null $body
     * @return RequestInterface
     */
    public function request($method, $endpoint, array $headers = [], $body = null)
    {
        $headers = array_merge($this->defaultHeaders, $headers);

        if ($this->isJsonRequest($headers) && !is_string($body)) {
            $body = jsonEncode($body);
        } else if ($this->isUrlEncodedRequest($headers) && is_array($body)) {
            $body = http_build_query($body ?? []);
        }

        return new Request($method, ltrim($endpoint, '/'), $headers, $body);
    }

    /**
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws EusiSDKException
     */
    public function sendRequest(RequestInterface $request)
    {
        try {
            return $this->httpClient->send($request);
        } catch (ClientException $e) {
            throw new EusiSDKException($e->getResponse());
        } catch (ServerException $e) {
            throw new EusiSDKException($e->getResponse());
        } catch (GuzzleException $e) {
            exceptionAsJson($e);
        }
    }

    /**
     * @param RequestInterface $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws EusiSDKException
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        try {
            return $this->httpClient->sendAsync($request);
        } catch (ClientException $e) {
            throw new EusiSDKException($e->getResponse());
        } catch (ServerException $e) {
            throw new EusiSDKException($e->getResponse());
        } catch (GuzzleException $e) {
            exceptionAsJson($e);
        }
    }
}