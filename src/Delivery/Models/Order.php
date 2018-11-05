<?php
/**
 * Created by PhpStorm.
 * User: sasa.blagojevic@mail.com
 * Date: 10/29/18
 * Time: 10:53 AM
 */

namespace Eusi\Delivery\Models;


use Eusi\Exceptions\EusiSDKException;
use Eusi\Utils\Json;

class Order extends Json
{
    public function __construct(Customer $customer, array $orderDetails = [], array $orderItems = [])
    {
        $orderDetails = array_merge([
            'discount_total' => 0,
            'tax_percentage' => 0,
            'shipping_total' => 0
        ], $orderDetails);

        if (count($orderItems) < 1) {
            throw new EusiSDKException("You need to provide at least one order item");
        }

        foreach ($orderItems as $i => $item) {
            if (!$item instanceof Item) {
                $orderItems[$i] = new Item($item);
            }
        }

        parent::__construct(array_merge($orderDetails, ['customer' => $customer, 'order_items' => $orderItems]));
    }

    public function id()
    {
        return $this['id'];
    }
}