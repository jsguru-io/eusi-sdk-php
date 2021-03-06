<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 1/11/18
 * Time: 8:35 PM
 */

namespace Eusi\Auth;

/**
 * Class MemberToken
 *
 * @package Eusi\Authorize
 */
class MemberToken extends BearerToken
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * MemberToken constructor.
     *
     * @param int $id
     * @param string $value
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(int $id, string $value, string $createdAt, string $updatedAt)
    {
        $this->id = $id;
        $this->value = $value;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}