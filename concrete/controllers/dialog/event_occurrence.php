<?php
namespace Concrete\Controller\Dialog;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Calendar\Event\EventOccurrence as EventOccurrenceObject;
use Concrete\Core\Calendar\CalendarServiceProvider;

class EventOccurrence extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/view';

    protected function canAccess()
    {
        $occurrence = EventOccurrenceObject::getByID($_REQUEST['occurrenceID']);
        if (is_object($occurrence)) {
            $calendar = $occurrence->getEvent()->getCalendar();
            if (is_object($calendar)) {
                $cp = new \Permissions($calendar);

                return $cp->canViewCalendarInEditInterface();
            }
        }

        return false;
    }

    public function view()
    {
        if ($this->canAccess(false)) {
            $occurrence = EventOccurrenceObject::getByID($this->request->query->get('occurrenceID'));
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }

            $linkFormatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
            $url = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
            $this->set('url', $url);
            $this->set('dateFormatter', $this->app->make(CalendarServiceProvider::class)->getDateFormatter());
            $this->set('occurrence', $occurrence);
            $this->requireAsset('core/lightbox');
        } else {
            die('Access Denied.');
        }
    }
}
