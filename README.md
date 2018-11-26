## eusi-sdk-php
Official PHP SDK for EUSI an API-first CMS

##### Table of contents
1. [Getting started](#getting-started)
2. [Authorization](#authorization)
    * Access Token
    * Login/Register user
3. [Fetching taxonomy](#fetching-taxonomy)
4. [Fetching content](#fetching-content)
    * Synchronous
    * Asynchronous
    * Filtering
5. [Forms](#forms)
6. [Extra](#extra) 

#### Getting Started

First install our package with composer by calling 
``
    composer require jsguru-io/eusi-php-sdk;
``

If you are making a custom script don't forget to require composer `autoload.php`, othwerwise if you're using some framework this is probably already done for you.
```php

    require 'vendor/autoload.php';
    
    
    $eusi = new Eusi\Eusi([
        'bucket_id' => 'xxx-xxx-xxx-xxx',
        'bucket_secret' => 'agent-007'
    ]);
```

You can put yout Bucket ID and Bucket SECRET in an `.env` file as well. Then you can initialize Eusi without providing them through constructor.

```dotenv
EUSI_BUCKET_ID=xxx-xxx-xxx-xxx
EUSI_BUCKET_SECRET=agent-007
```

```php
$eusi = new Eusi\Eusi();
``` 
#### Authorization
To get an access token call authorize. If you have previously stored the token somewhere you can provide it as an argument as well. 
```php  
$bucket = $eusi->authorize()->bucket();

// or

$bucker = $eusi->authorize(new AccessToken('xxx-xxx-xxx'))->bucket();
```


To get a higher level access token login a user.

```php  
$bucket = $eusi->authorize()->bucket();
    
$bucket->login('john.doe@mail.com', '******');
    
$bucket->register('sam.smith@mail.com', '******');
```

#### Fetching taxonomy

```php

$bucket->taxonomy('taxonomy-id-or-key');
    
$bucket->taxonomyRaw('taxonomy-id-or-key');

```

#### Fetching content

##### Fetch one item

```php
$item = $bucket->fetchItem($keyOrId);
```

##### Synchronous
```php
$items = $bucket->items()->fetch(10);
```
##### Asynchronous
```php
$items = $bucket->items()->async()->fetch(10)->unwrap();
       
//or
       
$items = $bucket->items()->async()->fetch(10)->then(function ($response) {
    // Do something else
})->then(function ($response) {
    // Do something else again
});
    
// Fails silently
$items = $items->unwrap();
    
// Throws exception
$items = $items->unwrap(false);
```

##### Filtering content
For your convenience we made a query builder, it might be familiar to you from Laravel. All of the filtering methods are chainable, but some of them might not work together. All of the methods accept either multiple `$value` arguments or an array of values.

```php
    $items = $bucket->items()
        ->where('sys.name', 'like', '%rasmus%')
        ->where('elem.elem_name', 'in', 'foo', 'bar')
        ->where('elem.elem_date', 'btw', ['2017-01-01', '2018-01-01'])
        ->fetch();
```

Here is a full list of helper methods for reference. 
```php

$items = $bucket->items()
    
    ->whereTitleIn(...$values)
    ->whereTitleLike(...$values)
    ->whereTitleNotIn(...$values)
    ->whereTitleBetween(... $values)
     
    ->whereTaxLike(...$values)
    ->whereTaxIn(...$values)
    ->whereTaxNotIn(...$values)
    ->whereTaxBetween(... $values)
     
    ->whereModelLike(...$values)
    ->whereModelIn(...$values)
    ->whereModelNotIn(...$values)
    ->whereModelBetween(... $values)
          
    ->whereTaxPathLike(...$values)
    ->whereTaxPathIn(...$values)
    ->whereTaxPathNotIn(...$values)
    ->whereTaxPathBetween($key,... $values)
      
    ->whereElementIn($key,...$values)
    ->whereElementNotIn($key,... $values)
    ->whereElementLike($key,... $values)
    ->whereElementBetween($key,... $values)
      
    ->fetch();
```

Or you can filter with the "low-level" fetch method building the query string manually

```php
    $bucket->fetchItems([
        'sys.name[$like]' => '%foo'
    ]);
```

List of supported operators and their user friendly aliases used in `->where()`:
```php
 'like' => '[$like]',
 '~' => '[$like]',
 '>' => '[$gt]',
 '>=' => '[$gte]',
 '<' => '[$lt]',
 '<=' => '[$lte]',
 '!=' => '[$ne]',
 'in' => '[$in]',
 'between' => '[$between]',
 'btw' => '[$between]'
```
#### Forms
```php
    $form = $bucket->form('id-or-key');
    
    $form->submit([
        'field_name_1' => 'foo',
        'field_name_2' => 'bar'
    ]);
    
    $form->submitAsync([
        'field_name_1' => 'foo',
        'field_name_2' => 'bar'
    ]);
```
#### Extra

Any method that has a suffix 'Raw' returns a PSR-7 compliant Response object,
otherwise your Response will be a `Eusi\Utils\Json` object. 

`Eusi\Utils\Json` is a convenience class that implements `ArrayAccess` interface, so you can access the server response either as object or array.
```php
    $response = $bucket->items()->whereTitle('Rasmus')->fetch(1);
    
    $rasmus = $response['data'][0];
    
    // or
    
    $rasmus = $response->data[0];
```

All async methods return an object implementing `Eusi\Delivery\AsyncInterface`. 

That is a thin wrapper around Guzzle PSR-7 compliant Promises which allow us to manipulate the response and maintain a consistent API for both synchronous and asynchronous requests. 
 
All SDK exceptions are thrown as application/json response.
