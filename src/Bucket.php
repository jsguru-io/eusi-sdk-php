<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/12/18
 * Time: 9:59 AM
 */

namespace Eusi;


use Eusi\Auth\BearerToken;
use Eusi\Auth\MemberToken;
use Eusi\Bucket\Client;
use Eusi\Bucket\Entity;
use Eusi\Delivery\DoQuery;
use Eusi\Delivery\FetchQuery;
use Eusi\Delivery\HttpQueryBuilder;
use Eusi\Delivery\HttpQuery;
use Eusi\Delivery\Models\Field;
use Eusi\Delivery\Models\Form;
use Eusi\Delivery\Models\Member;
use Eusi\Utils\Json;
use GuzzleHttp\ClientInterface as HttpClientInterface;

/**
 * Class Bucket
 *
 * @package Eusi
 */
class Bucket
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Bucket constructor.
     *
     * @param BearerToken $token
     * @param $bucketId
     * @param $bucketSecret
     * @param null $host
     * @param null $apiVersion
     * @param HttpClientInterface|null $httpClient
     * @throws Exceptions\InvalidTokenException
     */
    public function __construct(BearerToken $token, $bucketId, $bucketSecret, $host = null, $apiVersion = null, HttpClientInterface $httpClient = null)
    {
        $this->client = new Client($bucketId, $bucketSecret, $host, $apiVersion, $httpClient);

        $this->entity = new Entity($token, $bucketId, $bucketSecret);
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Login an existing user to Bucket
     *
     * @param $email
     * @param $password
     * @return $this
     * @throws Exceptions\EusiSDKException
     */
    public function login($email, $password)
    {
        $request = $this->client->request(
            "POST",
            __FUNCTION__,
            [
                'Authorization' => $this->entity->getAccessToken()
            ],
            [
                'email' => $email,
                'password' => $password
            ]
        );

        $response = $this->client->sendRequest($request);

        $body = jsonDecode($response->getBody());

        $memberToken = new MemberToken(
            $body['id'], $body['token'], $body['member_id'], $body['created_at'], $body['updated_at']
        );

        $this->entity->setMemberToken($memberToken);

        $this->entity->setMember(new Member($body['member']));

        $this->client->defaultHeaders['Authorization'] = $memberToken;

        return $this;
    }

    /**
     * Register a new user in Bucket
     *
     * @param $email
     * @param $password
     * @return $this
     * @throws Exceptions\EusiSDKException
     */
    public function register($email, $password)
    {
        $request = $this->client->request(
            "POST",
            __FUNCTION__,
            [
                'Authorization' => $this->entity->getAccessToken()
            ],
            [
                'email' => $email,
                'password' => $password
            ]
        );

        $this->client->sendRequest($request);

        return $this;
    }

    /**
     * @return $this
     */
    public function logout()
    {
        $this->entity->unsetMember();
        $this->client->defaultHeaders['Authorization'] = $this->entity->getToken();

        return $this;
    }

    /**
     * @return Member
     */
    public function getUser()
    {
        return $this->entity->getMember();
    }

    /**
     * @param array $where
     * @return Json
     */
    public function fetchItems($where = [])
    {
        return jsonMap($this->fetchRaw($where));
    }

    /**
     * @param array $where
     * @return \Psr\Http\Message\StreamInterface
     */
    public function fetchItemsRaw($where = [])
    {
        $response = $this->client->get(
            $this->client->getItemsEndpoint(http_query($where)),
            ['Authorization' => $this->entity->getToken()]
        );

        return $response->getBody();
    }

    /**
     * @param $identifier
     * @return Json
     */
    public function taxonomy($identifier)
    {
        return jsonDecode($this->taxonomyRaw($identifier));
    }

    /**
     * @param $identifier
     * @return \Psr\Http\Message\StreamInterface
     */
    public function taxonomyRaw($identifier)
    {
        $response = $this->client->get(
            $this->client->getTaxonomyEndpoint($identifier),
            ['Authorization' => $this->entity->getToken()]
        );

        return $response->getBody();
    }

    /**
     * @return HttpQueryBuilder
     */
    public function items()
    {
        return new HttpQueryBuilder(
            $this->client,
            new HttpQuery($this->entity->getToken(), [
                'uri' => $this->client->getItemsEndpoint()
            ])
        );
    }

    /**
     * @param string $identifier
     * @return Form
     */
    public function form($identifier)
    {
        $response = $this->client->get(
            $this->client->getFormsEndpoint($identifier),
            ['Authorization' => $this->entity->getToken()]
        );

        $form = json_decode($response->getBody(), true);

        return new Form($form, $this->client);
    }
}