<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;

/**
 * Calendar Event repetition
 *
 * @package Concrete\Core\Calendar
 */
class EventRepetition extends AbstractRepetition
{

    protected $repetitionID;

    /**
     * @param $id
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getByID($id)
    {
        $id = intval($id, 10);
        $row = \Database::connection()->executeQuery(
            'SELECT * FROM CalendarEventRepetitions WHERE repetitionID = ' . $id)->fetch();

        if ($serialized = array_get($row, 'repetitionObject')) {
            $object = unserialize($serialized);
            $object->repetitionID = intval($id, 10);
            return $object;
        }

        return null;
    }

    /**
     * @return bool Success or failure
     */
    public function save()
    {
        $connection = \Database::connection();
        if (!$this->getID()) {
            $connection->insert(
                'CalendarEventRepetitions',
                array(
                    'repetitionObject' => serialize($this)
                ));
            $id = $connection->lastInsertId();

            $this->repetitionID = intval($id, 10);
        } else {
            $connection->update(
                'CalendarEventRepetitions',
                array(
                    'repetitionObject' => serialize($this)
                ),
                array(
                    'repetitionID' => $this->getID()
                ));
        }

        return true;
    }

    public function delete()
    {
        if ($this->getID() > 0) {
            $db = \Database::connection();
            if ($db->delete('CalendarEventRepetitions', array('repetitionID' => intval($this->getID())))) {
                return true;
            }
        }
        return false;
    }

    public function getID()
    {
        return $this->repetitionID;
    }

    /**
     * @return EventRepetition|null
     */
    public static function translateFromRequest($request, $new = false)
    {
        $dt = \Core::make('helper/form/date_time');
        $dateStart = $dt->translate('pdStartDate');
        $dateEnd = $dt->translate('pdEndDate');

        if ($dateStart || $dateEnd) {
            // create a Repetition object
            if (!$new && $request->request->get('repetitionID')) {
                $pd = static::getByID($request->request->get('repetitionID'));
            } else {
                $pd = new static();
            }

            if ($request->request->get('pdStartDateAllDayActivate')) {
                $pd->setStartDateAllDay(1);
                $dateStart = date('Y-m-d 00:00:00', strtotime($dateStart));
            } else {
                $pd->setStartDateAllDay(0);
            }
            if ($request->request->get('pdEndDateAllDayActivate')) {
                $pd->setEndDateAllDay(1);
                $dateEnd = date('Y-m-d 23:59:59', strtotime($dateEnd));
            } else {
                $pd->setEndDateAllDay(0);
            }

            $pd->setStartDate($dateStart);
            $pd->setEndDate($dateEnd);
            if ($_POST['pdRepeatPeriod'] && $_POST['pdRepeat']) {

                if ($_POST['pdRepeatPeriod'] == 'daily') {
                    $pd->setRepeatPeriod(self::REPEAT_DAILY);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodDaysEvery']);
                } elseif ($_POST['pdRepeatPeriod'] == 'weekly') {
                    $pd->setRepeatPeriod(self::REPEAT_WEEKLY);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodWeeksEvery']);
                    $pd->setRepeatPeriodWeekDays($_POST['pdRepeatPeriodWeeksDays']);
                } elseif ($_POST['pdRepeatPeriod'] == 'monthly') {
                    $pd->setRepeatPeriod(self::REPEAT_MONTHLY);

                    $repeat_by = $_POST['pdRepeatPeriodMonthsRepeatBy'];
                    $repeat = self::MONTHLY_REPEAT_WEEKLY;
                    switch ($repeat_by) {
                        case 'week':
                            $repeat = self::MONTHLY_REPEAT_WEEKLY;
                            break;
                        case 'month':
                            $repeat = self::MONTHLY_REPEAT_MONTHLY;
                            break;
                        case 'lastweekday':
                            $repeat = self::MONTHLY_REPEAT_LAST_WEEKDAY;
                            $dotw = $request->request->get('pdRepeatPeriodMonthsRepeatLastDay', 0);
                            $pd->setRepeatMonthLastWeekday($dotw);
                            break;
                    }

                    $pd->setRepeatMonthBy($repeat);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodMonthsEvery']);
                }
                $pd->setRepeatPeriodEnd($dt->translate('pdEndRepeatDateSpecific'));
            } else {
                $pd->setRepeatPeriod(self::REPEAT_NONE);
            }

            return $pd;
        } else {
            unset($pd);
        }
        return null;
    }
}
