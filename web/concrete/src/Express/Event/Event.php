<?php
namespace Concrete\Core\Express\Event;
use Concrete\Core\Entity\Express\Entry;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class Event extends AbstractEvent
{

    /**
     * @var Entry
     */
    protected $entry;

    protected $entityManager;

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Event constructor.
     * @param $entry
     */
    public function __construct($entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }


}