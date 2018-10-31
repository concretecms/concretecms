<?php

namespace Concrete\Core\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Concrete\Core\Entity\User\LoginAttemptRepository")
 * @ORM\Table(
 *     name="UserLoginAttempts"
 * )
 */
class LoginAttempt
{

    /**
     * The attempt ID, this will be a unique identifier
     *
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * The user ID that a login was attempted for
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $userId;

    /**
     * The datetime of the time the login happened. This date must be in UTC
     *
     * @ORM\Column(type="datetime")
     */
    protected $utcDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return LoginAttempt
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     *
     * @return LoginAttempt
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUtcDate()
    {
        return $this->utcDate;
    }

    /**
     * @param mixed $utcDate
     *
     * @return LoginAttempt
     */
    public function setUtcDate($utcDate)
    {
        $this->utcDate = $utcDate;
        return $this;
    }

}
