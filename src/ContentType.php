<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/16/18
 * Time: 11:04 PM
 */

namespace Eusi;

use Psr\Http\Message\ResponseInterface;

trait ContentType
{
    /**
     * @param $headers
     * @return bool
     */
    protected function isJsonRequest(array $headers)
    {
        return in_array('application/json', array_values($headers));
    }

    /**
     * @param $headers
     * @return bool
     */
    protected function isUrlEncodedRequest(array $headers)
    {
        return in_array('application/x-www-form-urlencoded', array_values($headers));
    }

    /**
     * @param $headers
     * @return bool
     */
    protected function isFormDataRequest(array $headers)
    {
        return in_array('multipart/form-data', array_values($headers));
    }

    /**
     * Check if it is a JSON response
     *
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isJsonResponse(ResponseInterface $response)
    {
        $headers = array_values(array_values($response->getHeaders()));
        foreach ($headers as $header) {
            foreach ($header as $k => $v) {
                if (str_has($v, 'application/json')) {
                    return true;
                }
            }
        }
        return false;
    }
}