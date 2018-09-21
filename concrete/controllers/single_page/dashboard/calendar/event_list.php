<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;

use Concrete\Core\Permission\Checker;
use Concrete\Core\Page\Controller\DashboardCalendarPageController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Utility\Preferences;

class EventList extends DashboardCalendarPageController
{
    public function view($caID = null, $year = null, $month = null)
    {

        $this->requireAsset('core/calendar/admin');

        // note - month and year are not used here, but we
        // need to keep the method signature the same as the grid view
        // method.

        /**
         * @var $preferences Preferences
         */
        $preferences = $this->app->make(Preferences::class);
        $preferences->setPreferredViewToList();

        list($calendar, $calendars) = $this->getCalendars($caID);

        $permissions = new Checker($calendar);

        $this->set('calendars', $calendars);
        $this->set('calendarPermissions', $permissions);
        $this->set('calendar', $calendar);

        $list = new EventOccurrenceList();

        $date = $this->app->make('date')->date('Y-m-d');
        /*
        $time = $this->app->make('date')
            ->toDateTime($date . ' 00:00:00')
            ->getTimestamp();
        $list->filterByStartTimeAfter($time);
        */
        
        if ($this->request->query->has('query')) {
            $list->filterByKeywords($this->request->query->get('query'));
        }
        if ($this->request->query->has('topic_id')) {
            $topic = intval($this->request->query->get('topic_id'));
            if ($topic) {
                $list->filterByTopic($topic);
            }
        }

        $list->includeInactiveEvents();
        $list->filterByCalendar($calendar);
        $pagination = $list->getPagination();
        $results = $pagination->getCurrentPageResults();
        $this->set('events', $results);
        $this->set('pagination', $pagination);
        $serviceProvider = $this->app->make(CalendarServiceProvider::class);
        $this->set('dateFormatter', $serviceProvider->getDateFormatter());
        $this->set('linkFormatter', $serviceProvider->getLinkFormatter());
    }
}
