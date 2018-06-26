<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 2/22/18
 * Time: 1:03 PM
 */

namespace Eusi\Delivery;


use function Eusi\jsonDecode;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;
use function Eusi\exceptionAsJson;

class AsyncForm extends AbstractAsync implements AsyncInterface
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
            return new static($this->promise->then(function (Response $response) use (&$onSuccess) {
                $onSuccess(jsonDecode($response->getBody()), $response->getStatusCode());
            }, function (Response $response) use (&$onError) {
                $onError($response->getBody(), $response->getStatusCode());
            }));
        }

        if ($onSuccess && !$onError) {
            return new static($this->promise->then(function (Response $response) use (&$onSuccess) {
                $onSuccess(jsonDecode($response->getBody()), $response->getStatusCode());
            }));
        }

        if (!$onSuccess && $onError) {
            return new static($this->promise->then(null, function (Response $response) use (&$onError) {
                $onError(jsonDecode($response->getBody()), $response->getStatusCode());
            }));
        }

        return new static($this->promise);
    }

    /**
     * @param bool $silent
     * @return bool|\Eusi\Utils\Json|mixed|null
     */
    public function unwrap($silent = false)
    {
        try {

            $response = $this->promise->wait(!$silent);

            return jsonDecode($response->getBody())['success'];

        } catch (RequestException $e) {
            if ($silent) return false;
            throw new EusiSDKException($e->getResponse());
        } catch (\Exception $e) {
            if ($silent) return false;
            exceptionAsJson($e);
        }
    }
}