<?php
namespace Concrete\Core\Calendar\Utility;

use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Config\Repository\Repository;
use Symfony\Component\HttpFoundation\Session\Session;

class Preferences
{

    protected $session;
    protected $config;
    protected $eventCategory;

    public function __construct(Session $session, Repository $config, EventCategory $eventCategory)
    {
        $this->session = $session;
        $this->eventCategory = $eventCategory;
        $this->config = $config;
    }

    public function setPreferredViewToGrid()
    {
        $this->session->set('calendar.view', 'grid');
    }

    public function setPreferredViewToList()
    {
        $this->session->set('calendar.view', 'list');
    }

    public function getPreferredViewPath()
    {
        switch($this->session->get('calendar.view')) {
            case 'list':
                return '/dashboard/calendar/event_list';
            default:
                return '/dashboard/calendar/events';
        }
    }

    public function getCalendarTopicsAttributeKey()
    {
        $handle = $this->config->get('concrete.calendar.topic_attribute');
        if ($handle) {
            return $this->eventCategory->getByHandle($handle);
        }
    }
}

