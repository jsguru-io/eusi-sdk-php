<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/14/18
 * Time: 4:32 PM
 */

namespace Eusi\Utils;

/**
 * Class Json
 * @package Eusi\Utils
 */
class Json implements \ArrayAccess, \Serializable, Arrayable, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $content;

    /**
     * Json constructor.
     *
     * @param array $content
     */
    public function __construct(array $content = [])
    {
        $this->content = $content;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->content[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->content[$offset]) ? $this->content[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->content[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->content[$offset]);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->content[$name]) ? $this->content[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->content[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->content[$name]);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return \GuzzleHttp\json_encode($this->toArray());
    }

    /**
     * @param string $serialized
     * @return Json|void
     */
    public function unserialize($serialized)
    {
        return new Json(\GuzzleHttp\json_decode($serialized, true));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return \Eusi\jsonUnMap($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * @return \RecursiveArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator((object) $this->content);
    }
}