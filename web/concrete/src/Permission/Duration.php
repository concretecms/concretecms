<?php
namespace Concrete\Core\Permission;

use \Concrete\Core\Foundation\Object;
use Loader;
use Core;

class Duration extends Object
{
    public function getPermissionDurationID() { return $this->pdID;}

    public static function getByID($pdID)
    {
        $db = Loader::db();
        $pdObject = $db->getOne('select pdObject from PermissionDurationObjects where pdID = ?', array($pdID));
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
            $db->Execute('insert into PermissionDurationObjects (pdObject) values (?)', array($pdObject));
            $this->pdID = $db->Insert_ID();
        }
        $pdObject = serialize($this);
        $db->Execute('update PermissionDurationObjects set pdObject = ? where pdID = ?', array($pdObject, $this->pdID));
    }

    public function setStartDate($pdStartDate) {$this->pdStartDate = $pdStartDate;}
    public function setEndDate($pdEndDate) {$this->pdEndDate = $pdEndDate;}
    public function setStartDateAllDay($pdStartDateAllDay) {$this->pdStartDateAllDay = $pdStartDateAllDay;}
    public function setEndDateAllDay($pdEndDateAllDay) {$this->pdEndDateAllDay = $pdEndDateAllDay;}
    public function setRepeatPeriod($pdRepeatPeriod) {$this->pdRepeatPeriod = $pdRepeatPeriod;}
    public function setRepeatPeriodWeekDays($pdRepeatPeriodWeeksDays) {$this->pdRepeatPeriodWeeksDays = $pdRepeatPeriodWeeksDays;}
    public function setRepeatEveryNum($pdRepeatEveryNum) {$this->pdRepeatEveryNum = $pdRepeatEveryNum;}
    public function setRepeatMonthBy($pdRepeatPeriodMonthsRepeatBy) {$this->pdRepeatPeriodMonthsRepeatBy = $pdRepeatPeriodMonthsRepeatBy;}
    public function setRepeatPeriodEnd($pdRepeatPeriodEnd) {$this->pdRepeatPeriodEnd = $pdRepeatPeriodEnd;}

    public function getStartDate() {return $this->pdStartDate;}
    public function isStartDateAllDay() {return $this->pdStartDateAllDay;}
    public function isEndDateAllDay() {return $this->pdEndDateAllDay;}
    public function getEndDate() {return $this->pdEndDate;}
    public function repeats()
    {
        return (in_array($this->pdRepeatPeriod, array('daily','weekly','monthly')));
    }
    public function getRepeatPeriod() {return $this->pdRepeatPeriod;}
    public function getRepeatPeriodWeekDays()
    {
        if (is_array($this->pdRepeatPeriodWeeksDays)) {
            return $this->pdRepeatPeriodWeeksDays;
        } else {
            return array();
        }
    }
    public function getRepeatMonthBy() {return $this->pdRepeatPeriodMonthsRepeatBy;}
    public function getRepeatPeriodEveryNum() {return $this->pdRepeatEveryNum;}
    public function getRepeatPeriodEnd() {return $this->pdRepeatPeriodEnd;}

    public function isActive()
    {
        $now = Loader::helper('date')->getOverridableNow(true);
        if (!$this->repeats()) {
            $isActive = true;
            if ($this->getStartDate() != '' && strtotime($this->getStartDate()) > $now) {
                $isActive = false;
            }
            if ($this->getEndDate() != '' && strtotime($this->getEndDate()) < $now) {
                $isActive = false;
            }
        } else {
            $isActive = false;
            $startsOn = date('Y-m-d', strtotime($this->getStartDate()));
            $ymd = date('Y-m-d', $now);
            $dailyTimeStart = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getStartDate())));
            $dailyTimeEnd = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getEndDate())));
            switch ($this->getRepeatPeriod()) {
                case 'daily':
                    // number of days between now and the start
                    $numDays = round(($now - strtotime($startsOn)) / 86400);
                    if (($numDays % $this->getRepeatPeriodEveryNum()) == 0) {
                        if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                            $isActive = true;
                        }
                    }
                    break;
                case 'weekly':
                    $numWeeks = round(($now - strtotime($startsOn)) / (86400 * 7));
                    if (($numWeeks % $this->getRepeatPeriodEveryNum()) == 0) {
                        // now we check to see if it's on the right day
                        $startDOW = date('w', strtotime($this->getStartDate()));
                        $endDOW = date('w', strtotime($this->getEndDate()));
                        $dow = date('w', $now);
                        if ($startDOW == $endDOW) {
                            $days = $this->getRepeatPeriodWeekDays();
                            if (in_array($dow, $days)) {
                                if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                                    $isActive = true;
                                }
                            }
                        } else {
                            $checkTime = false;
                            if ($dow < $endDOW && $dow > $startDOW) {
                                $isActive = true; // we fall between the date range so we know it's perfect
                            } elseif ($dow == $startDOW) {
                                if ($now >= $dailyTimeStart) {
                                    $isActive = true;
                                }
                            } elseif ($dow == $endDOW) {
                                if ($now <= $dailyTimeEnd) {
                                    $isActive = true;
                                }
                            }
                        }
                    }
                    break;
                case 'monthly':
                    $numMonths = round(($now - strtotime($startsOn)) / (86400 * 30));
                    $checkTime = false;
                    if (($numMonths % $this->getRepeatPeriodEveryNum()) == 0) {
                        // now we check to see if it's on the right day
                        if ($this->getRepeatMonthBy() == 'month') {
                            // that means it has to be the same day of the month. e.g. the 29th, etc..
                            if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
                                $checkTime = true;
                            }
                        } elseif ($this->getRepeatMonthBy() == 'week') {
                            // the last sunday? etc..
                            $savedWeekNum = date("W", strtotime($this->getStartDate())) - date("W", strtotime(date("Y-m-01", strtotime($this->getStartDate())))) + 1;
                            $nowWeekNum = date("W", $now) - date("W", strtotime(date("Y-m-01", $now))) + 1;
                            if ($savedWeekNum == $nowWeekNum) {
                                if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
                                    $checkTime = true;
                                }
                            }
                        }

                        if ($checkTime) {
                            if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                                $isActive = true;
                            }
                        }
                    }
                    break;

            }

            if ($this->getStartDate() != '' && strtotime($this->getStartDate()) > $now) {
                $isActive = false;
            }
            if ($this->getRepeatPeriodEnd() != '' && strtotime($ymd) > strtotime($this->getRepeatPeriodEnd())) {
                $isActive = false;
            }

        }

        return $isActive;
    }

    public function getTextRepresentation()
    {
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        $text = '';
        if ($this->getStartDate() != '') {
            $text .= t('Starts %s. ', $dh->formatDateTime($this->getStartDate()));
        } else {
            $text .= t('Already Started. ');
        }
        if ($this->getEndDate() != '') {
            $text .= t('Ends %s. ', $dh->formatDateTime($this->getEndDate()));
        } else {
            $text .= t('No End Date. ');
        }
        if ($this->repeats()) {
            $text .= t('Repeats %s. ', ucfirst($this->getRepeatPeriod()));
        }

        return $text;
    }

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
                $pd->setRepeatPeriod($_POST['pdRepeatPeriod']);
                if ($_POST['pdRepeatPeriod'] == 'daily') {
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodDaysEvery']);
                } elseif ($_POST['pdRepeatPeriod'] == 'weekly') {
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodWeeksEvery']);
                    $pd->setRepeatPeriodWeekDays($_POST['pdRepeatPeriodWeeksDays']);
                } elseif ($_POST['pdRepeatPeriod'] == 'monthly') {
                    $pd->setRepeatMonthBy($_POST['pdRepeatPeriodMonthsRepeatBy']);
                    $pd->setRepeatEveryNum($_POST['pdRepeatPeriodMonthsEvery']);
                }
                $pd->setRepeatPeriodEnd($dt->translate('pdEndRepeatDateSpecific'));
            } else {
                $pd->setRepeatPeriod(false);
            }
            $pd->save();
        } else {
            unset($pd);
        }

        return $pd;
    }

}
