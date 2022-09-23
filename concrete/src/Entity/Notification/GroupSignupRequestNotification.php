<?php

namespace Concrete\Core\Entity\Notification;

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
     *
     * @var \Concrete\Core\Entity\User\GroupSignupRequest
     */
    protected $signupRequest;

    /**
     * @param \Concrete\Core\Entity\User\GroupSignupRequest $signupRequest
     */
    public function __construct(SubjectInterface $signupRequest)
    {
        $this->signupRequest = $signupRequest;
        parent::__construct($signupRequest);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupSignupRequest
     */
    public function getSignupRequest(): SubjectInterface
    {
        return $this->signupRequest;
    }

    /**
     * @param \Concrete\Core\Entity\User\GroupSignupRequest $signupRequest
     *
     * @return $this
     */
    public function setSignupRequest(SubjectInterface $signupRequest): self
    {
        $this->signupRequest = $signupRequest;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new GroupSignupRequestListView($this);
    }
}
