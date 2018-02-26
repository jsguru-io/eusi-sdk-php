<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 2/15/18
 * Time: 10:59 AM
 */

namespace Eusi\Delivery\Models;

use Eusi\Bucket\Client;
use Eusi\Delivery\AsyncForm;
use Eusi\Delivery\AsyncRaw;
use function Eusi\jsonDecode;
use Eusi\Utils\Json;

/**
 * Class Form
 *
 * @package Eusi\Delivery\Models
 */
class Form extends Json
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $enctype;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Form constructor.
     *
     * @param array $form
     * @param string $method
     * @param string $enctype
     * @param Client $client
     */
    public function __construct(array $form, Client $client, $method = 'POST', $enctype = "application/x-www-form-urlencoded")
    {
        $this->content = $form;

        foreach ($this->content['fields'] as $i => $field) {
            $this->content['fields'][$i] = new Field($field);
        }

        $this->client = $client;

        $this->method = $method;

        $this->enctype = $enctype;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws \Eusi\Exceptions\EusiSDKException
     */
    public function submit(array $payload)
    {
        $request = $this->client->request(
            strtoupper($this->method),
            $this->client->getFormsEndpoint($this['id']),
            array_merge($this->client->defaultHeaders, ['Content-Type' => $this->enctype]),
            http_build_query($payload)
        );

        return jsonDecode($this->client->sendRequest($request)->getBody())["success"];
    }

    /**
     * @param array $payload
     * @return AsyncForm
     * @throws \Eusi\Exceptions\EusiSDKException
     */
    public function submitAsync(array $payload)
    {
        $request = $this->client->request(
            strtoupper($this->method),
            $this->client->getFormsEndpoint($this['id']),
            array_merge($this->client->defaultHeaders, ['Content-Type' => $this->enctype]),
            http_build_query($payload)
        );

        return new AsyncForm($this->client->sendAsyncRequest($request));
    }
}