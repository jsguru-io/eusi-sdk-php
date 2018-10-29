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

class Customer extends Json
{
    public function __construct(array $content = [])
    {
        $required = ['email', 'address', 'city', 'zip', 'country', 'first_name', 'last_name'];

        foreach ($required as $key) {
            if (!isset($content[$key])) {
                throw new EusiSDKException("Following keys are required: " . implode(', ', $required));
            }
        }

        parent::__construct($content);
    }
}