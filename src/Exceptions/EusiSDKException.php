<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/10/18
 * Time: 10:02 PM
 */

namespace Eusi\Exceptions;

use Eusi\ContentType;
use Eusi\Eusi;
use Eusi\Utils\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function Eusi\jsonDecode;
use function Eusi\jsonEncode;

/**
 * Class EusiSDKException
 *
 * @package Eusi\Exceptions
 */
class EusiSDKException extends \Exception implements Arrayable
{
    use ContentType;

    /**
     * @var array|null
     */
    protected $response;

    /**
     * EusiSDKException constructor.
     *
     * @param ResponseInterface|string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message, int $code = 0, Throwable $previous = null)
    {

        if ($this->isHttpResponse($message)) {
            if ($this->isJsonResponse($message)) {
                $this->response = $this->parseJsonResponse($message);
            }
            $code = $message->getStatusCode();
            $message = $message->getReasonPhrase() . " : " . $message->getBody()->getContents();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Check if it is a Psr7 Http Response
     *
     * @param $message
     * @return bool
     */
    protected function isHttpResponse($message)
    {
        return is_object($message) && in_array(ResponseInterface::class, class_implements($message));
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function parseJsonResponse(ResponseInterface $response)
    {
        $body = jsonDecode($response->getBody()->getContents());

        if (isset($body['url'])) {
            $body['url'] = urldecode($body['url']);
        }

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        unset($body['stack']);
        unset($body['status']);

        return array_merge([
            'reason' => $reason,
            'code' => $code,
        ], (array) $body->toArray());
    }

    public function toArray()
    {
        $exception = [
            'type' => get_class($this),
            'code' => $this->getCode(),
            'message' => $this->getMessage()
        ];

        if (isset($this->response)) {
            $exception = array_merge($exception, $this->response);
        }

        $exception['file'] = $this->getFile();
        $exception['line'] = $this->getLine();

        if (Eusi::$debug) {
            $exception['stack'] = $this->getTrace();
        }

        return $exception;
    }

    public function __toString()
    {
        header('Content-type: application/json; charset=utf-8', true);

        print jsonEncode($this->toArray());

        die;
    }
}