<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;

use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Topic;
use Concrete\Core\Page\Controller\DashboardCalendarPageController;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use URL;
use Concrete\Core\Calendar\Utility\Preferences;

class Events extends DashboardCalendarPageController
{
    public function view($caID = null, $year = null, $month = null)
    {

        $this->requireAsset('core/calendar/admin');

        /**
         * @var $preferences Preferences
         */
        $preferences = $this->app->make(Preferences::class);
        $preferences->setPreferredViewToGrid();

        list($calendar, $calendars) = $this->getCalendars($caID);

        $this->set('calendars', $calendars);
        $this->set('calendarPermissions', new \Permissions($calendar));
        $this->set('calendar', $calendar);

        $dh = $this->app->make('date');
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }

        $monthYearTimestamp = strtotime($year . '-' . substr('0' . $month, -2) . '-01');
        $firstDayInMonthNum = date('N', $monthYearTimestamp) % 7;
        $previousLinkMonth = $month == 1 ? 12 : $month - 1;
        $previousLinkYear = $month == 1 ? $year - 1 : $year;
        $nextLinkMonth = $month == 12 ? 1 : $month + 1;
        $nextLinkYear = $month == 12 ? $year + 1: $year;

        $session = \Core::make('session');
        $topic_id = $this->request->get('topic_id', $session->get('dashboard_calendar_events_topic_list', null));

        $nextLink = URL::to('/dashboard/calendar/events/view', $calendar->getID(), $nextLinkYear, $nextLinkMonth);
        $previousLink = URL::to('/dashboard/calendar/events/view', $calendar->getID(), $previousLinkYear, $previousLinkMonth);
        $todayLink = URL::to('/dashboard/calendar/events/view', $calendar->getID());

        if ($topic_id) {
            $topic_id = intval($topic_id, 10);
            $topic = Node::getByID($topic_id);

            if ($topic instanceof Topic) {
                $this->set('topic', $topic);

                $query = "?topic_id={$topic_id}";
                $nextLink .= $query;
                $previousLink .= $query;
                $todayLink .= $query;

                $session->set('dashboard_calendar_events_topic_list', $topic_id);
            }
        } elseif (is_numeric($topic_id) && $topic_id == '0') {
            $session->remove('dashboard_calendar_events_topic_list');
        }

        // Set the date picker and keep the time zone properly available. Sigh.
        $timezone = $calendar->getTimezone();
        $todayDate = new \DateTime($year . '-' . substr('0' . $month, -2) . '-01 00:00:00', new \DateTimeZone($timezone));
        $todayDateTimestamp = $todayDate->getTimestamp();
        $this->set('monthText', $dh->date('F', $monthYearTimestamp, $todayDate->getTimezone()));
        $this->set('month', $month);
        $this->set('year', $year);
        $this->set('daysInMonth', date('t', $monthYearTimestamp));
        $this->set('firstDayInMonthNum', $firstDayInMonthNum);
        $this->set('nextLink', $nextLink);
        $this->set('previousLink', $previousLink);
        $this->set('todayLink', $todayLink);
        $this->set('todayDateTimestamp', $todayDateTimestamp);
        $serviceProvider = $this->app->make(CalendarServiceProvider::class);
        $this->set('dateFormatter', $serviceProvider->getDateFormatter());
        $this->set('linkFormatter', $serviceProvider->getLinkFormatter());

        $editor = $this->app->make('editor');
        $editor->requireEditorAssets();
        $this->requireAsset('moment');
    }

    public function calendar_deleted()
    {
        $this->set('success', t('Calendar deleted successfully.'));
        $this->view();
    }

    public function delete_calendar()
    {
        $caID = $this->request->request->get('caID');
        if (\Core::make("helper/validation/numbers")->integer($caID)) {
            if ($caID > 0) {
                $calendar = Calendar::getByID($caID);
                $cp = new \Permissions($calendar);
                if (!$cp->canDeleteCalendar()) {
                    unset($calendar);
                }
            }
        }

        if (!is_object($calendar)) {
            $this->error->add(t('Invalid calendar.'));
        }
        if (!$this->token->validate('delete_calendar')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            Calendar::delete($calendar);
            $this->redirect('/dashboard/calendar/events', 'calendar_deleted');
        }
    }
}
