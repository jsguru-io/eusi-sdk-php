<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/31/18
 * Time: 7:38 PM
 */

namespace Eusi\Delivery;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Eusi\Exceptions\EusiSDKException;
use function Eusi\jsonMap;
use function Eusi\exceptionAsJson;

/**
 * Class Async
 * 
 * @package Eusi\Delivery
 */
class Async implements AsyncInterface
{
    protected $promise;

    /**
     * Async constructor.
     *
     * @param PromiseInterface $promise
     */
    public function __construct(PromiseInterface $promise)
    {
        $this->promise = $promise;
    }

    /**
     * @param callable|null $onSuccess
     * @param callable|null $onError
     * @return AsyncInterface
     */
    public function then(callable $onSuccess = null, callable $onError = null)
    {
        if ($onSuccess && $onError) {
            return new static($this->promise->then(function (Response $response) use ($onSuccess) {
                $onSuccess(jsonMap($response->getBody()), $response->getStatusCode());
            }, function (Response $response) use (&$onError) {
                $onError(jsonMap($response->getBody()), $response->getStatusCode());
            }));
        }

        if ($onSuccess && !$onError) {
            return new static($this->promise->then(function (Response $response) use ($onSuccess) {
                $onSuccess(jsonMap($response->getBody()), $response->getStatusCode());
            }));
        }

        if (!$onSuccess && $onError) {
            return new static($this->promise->then(null, function (Response $response) use ($onError) {
                $onError(jsonMap($response->getBody()), $response->getStatusCode());
            }));
        }

        return new static($this->promise);
    }

    /**
     * @param bool $silent
     * @return \Eusi\Utils\Json|null|void
     * @throws EusiSDKException
     */
    public function unwrap($silent = true)
    {
        try {

            $response =  $this->promise->wait();

            return jsonMap($response->getBody());

        } catch (RequestException $e) {
            if ($silent) return;
            throw new EusiSDKException($e->getResponse());
        } catch (\Exception $e) {
            if ($silent) return;
            exceptionAsJson($e);
        }
    }
}