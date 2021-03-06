<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/25/18
 * Time: 11:57 AM
 */

namespace Eusi\Delivery;


use Eusi\Bucket\Client;
use Eusi\Exceptions\EusiSDKException;
use function Eusi\exceptionAsJson;
use function Eusi\jsonMap;

/**
 * Class HttpQueryBuilder
 *
 * @method $this whereTitle($value)
 * @method $this whereKey($value)
 * @method $this whereModel($value)
 * @method $this whereTax($value)
 * @method $this whereTaxPath($value)
 * @method $this whereElement($key, $value)
 *
 * @method $this whereTitleLike(...$values)
 * @method $this whereKeyLike(...$values)
 * @method $this whereModelLike(...$values)
 * @method $this whereTaxLike(...$values)
 * @method $this whereTaxPathLike(...$values)
 * @method $this whereElementLike($key,... $values)
 *
 * @method $this whereTitleIn(...$values)
 * @method $this whereKeyIn(...$values)
 * @method $this whereModelIn(...$values)
 * @method $this whereTaxIn(...$values)
 * @method $this whereTaxPathIn(...$values)
 * @method $this whereElementIn($key,...$values)
 *
 * @method $this whereTitleBetween(...$values)
 * @method $this whereKeyBetween(...$values)
 * @method $this whereModelBetween(...$values)
 * @method $this whereTaxBetween(...$values)
 * @method $this whereTaxPathBetween(...$values)
 * @method $this whereElementBetween($key,...$values)
 *
 * @method $this whereTitleNotIn(...$values)
 * @method $this whereKeyNotIn(...$values)
 * @method $this whereModelNotIn(...$values)
 * @method $this whereTaxNotIn(...$values)
 * @method $this whereTaxPathNotIn(...$values)
 * @method $this whereElementNotIn($key,...$values)
 *
 * @package Eusi\Delivery
 */
class HttpQueryBuilder
{
    /**
     * @var array
     */
    private $operators = [
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
    ];

    /**
     * @var array
     */
    private $queryKeys = [
        'model' => 'sys.model',
        'title' => 'sys.title',
        'key' => 'sys.key',
        'tax' => 'sys.taxonomy',
        'taxonomy' => 'sys.taxonomy',
        'taxonomy.path' => 'sys.taxonomy.path',
        'tax.path' => 'sys.taxonomy.path',
        'element' => 'elem',
        // Deprecated keys
        'type' => 'sys.model',
        'name' => 'sys.title',
        'sys.name' => 'sys.title',
        'sys.type' => 'sys.model'
    ];

    /**
     * @var HttpQueryInterface
     */
    protected $httpQuery;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $async = false;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * HttpQueryBuilder constructor.
     *
     * @param Client $client
     * @param HttpQueryInterface $httpQuery
     */
    public function __construct(Client $client, HttpQueryInterface $httpQuery)
    {
        $this->client = $client;
        $this->httpQuery = $httpQuery;
    }

    /**
     * @param $method
     * @param $args
     * @return $this
     * @throws EusiSDKException
     */
    public function __call($method, $args)
    {
        $params = preg_split('/(?=[A-Z])/', $method);

        $paramsCount = count($params);

        if (!$params || $paramsCount < 2) {
            exceptionAsJson(new \BadMethodCallException("Undefined method [$method]"));
        }

        $key = $this->getQueryKeyFromMethod($params, $paramsCount);

        if ($key === 'element') {
            $keyValue = $args[0];
            unset($args[0]);
            $args = array_values($args);
            $key = 'elem.'.$keyValue;
        }

        $operator = $this->getOperatorFromMethod($params, $paramsCount);

        if (count($args) === 1 && is_array($args[0])) {
            $args = $args[0];
        }

        $this->where($key, $operator, $args);

        return $this;
    }

    /**
     * @param array $params
     * @param $count
     * @return string
     */
    protected function getQueryKeyFromMethod(array $params, $count)
    {
        return strtolower(
            $count >= 3 && in_array('Path', $params) ? $params[1] . '.' . $params[2] :  $params[1]
        );
    }

    /**
     * @param array $params
     * @param $count
     * @return mixed|string
     */
    protected function getOperatorFromMethod(array $params, $count)
    {
        if (in_array('Not', $params)) {
            return '!=';
        }
        return $count < 3 || ($count === 3 && in_array('Path', $params)) ? '=' : $params[$count - 1];
    }

    /**
     * @param $key
     * @return mixed|string
     * @throws EusiSDKException
     */
    protected function mapQueryKey($key)
    {
        $key = strtolower($key);

        if (strpos($key, 'elem.') === 0) {
            return $key;
        }

        if (isset($this->queryKeys[$key])) {
            return $this->queryKeys[$key];
        }

        if (array_search($key, $this->queryKeys)) {
            return $key;
        }

        throw new EusiSDKException("Invalid query key [$key]");
    }

    /**
     * @param $key
     * @return mixed|string
     * @throws EusiSDKException
     */
    protected function mapQueryOperator($key)
    {
        if ($key === "=") {
            return "";
        }

        $key = strtolower($key);

        if (isset($this->operators[$key])) {
            return $this->operators[$key];
        }

        if (array_search($key, $this->operators)) {
            return $key;
        }

        throw new EusiSDKException("Invalid query operator [$key]");
    }


    /**
     * @param string $key
     * @param string|null $operator
     * @param array|string $value
     * @return $this
     * @throws EusiSDKException
     */
    public function where($key, $operator = null, $value = null)
    {
        $args = func_get_args();

        $argsCount = count($args);

        if ($argsCount < 2) {
            exceptionAsJson(new \InvalidArgumentException("Missing argument 2 [\$value]"));
        }

        $key = isset($args[2]) ?
            $this->mapQueryKey($args[0]).$this->mapQueryOperator($args[1]) : $this->mapQueryKey($args[0]);

        $value = $argsCount > 2 ? $args[2] : $args[1];

        if (strtolower($operator) === 'between' || $operator === 'btw') {
            $value = implode(',', $value);
        }

        if (is_array($value)) {
            foreach ($value as $i => $v) {
                $this->filters[] = [$key => $v];
            }
        } else {
            $this->filters[] = [$key => $value];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryString() : string
    {
        return urldecode($this->httpQuery->setQueryParams($this->filters)->getQueryParams());
    }

    /**
     * @return $this
     */
    public function async()
    {
        $this->async = true;
        return $this;
    }

    /**
     * @param null $take
     * @return Async|\Eusi\Utils\Json
     * @throws EusiSDKException
     */
    public function fetch($take = null)
    {
        $this->httpQuery->setQueryParams($this->filters);

        if ($take) {
            $this->httpQuery->appendQueryParams(['number' => $take ? $take : 20]);
        }

        $this->httpQuery->setMethod('GET');

        if ($this->async) {
            return new Async($this->client->sendAsyncRequest(
                $this->httpQuery->request()
            ));
        }

        return jsonMap($this->client->sendRequest(
            $this->httpQuery->request()
        )->getBody());
    }

    /**
     * @param null $take
     * @return AsyncRaw|\Psr\Http\Message\StreamInterface
     * @throws EusiSDKException
     */
    public function fetchRaw($take = null)
    {
        $this->httpQuery->setQueryParams($this->filters);

        if ($take) {
            $this->httpQuery->appendQueryParams(['number' => $take ? $take : 20]);
        }

        $this->httpQuery->setMethod('GET');
        
        if ($this->async) {
            return new AsyncRaw($this->client->sendAsyncRequest(
                $this->httpQuery->request()
            ));
        }

        return $this->client->sendRequest(
            $this->httpQuery->request()
        )->getBody();
    }
}