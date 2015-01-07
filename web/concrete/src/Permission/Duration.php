<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;
use Loader;

class Duration extends AbstractRepetition
{

    public static function filterByActive($list)
    {
        $filteredList = array();
        foreach ($list as $l) {
            $pd = $l->getPermissionDurationObject();
            if (is_object($pd)) {
                if ($pd->isActive()) {
                    $filteredList[] = $l;
                }
            } else {
                $filteredList[] = $l;
            }
        }

        return $filteredList;
    }

    /**
     * @return Duration|null
     */
    public static function translateFromRequest()
    {
        $dt = Loader::helper('form/date_time');
        $dateStart = $dt->translate('pdStartDate');
        $dateEnd = $dt->translate('pdEndDate');

        if ($dateStart || $dateEnd) {
            // create a Duration object
            if ($_REQUEST['pdID']) {
                $pd = Duration::getByID($_REQUEST['pdID']);
            } else {
                $pd = new Duration();
            }

            if ($_REQUEST['pdStartDateAllDayActivate']) {
                $pd->setStartDateAllDay(1);
                $dateStart = date('Y-m-d 00:00:00', strtotime($dateStart));
            } else {
                $pd->setStartDateAllDay(0);
            }
            if ($_REQUEST['pdEndDateAllDayActivate']) {
                $pd->setEndDateAllDay(1);
                $dateEnd = date('Y-m-d 23:59:59', strtotime($dateEnd));
            } else {
                $pd->setEndDateAllDay(0);
            }

            $pd->setStartDate($dateStart);
            $pd->setEndDate($dateEnd);
            if ($_POST['pdRepeatPeriod'] && $_POST['pdRepeat']) {

                if ($_POST['pdRepeatPeriod'] == 'daily') {
                    $pd->setRepeatPeriod(Duration::REPEAT_DAILY);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodDaysEvery']);
                } elseif ($_POST['pdRepeatPeriod'] == 'weekly') {
                    $pd->setRepeatPeriod(Duration::REPEAT_WEEKLY);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodWeeksEvery']);
                    $pd->setRepeatPeriodWeekDays($_POST['pdRepeatPeriodWeeksDays']);
                } elseif ($_POST['pdRepeatPeriod'] == 'monthly') {
                    $pd->setRepeatPeriod(Duration::REPEAT_MONTHLY);
                    $repeat = $_POST['pdRepeatPeriodMonthsRepeatBy'] === 'weekly' ?
                        Duration::MONTHLY_REPEAT_WEEKLY :
                        Duration::MONTHLY_REPEAT_MONTHLY;
                    $pd->setRepeatMonthBy($repeat);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodMonthsEvery']);
                }
                $pd->setRepeatPeriodEnd($dt->translate('pdEndRepeatDateSpecific'));
            } else {
                $pd->setRepeatPeriod(Duration::REPEAT_NONE);
            }
            $pd->save();

            return $pd;
        } else {
            unset($pd);
        }
        return null;
    }

    public static function getByID($pdID)
    {
        $db = Loader::db();
        $pdObject = $db->getOne('SELECT pdObject FROM PermissionDurationObjects WHERE pdID = ?', array($pdID));
        if ($pdObject) {
            $pd = unserialize($pdObject);

            return $pd;
        }
    }

    public function save()
    {
        $db = Loader::db();
        if (!$this->pdID) {
            $pd = new Duration();
            $pdObject = serialize($pd);
            $db->Execute('INSERT INTO PermissionDurationObjects (pdObject) VALUES (?)', array($pdObject));
            $this->pdID = $db->Insert_ID();
        }
        $pdObject = serialize($this);
        $db->Execute('UPDATE PermissionDurationObjects SET pdObject = ? WHERE pdID = ?', array($pdObject, $this->pdID));
    }

    public function getID()
    {
        return $this->getPermissionDurationID();
    }

    public function getPermissionDurationID()
    {
        return $this->pdID;
    }

}
