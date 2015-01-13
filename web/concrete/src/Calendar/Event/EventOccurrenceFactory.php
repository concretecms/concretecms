<?php
namespace Concrete\Core\Calendar\Event;

class EventOccurrenceFactory
{

    /**
     * @param Event $event
     * @param       $start
     * @param       $end
     * @return EventOccurrence
     */
    public function createEventOccurrence(Event $event, $start, $end)
    {
        return new EventOccurrence($event, $start, $end);
    }

    /**
     * @param Event $event
     * @param int   $start
     * @param int   $end
     * @return EventOccurrence[]
     */
    public function eventOccurrencesBetween(Event $event, $start, $end)
    {
        $occurrences = array();

        $repetition = $event->getRepetition();

        $start_date = $repetition->getStartDate();
        $end_date = $repetition->getEndDate();
        if (!$end_date) {
            $end_date = $repetition->getStartDate();
        }

        $repetition_start = strtotime($start_date);
        $repetition_end = strtotime($end_date);
        $repetition_num = $repetition->getRepeatEveryNum();

        if (!$repetition->repeats()) {
            if ($repetition_start > $start && $repetition_start < $end) {
                $occurrences[] = $this->createEventOccurrence($event, $repetition_start, $repetition_end);
                return $occurrences;
            }
            return $occurrences;
        } else {
            $today = floor($start / 86400);
            $repetition_day = floor($repetition_start / 86400);

            if ($repetition_day > $today) {
                $today = $repetition_day;
            }
            switch ($repetition->getRepeatPeriod()) {

                case $repetition::REPEAT_DAILY:
                    if ($repetition_difference = ($today - $repetition_day) % $repetition_num) {
                        $today -= $repetition_difference;
                        $today += $repetition_num;
                    }

                    $day_difference = $today - $repetition_day;
                    $current_date = strtotime("+{$day_difference} days", $repetition_start);

                    while ($current_date < $end) {
                        $occurrences[] = $this->createEventOccurrence(
                            $event,
                            $current_date,
                            $current_date + $repetition_end - $repetition_start);
                        $current_date = strtotime("+{$repetition_num} days", $current_date);
                    }

                    return $occurrences;
                    break;

                case $repetition::REPEAT_WEEKLY:
                    $start_time = $start;
                    if (date('w', $start_time) != '0') {
                        $start_time = strtotime('last sunday', $start_time);
                    }

                    $weeks = floor(($start_time - $repetition_start) / (86400 * 7));
                    if ($difference = ($weeks % 3)) {
                        $start_time = strtotime(
                            "+{$repetition_num} weeks",
                            strtotime("-{$difference} weeks", $start_time));
                    }

                    $current_date = strtotime(date('Y-m-d ', $start_time) . date('H:i:s', $repetition_start));
                    while ($current_date < $end) {
                        foreach ($repetition->getRepeatPeriodWeekDays() as $day) {
                            $day_of_the_week = strtotime("+{$day} days", $current_date);
                            if ($day_of_the_week < $end) {
                                $occurrences[] = $this->createEventOccurrence(
                                    $event,
                                    $day_of_the_week,
                                    $day_of_the_week + $repetition_end - $repetition_start);
                            }
                        }

                        $current_date = strtotime("+{$repetition_num} weeks", $current_date);
                    }

                    return $occurrences;
                    break;

                case $repetition::REPEAT_MONTHLY:

            }

        }
    }

}
