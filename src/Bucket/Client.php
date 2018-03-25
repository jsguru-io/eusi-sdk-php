<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/11/18
 * Time: 1:38 AM
 */

namespace Eusi\Bucket;


use Eusi\Exceptions\EusiSDKException;
use function Eusi\str_has;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Request;
use function Eusi\exceptionAsJson;
use function Eusi\jsonEncode;

/**
 * Class Client
 *
 * @package Eusi\Bucket
 * @method ResponseInterface get(string $endpoint, $headers = [])
 * @method ResponseInterface head(string $endpoint, $headers = [])
 * @method ResponseInterface put(string $endpoint, $body, $headers = [])
 * @method ResponseInterface post(string $endpoint, $body, $headers = [])
 * @method ResponseInterface patch(string $endpoint, $body, $headers = [])
 * @method ResponseInterface delete(string $endpoint, $headers = [])
 * @method ResponseInterface options(string $endpoint, $headers = [])
 */
class Client extends \Eusi\Client
{
    const DELIVERY_PATH = '/items';

    const FORMS_PATH = '/forms';

    const TAXONOMY_PATH = '/taxonomy';

    /**
     * @var string|null
     */
    protected $deliveryPath;

    /**
     * @var string|null
     */
    protected $formsPath;

    /**
     * @var string|null
     */
    protected $taxonomyPath;

    /**
     * @var int
     */
    protected static $requestCount = 0;

    /**
     * @var ResponseInterface
     */
    protected $lastResponse;

    /**
     * @var RequestInterface
     */
    protected $lastRequest;

    /**
     * @return RequestInterface
     */
    public function getLastRequest() : RequestInterface
    {
        return $this->lastRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse() : ResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * @return int
     */
    public static function getRequestCount() : int
    {
        return self::$requestCount;
    }

    /**
     * @param string $query
     * @return string
     */
    public function getItemsEndpoint($query = '')
    {
        return static::DELIVERY_PATH . $query;
    }

    /**
     * @param $identifier
     * @return string
     */
    public function getFormsEndpoint($identifier)
    {
        return static::FORMS_PATH . "/$identifier";
    }

    /**
     * @param $identifier
     * @return string
     */
    public function getTaxonomyEndpoint($identifier)
    {
        return static::TAXONOMY_PATH . "/$identifier";
    }

    /**
     * @param $name
     * @param $args
     * @throws EusiSDKException
     */
    public function __call($name, $args)
    {
        switch (strtolower($name)) {
            case 'head':
            case 'get':
            case 'options':
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':

                $endpoint = $args[0];

                $body = null;

                if ($name == 'post' || $name == 'put' || $name == 'patch') {

                    $body = $args[1];

                    $headers = $args[2] ?? [];

                } else {
                    $headers = $args[1] ?? [];
                }

                if (!is_array($headers)) {
                    exceptionAsJson(new \InvalidArgumentException("Argument ".func_num_args()." needs to be an array."));
                }

                $headers = array_merge($this->defaultHeaders, $headers);

                try {

                    if ($this->isJsonRequest($headers) && is_array($body)) {
                        $body = jsonEncode($body);
                    } else if ($this->isUrlEncodedRequest($headers) && is_array($body)) {
                        $body = http_build_query($body ?? []);
                    }

                    $request = new Request(strtoupper($name), ltrim($endpoint, '/'), $headers, $body);

                    return $this->sendRequest($request);

                } catch (ClientException $e) {
                    throw new EusiSDKException($e->getResponse());
                } catch (ServerException $e) {
                    throw new EusiSDKException($e->getResponse());
                } catch (GuzzleException $e) {
                    exceptionAsJson($e);
                }

            default:
                exceptionAsJson(new \BadMethodCallException());
                break;
        }
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws EusiSDKException
     */
    public function sendRequest(RequestInterface $request)
    {
        $this->lastRequest = $request;

        static::$requestCount++;

        $response = parent::sendRequest($request);

        $this->lastResponse = $request;

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws EusiSDKException
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        $this->lastRequest = $request;

        static::$requestCount++;

        $response = parent::sendAsyncRequest($request);

        $this->lastResponse = $request;

        return $response;
    }
}