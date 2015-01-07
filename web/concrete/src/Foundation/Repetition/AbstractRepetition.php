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
     * @var string Date string of the start date/time
     */
    protected $startDate;

    /**
     * @var string Date string of the end date/time
     */
    protected $endDate;

    /**
     * @var bool Does the startDate include a time or should we just use the entire day?
     */
    protected $startDateAllDay;

    /**
     * @var bool Does the endDate include a time, or should we just use the entire day?
     */
    protected $endDateAllDay;

    /**
     * @var int self::REPEAT_* enum [ ::REPEAT_DAILY | ::REPEAT_WEEKLY | ::REPEAT_MONTHLY ]
     */
    protected $repeatPeriod;

    /**
     * @var int[] List of numeric representations of the day of the week 0 for sunday, 6 for saturday.
     */
    protected $repeatPeriodWeekDays;

    /**
     * @var int Number for the `repeatPeriod`
     */
    protected $repeatEveryNum;

    /**
     * @var string [ month | week ]
     */
    protected $repeatMonthBy;

    /**
     * @var int Timestamp of the last possible time for repetition
     */
    protected $repeatPeriodEnd;

    /**
     * @return bool
     */
    public function isStartDateAllDay()
    {
        return !!$this->startDateAllDay;
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
     * @return bool
     */
    public function isEndDateAllDay()
    {
        return !!$this->startDateAllDay;
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
     * @param int|null $now The timestamp to check against
     * @return bool
     */
    public function isActive($now = null)
    {
        return !!$this->getActiveRange($now);
    }

    /**
     * @return array|null Returns a list [ start, end ] or null if miss
     */
    public function getActiveRange($now = null)
    {
        if (!$now) {
            $now = \Core::make('helper/date')->getOverridableNow(true);
        }

        if (!$this->repeats()) {
            $isActive = true;
            $start_date = $this->getStartDate();
            $end_date = $this->getEndDate();
            if (!$start_date || !$end_date) {
                return null;
            }
            if ($start_date && strtotime($start_date) > $now) {
                return null;
            }
            if ($end_date && strtotime($end_date) < $now) {
                return null;
            }
            if ($isActive) {
                return $this->rangeFromTime(strtotime($start_date));
            }
        } else {
            $startsOn = date('Y-m-d', strtotime($this->getStartDate()));
            $ymd = date('Y-m-d', $now);
            $dailyTimeStart = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getStartDate())));
            $dailyTimeEnd = strtotime($ymd . ' ' . date('H:i:s', strtotime($this->getEndDate())));

            if ($this->getStartDate() != '' && strtotime($this->getStartDate()) > $now) {
                return null;
            }
            if ($this->getRepeatPeriodEnd() != '' && strtotime($ymd) > strtotime($this->getRepeatPeriodEnd())) {
                return null;
            }

            switch ($this->getRepeatPeriod()) {

                case self::REPEAT_DAILY:
                    // number of days between now and the start
                    $numDays = round(($now - strtotime($startsOn)) / 86400);
                    if (($numDays % $this->getRepeatEveryNum()) == 0) {
                        if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                            return $this->rangeFromTime($dailyTimeStart);
                        }
                    }
                    break;

                case self::REPEAT_WEEKLY:
                    $numWeeks = round(($now - strtotime($startsOn)) / (86400 * 7));
                    if (!$this->getRepeatEveryNum() || ($numWeeks % $this->getRepeatEveryNum()) == 0) {
                        // now we check to see if it's on the right day
                        $startDOW = date('w', strtotime($this->getStartDate()));
                        $endDOW = date('w', strtotime($this->getEndDate()));
                        $dow = date('w', $now);
                        if ($startDOW == $endDOW) {
                            $days = $this->getRepeatPeriodWeekDays();
                            if (in_array($dow, $days)) {
                                if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                                    return $this->rangeFromTime($dailyTimeStart);
                                }
                            }
                        } else {
                            if ($dow <= $endDOW && $dow >= $startDOW) {

                                // Calculate start date
                                $start_dow_difference = $dow - $startDOW;
                                $start_day = strtotime("-{$start_dow_difference} days", $now);
                                $start_time = strtotime(date('Y-m-d ', $start_day) . date('H:i:s', $dailyTimeStart));

                                // Calculate end date
                                $end_dow_difference = $endDOW - $dow;
                                $end_day = strtotime("+{$end_dow_difference} days", $now);
                                $end_time = strtotime(date('Y-m-d ', $end_day) . date('H:i:s', $dailyTimeEnd));

                                if ($dow < $endDOW && $dow > $startDOW) {
                                    return $this->rangeFromTime($start_time);
                                } elseif ($dow == $startDOW) {
                                    if ($now >= $dailyTimeStart) {
                                        return $this->rangeFromTime($start_time);
                                    }
                                } elseif ($dow == $endDOW) {
                                    if ($now <= $dailyTimeEnd) {
                                        return $this->rangeFromTime($start_time);
                                    }
                                }
                            }
                        }
                    }
                    break;

                case self::REPEAT_MONTHLY:
                    $numMonths = round(($now - strtotime($startsOn)) / (86400 * 30));
                    $checkTime = false;
                    if (($numMonths % $this->getRepeatEveryNum()) == 0) {
                        $repeat = $this->getRepeatMonthBy();

                        switch ($repeat) {

                            case self::MONTHLY_REPEAT_MONTHLY:
                                // that means it has to be the same day of the month. e.g. the 29th, etc..
                                if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
                                    $checkTime = true;
                                }
                                break;

                            case self::MONTHLY_REPEAT_WEEKLY:
                                // the last sunday? etc..
                                $savedWeekNum = date("W", strtotime($this->getStartDate())) - date("W", strtotime(date("Y-m-01", strtotime($this->getStartDate())))) + 1;
                                $nowWeekNum = date("W", $now) - date("W", strtotime(date("Y-m-01", $now))) + 1;
                                if ($savedWeekNum == $nowWeekNum) {
                                    if (date('w', $now) == date('w', strtotime($this->getStartDate()))) {
                                        $checkTime = true;
                                    }
                                }

                                break;
                        }

                        if ($checkTime) {
                            if ($now >= $dailyTimeStart && $now <= $dailyTimeEnd) {
                                return $this->rangeFromTime($dailyTimeStart);
                            }
                        }
                    }
                    break;

            }

        }

        return null;
    }

    /**
     * @return bool
     */
    public function repeats()
    {
        return ($this->getRepeatPeriod() !== self::REPEAT_NONE);
    }

    /**
     * @return int Constant for repeat [ ::REPEAT_DAILY | ::REPEAT_WEEKLY | ::REPEAT_MONTHLY ]
     */
    public function getRepeatPeriod()
    {
        $period = $this->repeatPeriod;

        if ($period === self::REPEAT_DAILY) {
            return self::REPEAT_DAILY;
        }
        if ($period === self::REPEAT_WEEKLY) {
            return self::REPEAT_WEEKLY;
        }
        if ($period === self::REPEAT_MONTHLY) {
            return self::REPEAT_MONTHLY;
        }

        return self::REPEAT_NONE;
    }

    /**
     * @param int $repeat_period [ ::REPEAT_DAILY | ::REPEAT_WEEKLY | ::REPEAT_MONTHLY ]
     * @return void
     */
    public function setRepeatPeriod($repeat_period)
    {
        $this->repeatPeriod = $repeat_period;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

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
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
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

    protected function rangeFromTime($start)
    {
    return array($start, $start + (strtotime($this->getEndDate()) - strtotime($this->getStartDate())));
    }

    /**
     * @return int Timestamp of the last possible time for an occurrence
     */
    public function getRepeatPeriodEnd()
    {
        return $this->repeatPeriodEnd;
    }

    /**
     * @param $repeat_period_end
     * @return void
     */
    public function setRepeatPeriodEnd($repeat_period_end)
    {
        $this->repeatPeriodEnd = $repeat_period_end;
    }

    /**
     * @return int
     */
    public function getRepeatEveryNum()
    {
        return $this->repeatEveryNum;
    }

    /**
     * @param $repeat_every_num
     * @return void
     */
    public function setRepeatEveryNum($repeat_every_num)
    {
        $this->repeatEveryNum = $repeat_every_num;
    }

    /**
     * @return int[]
     */
    public function getRepeatPeriodWeekDays()
    {
        return (array)$this->repeatPeriodWeekDays;
    }

    /**
     * @param int[] $repeat_period_week_days
     * @return void
     */
    public function setRepeatPeriodWeekDays($repeat_period_week_days)
    {
        $this->repeatPeriodWeekDays = $repeat_period_week_days;
    }

    /**
     * @return int [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY ]
     */
    public function getRepeatMonthBy()
    {
        if ($this->repeatMonthBy === self::MONTHLY_REPEAT_WEEKLY) {
            return self::MONTHLY_REPEAT_WEEKLY;
        }

        return self::MONTHLY_REPEAT_MONTHLY;
    }

    /**
     * @param int $repeat_month_by [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY ]
     * @return void
     */
    public function setRepeatMonthBy($repeat_month_by)
    {
        $repeat_month_by = intval($repeat_month_by, 10);
        $repeat = $repeat_month_by === self::MONTHLY_REPEAT_WEEKLY ? self::MONTHLY_REPEAT_WEEKLY : self::MONTHLY_REPEAT_MONTHLY;
        $this->repeatMonthBy = $repeat;
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
