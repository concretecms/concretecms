<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventRepetition;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersionRepetition;

class EventOccurrenceFactory
{
    /**
     * @param CalendarEvent $event
     * @param       $start
     * @param       $end
     *
     * @return CalendarEventVersionOccurrence
     */
    public function createEventOccurrence(CalendarEventVersion $version, CalendarEventRepetition $repetition, $start, $end)
    {
        return new CalendarEventVersionOccurrence($version, $repetition, $start, $end);
    }

    /**
     * @param int $start_time The earliest possible time for an event to occur
     * @param int $end_time The latest possible time for an event to occur
     *
     * @return CalendarEventVersionOccurrence[]
     */
    public function generateOccurrences(CalendarEventVersion $version, CalendarEventVersionRepetition $repetitionEntity, $start_time, $end_time)
    {

        $saver = \Core::make(EventOccurrenceService::class);

        $repetition = $repetitionEntity->getRepetitionObject();

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $initial_occurrence_time = (new \DateTime($repetition->getStartDate(), $repetition->getTimezone()))
            ->getTimestamp();
        if ($repetition->getEndDate()) {
            $initial_occurrence_time_end = (new \DateTime($repetition->getEndDate(), $repetition->getTimezone()))
                ->getTimestamp();
        } else {
            $initial_occurrence_time_end = $initial_occurrence_time;
        }

        $initial_occurrence = $this->createEventOccurrence(
            $version,
            $repetitionEntity->getRepetition(),
            $initial_occurrence_time,
            $initial_occurrence_time_end
        );

        if ($initial_occurrence_time >= $start_time && $initial_occurrence_time <= $end_time) {
            $saver->save($initial_occurrence);
        }

        $all_occurrences = array();
        foreach ($occurrences as $occurrence) {
            if ($occurrence[0] === $initial_occurrence->getStart()) {
                continue;
            }
            $all_occurrences[] = $saver->save(
                $this->createEventOccurrence($version, $repetitionEntity->getRepetition(), $occurrence[0], $occurrence[1])
            );
        }

        return $all_occurrences;
    }
}
