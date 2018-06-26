<?php
/**
 * Created by PhpStorm.
 * User: sasa.blagojevic@mail.com
 * Date: 6/26/18
 * Time: 1:56 PM
 */

namespace Eusi\Delivery;


abstract class AbstractAsync
{
    /**
     * @return PromiseInterface
     */
    public function promise()
    {
        return $this->promise;
    }
}