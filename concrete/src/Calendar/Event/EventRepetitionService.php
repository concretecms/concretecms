<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Foundation\Repetition\Comparator;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEventRepetition;

class EventRepetitionService
{
    protected $entityManager;
    protected $formDateTime;

    public function __construct(EntityManagerInterface $entityManagerInterface, DateTime $formDateTime)
    {
        $this->entityManager = $entityManagerInterface;
        $this->formDateTime = $formDateTime;
    }

    public function getByID($id)
    {
        $r = $this->entityManager->getRepository(CalendarEventRepetition::class);
        return $r->findOneByRepetitionID($id);
    }

    /**
     * @param string $namespace
     * @param Calendar $calendar
     * @param \Concrete\Core\Http\Request $request
     *
     * @return \Concrete\Core\Entity\Calendar\CalendarEventRepetition[]
     */
    public function translateFromRequest($namespace, Calendar $calendar, $request)
    {
        $sets = $request->request->get($namespace . '_repetitionSetID');
        $r = $request->request;

        $repetitions = array();

        foreach($sets as $repetitionSetID) {

            $dateStart = $r->get($namespace . '_pdStartDate_' . $repetitionSetID);
            $dateEnd = $r->get($namespace . '_pdEndDate_' . $repetitionSetID);
            if ($dateStart || $dateEnd) {

                // create a Repetition object

                $pd = new EventRepetition();

                $timezone = $calendar->getSite()->getConfigRepository()->get('timezone');
                if (!$timezone) {
                    $timezone = date_default_timezone_get();
                }

                $timezone = new \DateTimeZone($timezone);
                $pd->setTimezone($timezone);

                if ($r->get($namespace . '_pdStartDateAllDayActivate_' . $repetitionSetID)) {
                    $pd->setStartDateAllDay(true);
                    $pd->setEndDateAllDay(true);
                    $dateStart = date('Y-m-d 00:00:00', strtotime($dateStart));
                    $dateEnd = date('Y-m-d 23:59:59', strtotime($dateEnd));
                } else {
                    $pd->setStartDateAllDay(0);
                    // Grab the times.
                    $dateStart = date('Y-m-d H:i:s', strtotime($dateStart . ' ' . $r->get($namespace . '_pdStartDateSelectTime_' . $repetitionSetID)));
                    $dateEnd = date('Y-m-d H:i:s', strtotime($dateEnd . ' ' . $r->get($namespace . '_pdEndDateSelectTime_' . $repetitionSetID)));
                }

                $pd->setStartDate($dateStart);
                $pd->setEndDate($dateEnd);

                if ($r->get($namespace . '_pdRepeatPeriod_' . $repetitionSetID) && $r->get($namespace . '_pdRepeat_' . $repetitionSetID)) {
                    if ($r->get($namespace . '_pdRepeatPeriod_' . $repetitionSetID) == 'daily') {
                        $pd->setRepeatPeriod($pd::REPEAT_DAILY);
                        $pd->setRepeatEveryNum($r->get($namespace . '_pdRepeatPeriodDaysEvery_' . $repetitionSetID));
                    } elseif ($r->get($namespace . '_pdRepeatPeriod_' . $repetitionSetID) == 'weekly') {
                        $pd->setRepeatPeriod($pd::REPEAT_WEEKLY);
                        $pd->setRepeatEveryNum($r->get($namespace . '_pdRepeatPeriodWeeksEvery_' . $repetitionSetID));
                        $pd->setRepeatPeriodWeekDays($r->get($namespace . '_pdRepeatPeriodWeeksDays_' . $repetitionSetID));
                    } elseif ($r->get($namespace . '_pdRepeatPeriod_' . $repetitionSetID) == 'monthly') {
                        $pd->setRepeatPeriod($pd::REPEAT_MONTHLY);
                        $repeat_by = $r->get($namespace . '_pdRepeatPeriodMonthsRepeatBy_' . $repetitionSetID);
                        $repeat = $pd::MONTHLY_REPEAT_WEEKLY;
                        switch ($repeat_by) {
                            case 'week':
                                $repeat = $pd::MONTHLY_REPEAT_WEEKLY;
                                break;
                            case 'month':
                                $repeat = $pd::MONTHLY_REPEAT_MONTHLY;
                                break;
                            case 'lastweekday':
                                $repeat = $pd::MONTHLY_REPEAT_LAST_WEEKDAY;
                                $dotw = $r->get($namespace . '_pdRepeatPeriodMonthsRepeatLastDay_' . $repetitionSetID);
                                if (!$dotw) {
                                    $dotw = 0;
                                }
                                $pd->setRepeatMonthLastWeekday($dotw);
                                break;
                        }

                        $pd->setRepeatMonthBy($repeat);
                        $pd->setRepeatEveryNum($r->get($namespace . '_pdRepeatPeriodMonthsEvery_' . $repetitionSetID));
                    }

                    $pdEndRepeatDate = $r->get($namespace . '_pdEndRepeatDate_' . $repetitionSetID);
                    if ($pdEndRepeatDate == 'date') {
                        $pd->setRepeatPeriodEnd($this->formDateTime->translate($namespace . '_pdEndRepeatDateSpecific_' . $repetitionSetID));
                    } else {
                        $pd->setRepeatPeriodEnd(null);
                    }
                } else {
                    $pd->setRepeatPeriod($pd::REPEAT_NONE);
                }

                $reuseExisting = false;
                $comparator = new Comparator();
                if ($r->get($namespace . '_repetitionID_' . $repetitionSetID)) {
                    $pdEntity = $this->getByID($r->get($namespace . '_repetitionID_' . $repetitionSetID));
                    if ($pdEntity) {
                        $repetitionObject = $pdEntity->getRepetitionObject();
                        // If the object is the same as the one we have stored in the db, reuse the same object.
                        if ($comparator->areEqual($repetitionObject, $pd)) {
                            $repetitions[] = $pdEntity;
                            $reuseExisting = true;
                        }
                    }
                }

                if (!$reuseExisting) {
                    $pdEntity = new CalendarEventRepetition($pd);
                    $repetitions[] = $pdEntity;
                }
            }
        }

        return $repetitions;
    }


}
