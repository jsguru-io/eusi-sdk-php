<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/10/18
 * Time: 11:23 PM
 */

namespace Eusi;

use Eusi\Auth\Client as AuthClient;
use Eusi\Exceptions\EusiSDKException;

/**
 * Class Eusi
 *
 * @package Eusi
 */
class Eusi
{
    /**
     * Eusi SDK Version
     */
    const VERSION = '1.0.1';

    /**
     * Env ID var name
     */
    const ENV_ID_NAME = 'EUSI_BUCKET_ID';

    /**
     * Env SECRET var name
     */
    const ENV_SECRET_NAME = 'EUSI_BUCKET_SECRET';

    /**
     * Env DEBUG var name
     */
    const ENV_DEBUG_NAME = 'EUSI_DEBUG';

    /**
     * SDK exception messages level
     *
     * @var bool
     */
    static $debug = false;

    /**
     * Authorization client
     *
     * @var \Eusi\Auth\Client
     */
    private $authClient;

    /**
     * Bucket service
     *
     * @var Bucket
     */
    private $bucket;

    /**
     * @var array
     */
    private $config;

    /**
     * Eusi constructor.
     *
     * @param array $options
     * @throws EusiSDKException
     */
    public function __construct(array $options = [])
    {
        $config = array_merge([
            'bucket_id' => get_env(static::ENV_ID_NAME),
            'bucket_secret' => get_env(static::ENV_SECRET_NAME),
            'http_client' => null,
            'host' => null,
            'api_version' => null,
            'debug' => get_env(static::ENV_DEBUG_NAME)
        ], $options);

        static::$debug = $config['debug'];

        if (!isset($config['bucket_id'])) {
            throw new EusiSDKException('Bucket id not provided and it could not be found in fallback environment variable ' . static::ENV_ID_NAME . '.');
        }

        if (!isset($config['bucket_secret'])) {
            throw new EusiSDKException('Bucket secret not provided and it could not be found in fallback environment variable ' . static::ENV_SECRET_NAME . '.');
        }

        $this->authClient = new AuthClient(
            $config['bucket_id'],
            $config['bucket_secret'],
            $config['host'],
            $config['api_version'],
            $config['http_client']
        );

        $this->config = $config;
    }

    /**
     * Get bucket id
     *
     * @return string
     */
    public function getBucketId()
    {
        return $this->authClient->getBucketId();
    }

    /**
     * Get bucket secret
     *
     * @return string
     */
    public function getBucketSecret()
    {
        return $this->authClient->getBucketSecret();
    }

    /**
     * Get access token and authorize bucket
     *
     * @return $this
     * @throws EusiSDKException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authorize()
    {
        if (!$this->bucket) {
            $accessToken = $this->authClient->getAccessToken();

            $this->setBucket(new Bucket(
                $accessToken,
                $this->authClient->getBucketId(),
                $this->authClient->getBucketSecret(),
                $this->config['host'],
                $this->config['api_version'],
                $this->config['http_client']
            ));
        }

        return $this;
    }

    /**
     * Set authorized Bucket
     *
     * @param Bucket $bucket
     */
    public function setBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * Get authorized Bucket
     *
     * @return Bucket
     */
    public function bucket()
    {
        return $this->bucket;
    }

    /**
     * Get Authorization client
     *
     * @return AuthClient
     */
    public function getAuthClient()
    {
        return $this->authClient;
    }

    /**
     * Get SDK version
     *
     * @return string
     */
    public static function getSDKversion()
    {
        return static::VERSION;
    }

    /**
     * Get SDK configurable options
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}