<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Express\Entry\Notifier\Notification\EntrySubject;
use Concrete\Core\Notification\View\NewFormSubmissionListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="FormSubmissionNotifications"
 * )
 */
class NewFormSubmissionNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID", onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Express\Entry
     */
    protected $entry;

    public function __construct(EntrySubject $subject)
    {
        $this->entry = $subject->getEntry();
        parent::__construct($subject);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new NewFormSubmissionListView($this);
    }

    /**
     * @return \Concrete\Core\Entity\Express\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }
}
