<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/10/18
 * Time: 8:03 PM
 */

namespace Eusi;

use Eusi\Utils\Json;

/**
 * Check if a string contains a string
 *
 * @param $haystack
 * @param $needle
 * @param int $offset
 * @return bool
 */
function str_has($haystack, $needle, $offset = 0)
{
    return strpos($haystack, $needle, $offset) !== false;
}

/**
 * Get environment variable by key
 *
 * @param $key
 * @param null $default
 * @return mixed
 */
function get_env($key, $default = null)
{
    $var = getenv($key);

    if ($var === false) {
        return is_callable($default) ? $default() : $default;
    }

    $var = trim($var, '"');

    switch (strtolower($var)) {
        case 'true':
        case 'false':
            return (bool) $var;
        case '':
        case 'null':
            return;
    }

    return $var;
}

/**
 * @param array $params
 * @return string
 */
function http_query($params = [])
{
    return $params ? '?'.http_build_query($params) : '';
}

/**
 * Decode to Json helper class
 *
 * @param $json
 * @return \Eusi\Utils\Json
 */
function jsonDecode($json)
{
    return new Json(\GuzzleHttp\json_decode($json, true));
}

/**
 * @param string $json
 * @return \Eusi\Utils\Json
 */
function jsonMap(string $json)
{
    $json = \GuzzleHttp\json_decode($json, true);

    $arrayIterator = new \RecursiveArrayIterator($json);

    $iterator = new \RecursiveIteratorIterator(
        $arrayIterator, \RecursiveIteratorIterator::CHILD_FIRST
    );

    $mapKeys = ['content', 'linked_content', 'data', 'media', 'order_items'];

    foreach ($iterator as $key => $value) {

        if (!is_numeric($key) && in_array($key, $mapKeys)) {

            if (is_array($value) && !empty($value)) {

                switch ($key) {
                    case "linked_content":
                        $class = "Eusi\\Delivery\\Models\\Content";
                        break;
                    case "data":
                        $class = "Eusi\\Delivery\\Models\\Item";
                        break;
                    default:
                        $class = "Eusi\\Delivery\\Models\\".ucfirst($key);
                        break;
                }

                $newValue = [];
                array_walk($value, function ($v, $i) use (&$newValue, $class) {
                    $newValue[$i] = new $class($v);
                });

                // Get the current depth and traverse back up the tree, saving the modifications
                $currentDepth = $iterator->getDepth();
                for ($subDepth = $currentDepth; $subDepth >= 0; $subDepth--) {
                    // Get the current level iterator
                    $subIterator = $iterator->getSubIterator($subDepth);
                    // If we are on the level we want to change, use $newValue otherwise set the key to the parent iterators value
                    if ($currentDepth === $subDepth) {
                        $subIterator->offsetSet($subIterator->key(), $newValue);
                    } else {
                        $subIterator->offsetSet($subIterator->key(), $iterator->getSubIterator($subDepth + 1)->getArrayCopy());
                    }
                }
            }
        }
    }

    return new Json([
        'data' => $iterator->getArrayCopy()['data'],
        'pagination' => new Json($json['pagination'])
    ]);
}

/**
 * @param Json $json
 * @return array
 */
function jsonUnMap(Json $json): array
{
    $arrayIterator = $json->getIterator();

    $iterator = new \RecursiveIteratorIterator(
        $arrayIterator, \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $key => $value) {

        if (is_object($value) && method_exists($value, 'toArray')) {

            $newValue = $value->toArray();

            // Get the current depth and traverse back up the tree, saving the modifications
            $currentDepth = $iterator->getDepth();
            for ($subDepth = $currentDepth; $subDepth >= 0; $subDepth--) {
                // Get the current level iterator
                $subIterator = $iterator->getSubIterator($subDepth);
                // If we are on the level we want to change, use $newValue otherwise set the key to the parent iterators value
                if ($currentDepth === $subDepth) {
                    $subIterator->offsetSet($subIterator->key(), $newValue);
                } else {
                    $subIterator->offsetSet($subIterator->key(), $iterator->getSubIterator($subDepth + 1)->getArrayCopy());
                }
            }
        }
    }

    return $iterator->getArrayCopy();
}

/**
 * @param array $order
 * @return Delivery\Models\Order
 * @throws Exceptions\EusiSDKException
 */
function mapOrdersResponse(array $order)
{
    $orderDetails = array_filter($order, function ($v, $k) {
        return !in_array($k, ['customer', 'order_items']);
    }, ARRAY_FILTER_USE_BOTH);

    $customer = new \Eusi\Delivery\Models\Customer($order['customer']);

    return new \Eusi\Delivery\Models\Order($customer, $orderDetails, $order['order_items']);
}

/**
 * Encode json
 *
 * @param $json
 * @param int $options
 * @param int $depth
 * @return string
 */
function jsonEncode($json, $options = 0, $depth = 512)
{
    return \GuzzleHttp\json_encode($json, $options, $depth);
}

/**
 * @param \Exception $exception
 */
function exceptionAsJson(\Exception $exception)
{
    header('Content-type: application/json; charset=utf-8', true);

    if (in_array(\Eusi\Utils\Arrayable::class, class_implements($exception))) {
        $exception = $exception->toArray();
    } else {
        $exception = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stack' => $exception->getTrace()
        ];
    }

    print jsonEncode($exception);

    die;
}

/**
 * @param $key
 * @param null $value
 * @return mixed
 */
function config($key, $value = null)
{
    if ($value) {
        \Eusi\Utils\Config::set($key, $value);
    }
    return \Eusi\Utils\Config::get($key);
}

/**
 * @param $var
 */
function dd($var)
{
    dump($var);
    die;
}

/**
 * @param $var
 */
function d($var)
{
    dump($var);
}
