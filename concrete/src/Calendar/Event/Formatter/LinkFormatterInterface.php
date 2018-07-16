<?php
namespace Concrete\Core\Calendar\Event\Formatter;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;

interface LinkFormatterInterface
{

    /**
     * @param CalendarEventVersionOccurrence $occurrence
     * @return string
     */
    function getEventOccurrenceBackgroundColor(CalendarEventVersionOccurrence $occurrence);

    /**
     * @param CalendarEventVersionOccurrence $occurrence
     * @return string
     */
    function getEventOccurrenceTextColor(CalendarEventVersionOccurrence $occurrence);


    function getEventOccurrenceLinkObject(CalendarEventVersionOccurrence $occurrence);

    /**
     * @param CalendarEventVersionOccurrence $occurrence
     * @return string
     */
    function getEventOccurrenceFrontendViewLink(CalendarEventVersionOccurrence $occurrence);

    /**
     * @param CalendarEvent $event
     * @return string
     */
    function getEventFrontendViewLink(CalendarEvent $event);

}
