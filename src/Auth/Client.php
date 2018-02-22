<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/12/18
 * Time: 3:00 AM
 */

namespace Eusi\Auth;

use Eusi\Client as BaseClient;
use Eusi\Exceptions\EusiSDKException;
use function Eusi\jsonDecode;

class Client extends BaseClient
{
    const AUTH_PATH = '/authorize';

    /**
     * @return AccessToken
     * @throws EusiSDKException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessToken()
    {
        $request = $this->request(
            'POST',
            static::AUTH_PATH,
            $this->defaultHeaders,
            ['secret' => $this->getBucketSecret()]
        );

        $response = $this->sendRequest($request);

        $body = jsonDecode($response->getBody());

        return new AccessToken($body['token']);
    }
}