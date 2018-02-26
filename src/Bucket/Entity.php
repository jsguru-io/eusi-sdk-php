<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/12/18
 * Time: 9:20 AM
 */

namespace Eusi\Bucket;


use Eusi\Auth\AccessToken;
use Eusi\Auth\BearerToken;
use Eusi\Auth\MemberToken;
use Eusi\Delivery\Models\Member;
use Eusi\Exceptions\InvalidTokenException;

class Entity
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $secret;

    /**
     * Public access token
     *
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * Public + Private access token
     *
     * @var MemberToken
     */
    protected $memberToken;

    /**
     * Logged in user
     *
     * @var Member
     */
    protected $member;

    /**
     * Bucket entity constructor.
     *
     * @param BearerToken $token
     * @param string $id
     * @param string $secret
     * @throws InvalidTokenException
     */
    public function __construct(BearerToken $token, string $id, string $secret)
    {
        $this->id = $id;

        $this->secret = $secret;

        if ($token instanceof MemberToken) {
            $this->setMemberToken($token);
        } else if ($token instanceof AccessToken) {
            $this->setAcceToken($token);
        } else {
            throw new InvalidTokenException();
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return BearerToken
     */
    public function getToken()
    {
        if (isset($this->memberToken)) {
            return $this->memberToken;
        }
        return $this->accessToken;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return MemberToken
     */
    public function getMemberToken()
    {
        return $this->memberToken;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param AccessToken $token
     */
    public function setAcceToken(AccessToken $token)
    {
        $this->accessToken = $token;
    }

    /**
     * @param MemberToken $memberToken
     */
    public function setMemberToken(MemberToken $memberToken)
    {
        $this->memberToken = $memberToken;
    }

    /**
     * @param Member $member
     */
    public function setMember(Member $member)
    {
        $this->member = $member;
    }

    public function unsetMember()
    {
        unset($this->memberToken, $this->member);
    }
}