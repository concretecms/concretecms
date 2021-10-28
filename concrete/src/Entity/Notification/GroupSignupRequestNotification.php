<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\GroupSignupRequestListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequestNotifications"
 * )
 */
class GroupSignupRequestNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupSignupRequest", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $signupRequest;

    /**
     * GroupSignupRequestNotification constructor.
     * @param GroupSignupRequest $group
     */
    public function __construct(SubjectInterface $signupRequest)
    {
        $this->signupRequest = $signupRequest;
        parent::__construct($signupRequest);
    }

    /**
     * @return GroupSignupRequest
     */
    public function getSignupRequest(): SubjectInterface
    {
        return $this->signupRequest;
    }

    /**
     * @param SubjectInterface $signupRequest
     * @return GroupSignupRequestNotification
     */
    public function setSignupRequest(SubjectInterface $signupRequest): GroupSignupRequestNotification
    {
        $this->signupRequest = $signupRequest;
        return $this;
    }

    public function getListView()
    {
        return new GroupSignupRequestListView($this);
    }

}
