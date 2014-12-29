<?php
namespace Concrete\Core\Foundation\Repetition;

/**
 * Abstract repetition class
 * This class is used to define and match against various time windows
 *
 * @package Concrete\Core\Foundation\Repetition
 */
abstract class AbstractRepetition implements RepetitionInterface
{

    /**
     * @TODO: Describe each of these fields with a type, I am unsure of the expected format and value of most of these
     */
    protected $startDate;
    protected $endDate;
    protected $startDateAllDay;
    protected $endDateAllDay;
    protected $repeatPeriod;
    protected $repeatPeriodWeekDays;
    protected $repeatEveryNum;
    protected $repeatMonthBy;
    protected $repeatPeriodEnd;

    /**
     * Set the start date
     *
     * @param $start_date
     * @return void
     */
    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    /**
     * Set the end date
     *
     * @param $end_date
     * @return void
     */
    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    /**
     * Toggle whether `start_date` is all day
     *
     * @param bool $start_date_all_day
     * @return void
     */
    public function setStartDateAllDay($start_date_all_day)
    {
        $this->startDateAllDay = $start_date_all_day;
    }

    /**
     * Toggle whether `end_date` is all day
     *
     * @param bool $end_date_all_day
     * @return void
     */
    public function setEndDateAllDay($end_date_all_day)
    {
        $this->endDateAllDay = $end_date_all_day;
    }

    /**
     * @param $repeat_period
     * @return void
     */
    public function setRepeatPeriod($repeat_period)
    {
        $this->repeatPeriod = $repeat_period;
    }

    /**
     * @param $repeat_period_week_days
     * @return mixed
     */
    public function setRepeatPeriodWeekDays($repeat_period_week_days)
    {
        $this->repeatPeriodWeekDays = $repeat_period_week_days;
    }

    /**
     * @param $repeat_every_num
     * @return mixed
     */
    public function setRepeatEveryNum($repeat_every_num)
    {
        $this->repeatEveryNum = $repeat_every_num;
    }

    /**
     * @param $repeat_month_by
     * @return mixed
     */
    public function setRepeatMonthBy($repeat_month_by)
    {
        $this->repeatMonthBy = $repeat_month_by;
    }

    /**
     * @param $repeat_period_end
     * @return mixed
     */
    public function setRepeatPeriodEnd($repeat_period_end)
    {
        $this->repeatPeriodEnd = $repeat_period_end;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return bool
     */
    public function isStartDateAllDay()
    {
        return !!$this->startDateAllDay;
    }

    /**
     * @return bool
     */
    public function isEndDateAllDay()
    {
        return !!$this->startDateAllDay;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return bool
     */
    public function repeats()
    {
        return (in_array($this->repeatPeriod, array('daily', 'weekly', 'monthly')));
    }

    /**
     * @return mixed
     */
    public function getRepeatPeriod()
    {
        return $this->repeatPeriod;
    }

    /**
     * @return array
     */
    public function getRepeatPeriodWeekDays()
    {
        return (array)$this->repeatPeriodWeekDays;
    }

    /**
     * @return mixed
     */
    public function getRepeatMonthBy()
    {
        return $this->repeatMonthBy;
    }

    /**
     * @return mixed
     */
    public function getRepeatEveryNum()
    {
        return $this->repeatEveryNum;
    }

    /**
     * @return mixed
     */
    public function getRepeatPeriodEnd()
    {
        return $this->repeatPeriodEnd;
    }

    /**
     * @param int|null $now The timestamp to check against
     * @return bool
     */
    public function isActive($now = null)
    {
        if (!$now) {
            $now = \Core::make('helper/date')->getOverridableNow(true);
        }
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
                    if (($numMonths % $this->getRepeatEveryNum()) == 0) {
                        // now we check to see if it's on the right day
                        if ($this->getRepeatMonthBy() == 'month') {
                            // that means it has to be the same day of the month. e.g. the 29th, etc..
                            if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
                                $checkTime = true;
                            }
                        } elseif ($this->getRepeatMonthBy() == 'week') {
                            // the last sunday? etc..
                            $savedWeekNum = date("W", strtotime($this->getStartDate())) - date(
                                    "W",
                                    strtotime(
                                        date(
                                            "Y-m-01",
                                            strtotime(
                                                $this->getStartDate())))) + 1;
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

    /**
     * @return string
     */
    public function getTextRepresentation()
    {
        return (string)$this;
    }

    public function __toString()
    {
        $dh = \Core::make('helper/date');
        /* @var $dh \Concrete\Core\Localization\Service\Date */
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

}
