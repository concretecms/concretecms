<?php
namespace Concrete\Core\Foundation\Repetition;

class Comparator
{

    /**
     * Returns true if the two repetitions are equal.
     * @param RepetitionInterface $r1
     * @param RepetitionInterface $r2
     */
    public function areEqual(RepetitionInterface $r1, RepetitionInterface $r2)
    {
        if ($r1->getStartDate() != $r2->getStartDate()) {
            return false;
        }

        if ($r1->getTimezone()->getName() != $r2->getTimezone()->getName()) {
            return false;
        }

        if ($r1->getEndDate() != $r2->getEndDate()) {
            return false;
        }

        if ($r1->isStartDateAllDay() != $r2->isStartDateAllDay()) {
            return false;
        }

        if ($r1->isEndDateAllDay() != $r2->isEndDateAllDay()) {
            return false;
        }

        if ($r1->getRepeatPeriod() != $r2->getRepeatPeriod()) {
            return false;
        }

        if ($r1->getRepeatMonthBy() != $r2->getRepeatMonthBy()) {
            return false;
        }

        if ($r1->getRepeatEveryNum() != $r2->getRepeatEveryNum()) {
            return false;
        }

        if ($r1->getRepeatPeriodEnd() != $r2->getRepeatPeriodEnd()) {
            return false;
        }

        foreach($r1->getRepeatPeriodWeekDays() as $weekDay) {
            if (!in_array($weekDay, $r2->getRepeatPeriodWeekDays())) {
                return false;
            }
        }

        if ($r1->getRepeatPeriodWeekDays() != $r2->getRepeatPeriodWeekDays()) {
        }return true;
    }
}

