<?php
namespace Concrete\Core\Calendar\Utility;

use Symfony\Component\HttpFoundation\Session\Session;

class Preferences
{

    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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
}

