<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;
use Database;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;

class Duration extends AbstractRepetition
{
    protected $pdID;

    /**
     * @param \Concrete\Core\Permission\Access\ListItem\ListItem[] $list
     *
     * @return \Concrete\Core\Permission\Access\ListItem\ListItem[]
     */
    public static function filterByActive($list)
    {
        $filteredList = [];
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
    public static function createFromRequest()
    {
        $app = Application::getFacadeApplication();
        $dt = $app->make('helper/form/date_time');
        /* @var \Concrete\Core\Form\Service\Widget\DateTime $dt */
        $dateStartDT = $dt->translate('pdStartDate', null, true);
        $dateEndDT = $dt->translate('pdEndDate', null, true);
        $result = null;
        if ($dateStartDT !== null || $dateEndDT !== null) {
            $service = $app->make('helper/date');
            /* @var \Concrete\Core\Localization\Service\Date $service */
            $request = Request::getInstance();
            // create a Duration object
            $pd = new self();

            $pd->setStartDateAllDay(0);
            if ($dateStartDT === null) {
                $dateStart = '';
            } else {
                $dateStart = $dateStartDT->format('Y-m-d H:i:s');
                if ($request->get('pdStartDateAllDayActivate')) {
                    // We need to work in the user timezone, otherwise we risk to change the day
                    $dateStart = $service->toDateTime($dateStart, 'user', 'system')->format('Y-m-d').' 00:00:00';
                    $pd->setStartDateAllDay(1);
                }
            }
            $pd->setEndDateAllDay(0);
            if ($dateEndDT === null) {
                $dateEnd = '';
            } else {
                $dateEnd = $dateEndDT->format('Y-m-d H:i:s');
                if ($request->get('pdEndDateAllDayActivate')) {
                    // We need to work in the user timezone, otherwise we risk to change the day
                    $dateEnd = $service->toDateTime($dateEnd, 'user', 'system')->format('Y-m-d').' 23:59:59';
                    $pd->setEndDateAllDay(1);
                }
            }
            $pd->setStartDate($dateStart);
            $pd->setEndDate($dateEnd);
            if ($request->request->get('pdRepeatPeriod') && $request->request->get('pdRepeat')) {
                switch ($request->request->get('pdRepeatPeriod')) {
                    case 'daily':
                        $pd->setRepeatPeriod(self::REPEAT_DAILY);
                        $pd->setRepeatEveryNum($request->request->get('pdRepeatPeriodDaysEvery'));
                        break;
                    case 'weekly':
                        $pd->setRepeatPeriod(self::REPEAT_WEEKLY);
                        $pd->setRepeatEveryNum($request->request->get('pdRepeatPeriodWeeksEvery'));
                        $pd->setRepeatPeriodWeekDays($request->request->get('pdRepeatPeriodWeeksDays'));
                        break;
                    case 'monthly':
                        $pd->setRepeatPeriod(self::REPEAT_MONTHLY);
                        switch ($request->request->get('pdRepeatPeriodMonthsRepeatBy')) {
                            case 'month':
                                $repeat = self::MONTHLY_REPEAT_MONTHLY;
                                break;
                            case 'lastweekday':
                                $repeat = self::MONTHLY_REPEAT_LAST_WEEKDAY;
                                $dotw = $request->request->get('pdRepeatPeriodMonthsRepeatLastDay') ?: 0;
                                $pd->setRepeatMonthLastWeekday((int) $dotw);
                                break;
                            case 'week':
                            default:
                                $repeat = self::MONTHLY_REPEAT_WEEKLY;
                                break;
                        }
                        $pd->setRepeatMonthBy($repeat);
                        $pd->setRepeatEveryNum($request->request->get('pdRepeatPeriodMonthsEvery'));
                        break;
                }
                $pd->setRepeatPeriodEnd($dt->translate('pdEndRepeatDateSpecific'));
            } else {
                $pd->setRepeatPeriod(self::REPEAT_NONE);
            }
            $pd->save();
            $result = $pd;
        }

        return $result;
    }

    /**
     * @param $pdID
     *
     * @return \Concrete\Core\Permission\Duration
     */
    public static function getByID($pdID)
    {
        $db = Database::connection();
        $pdObject = $db->fetchColumn('SELECT pdObject FROM PermissionDurationObjects WHERE pdID = ?', [$pdID]);
        if ($pdObject) {
            $pd = unserialize($pdObject);

            return $pd;
        }

        return null;
    }

    public function save()
    {
        $db = Database::connection();
        if (!$this->pdID) {
            $pd = new self();
            $pdObject = serialize($pd);
            $db->executeQuery('INSERT INTO PermissionDurationObjects (pdObject) VALUES (?)', [$pdObject]);
            $this->pdID = $db->lastInsertId();
        }
        $pdObject = serialize($this);
        $db->executeQuery(
            'UPDATE PermissionDurationObjects SET pdObject = ? WHERE pdID = ?',
            [$pdObject, $this->pdID]
        );
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
