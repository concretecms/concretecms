<?php
namespace Concrete\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Calendar\CalendarServiceProvider;

class CalendarFeed extends Controller
{
    public function view($calendar_id)
    {
        $calendar = Calendar::getByID($calendar_id);
        if (is_object($calendar)) {
            $cp = new \Permissions($calendar);
            if ($cp->canAccessCalendarRssFeed()) {
                if ($calendar->getEventPageParentID()) {
                    $calendarPage = Page::getByID($calendar->getEventPageParentID());
                    $calendarLink = $calendarPage->getCollectionLink(true);
                } else {
                    $calendarLink = BASE_URL . DIR_REL;
                }

                $date = \Core::make('date')->date('Y-m-d');
                $time = \Core::make('date')->toDateTime($date . ' 00:00:00')->getTimestamp();

                $list = new EventOccurrenceList();
                $list->filterByStartTimeAfter($time);
                $list->filterByCalendar($calendar);

                $writer = new \Zend\Feed\Writer\Feed();
                $writer->setTitle($calendar->getName());
                $writer->setLink($calendarLink);
                $writer->setDescription(t('Calendar Events for Calendar: %s', $calendar->getName()));

                $results = $list->getResults();

                /**
                 * @var EventOccurrence
                 */
                foreach ($results as $occurrence) {
                    $entry = $writer->createEntry();
                    $entry->setTitle($occurrence->getEvent()->getName());
                    $entry->setDateCreated(\Core::make('date')->toDateTime($occurrence->getStart()));
                    $content = $occurrence->getEvent()->getDescription();
                    if (!$content) {
                        $content = t('No Content.');
                    }
                    $entry->setDescription($content);
                    $linkFormatter = $this->app->make(CalendarServiceProvider::class)->getLinkFormatter();
                    $url = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
                    if ($url) {
                        $link = $url;
                    } else {
                        $link = $calendarLink;
                    }
                    $entry->setLink($link);
                    $writer->addEntry($entry);
                }

                return Response::create($writer->export('rss'), 200, array('Content-Type' => 'text/xml'));
            }
        }

        return new Response(t('Access Denied'));
    }
}
