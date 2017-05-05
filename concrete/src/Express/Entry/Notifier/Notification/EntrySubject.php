<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Notification\Subject\SubjectInterface;

class EntrySubject implements SubjectInterface
{

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @var Entry
     */
    protected $entry;

    public function getNotificationDate()
    {
        return $this->entry->getDateCreated();
    }

    public function getUsersToExcludeFromNotification()
    {
        return array();
    }

    /**
     * @return Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }



}