<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/11/18
 * Time: 8:35 PM
 */

namespace Eusi\Auth;

/**
 * Class BearerToken
 *
 * @package Eusi\Authorize
 */
abstract class BearerToken
{
    /**
     * @var string
     */
    protected $type = 'Bearer';

    /**
     * @var string
     */
    protected $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * BearerToken constructor.
     *
     * @param $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->type} {$this->value}";
    }
}