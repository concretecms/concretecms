<?php
namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;

class UserSignup implements SubjectInterface
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User"),
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getNotificationDate()
    {
        return $this->user->getUserDateAdded();
    }

}
