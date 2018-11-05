<?php
namespace Concrete\Core\Calendar\Event\Formatter;

use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;

class DateFormatter
{

    public function getOccurrenceDateString(CalendarEventVersionOccurrence $occurrence)
    {
        $date = \Core::make('date');
        $duration = $occurrence->getRepetition();
        $isSameDayEvent = false;

        $timezone = $occurrence->getEvent()->getCalendar()->getSite()->getConfigRepository()->get('timezone');

        $startDateAllDay = $date->formatDate($occurrence->getStart(), true, $timezone);
        $endDateAllDay = $date->formatDate($occurrence->getEnd(), true, $timezone);

        if ($startDateAllDay == $endDateAllDay) {
            $isSameDayEvent = true;
        }

        if ($duration->isStartDateAllDay()) {
            $start = $startDateAllDay;
        } else {
            $start = $date->formatDateTime($occurrence->getStart(), true, false, $timezone);
        }

        if ($duration->getEndDate()) {
            if ($duration->isEndDateAllDay()) {
                if ($isSameDayEvent) {
                    return $start;
                } else {
                    $end = $endDateAllDay;
                }
            } else {
                if ($isSameDayEvent) {
                    $end = $date->formatTime($occurrence->getEnd(), false, $timezone);
                } else {
                    $end = $date->formatDateTime($occurrence->getEnd(), true, false, $timezone);
                }
            }
        }

        if (isset($end)) {
            return $start . ' – ' . $end;
        } else {
            return $start;
        }
    }


}
