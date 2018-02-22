<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/31/18
 * Time: 7:58 PM
 */

namespace Eusi\Delivery;


use Eusi\Utils\Json;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Interface AsyncInterface
 *
 * @package Eusi\Delivery
 */
interface AsyncInterface
{
    /**
     * AsyncInterface constructor.
     *
     * @param PromiseInterface $promise
     */
    public function __construct(PromiseInterface $promise);

    /**
     * @param callable|null $onSuccess
     * @param callable|null $onError
     * @return AsyncInterface
     */
    public function then(callable $onSuccess = null, callable $onError = null);

    /**
     * @param bool $silent
     * @return Json|null
     * @throws \LogicException
     */
    public function unwrap($silent = true);
}