<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 2/28/18
 * Time: 1:19 PM
 */

namespace Tests\Unit;


use Eusi\Auth\Client as AuthClient;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use Eusi\Eusi;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class BucketTest extends TestCase
{
    protected $mockServer;

    protected $accessTokenValue;

    protected $memberTokenValue;

    protected $bucket;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->accessTokenValue = 'auth_token'.time();

        $this->memberTokenValue = 'member_token'.time();

        $authorizeResponse = json_encode(["type" => "Bearer", "token" => $this->accessTokenValue]);

        $loginResponse = json_encode([
            "id" => "1392",
            "member_id" => "303d0fb1-ed43-49b1-921c-390e5ffc0cc5",
            "token" => $this->memberTokenValue,
            "updated_at" => "2018-03-25T19:32:21.377Z",
            "created_at" => "2018-03-25T19:32:21.377Z",
            "member" => [
                "id" => "303d0fb1-ed43-49b1-921c-390e5ffc0cc5",
                "bucket_id" => "9602b97a-8b38-4f8f-9050-992bcc1dd9d6",
                "first_name" => null,
                "last_name" => null,
                "email" => "test111@mail.com",
                "created_at" => "2018-01-10T20:10:42.000Z",
                "updated_at" => "2018-01-10T20:10:42.000Z",
                "deleted_at" => null,
                "status" => "1"
            ]
        ]);

        $this->mockServer = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $authorizeResponse),
            new Response(200, ['Content-Type' => 'application/json'], $loginResponse)
        ]);
    }

    protected function getConfig()
    {
        $requestHandler = HandlerStack::create($this->mockServer);

        return  [
            'bucket_id' =>  '0000-1111-2222-3333',
            'bucket_secret' => 'jamesbond007',
            'host' => 'https://delivery.test.eusi.cloud',
            'http_client' => new Client(['handler' => $requestHandler]),
            'api_version' => 'v12',
            'debug' => true
        ];
    }

    public function test_client_init()
    {
        $config = $this->getConfig();

        $eusi = new Eusi($config);

        $this->assertEquals($config, $eusi->getConfig());

        $this->assertEquals(
            $eusi->getAuthClient(),
            new AuthClient('0000-1111-2222-3333', 'jamesbond007', 'https://delivery.test.eusi.cloud', 'v12', $config['http_client'])
        );
    }

    public function test_client_authorize()
    {
        $config = $this->getConfig();

        $eusi = new Eusi($config);

        $eusi->authorize();

        $this->assertSame('0000-1111-2222-3333', $eusi->bucket()->getEntity()->getId());

        $this->assertSame('jamesbond007', $eusi->bucket()->getEntity()->getSecret());

        $this->assertSame('Bearer '.$this->accessTokenValue, (string) $eusi->bucket()->getEntity()->getAccessToken());
    }

    public function test_member_login()
    {
        $config = $this->getConfig();

        $eusi = new Eusi($config);

        $eusi->authorize()->bucket()->login('test111@mail.com', '*****');

        $this->assertSame('Bearer '.$this->memberTokenValue, (string) $eusi->bucket()->getEntity()->getMemberToken());
    }
}