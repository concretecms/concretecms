<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Support\Facade\Facade;
use Core;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventService;

class ViewVersion extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/view_version';

    /**
     * @var EventService
     */
    protected $eventService;

    public function __construct()
    {
        parent::__construct();
        $app = Facade::getFacadeApplication();
        $this->eventService = $app->make(EventService::class);
        $this->dateFormatter = $app->make(CalendarServiceProvider::class)->getDateFormatter();
    }

    public function view()
    {
        if ($this->canAccess()) {
            $version = $this->eventService->getVersionByID($_REQUEST['eventVersionID']);
            if (!$version) {
                throw new \Exception(t('Invalid version ID.'));
            }
            $this->set('version', $version);
            $this->set('dateFormatter', $this->dateFormatter);
        } else {
            die('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $version = $this->eventService->getVersionByID($_REQUEST['eventVersionID']);
        if (is_object($version)) {
            $calendar = $version->getEvent()->getCalendar();
            if (is_object($calendar)) {
                $cp = new \Permissions($calendar);
                return $cp->canEditCalendarEvents();
            }
        }

        return false;
    }




}
