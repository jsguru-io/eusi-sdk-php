<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/31/18
 * Time: 8:14 PM
 */

namespace Eusi\Delivery;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;
use function Eusi\exceptionAsJson;

/**
 * Class AsyncRaw
 *
 * @package Eusi\Delivery
 */
class AsyncRaw implements AsyncInterface
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
                $onSuccess($response->getBody(), $response->getStatusCode());
            }, function (Response $response) use (&$onError) {
                $onError($response->getBody(), $response->getStatusCode());
            }));
        }

        if ($onSuccess && !$onError) {
            return new static($this->promise->then(function (Response $response) use (&$onSuccess) {
                $onSuccess($response->getBody(), $response->getStatusCode());
            }));
        }

        if (!$onSuccess && $onError) {
            return new static($this->promise->then(null, function (Response $response) use (&$onError) {
                $onError($response->getBody(), $response->getStatusCode());
            }));
        }

        return new static($this->promise);
    }

    /**
     * @param bool $silent
     * @return StreamInterface|null|void
     */
    public function unwrap($silent = true)
    {
        try {

            $response = $this->promise->wait();

            return $response->getBody();

        } catch (RequestException $e) {
            if ($silent) return;
            throw new EusiSDKException($e->getResponse());
        } catch (\Exception $e) {
            if ($silent) return;
            exceptionAsJson($e);
        }
    }
}