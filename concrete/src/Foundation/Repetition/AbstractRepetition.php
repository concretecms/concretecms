<?php
namespace Concrete\Core\Foundation\Repetition;

use Concrete\Core\Localization\Service\Date;

/**
 * Abstract repetition class
 * This class is used to define and match against various time windows.
 *
 * \@package Concrete\Core\Foundation\Repetition
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
     * @var int self::MONTHLY_REPEAT_* enum [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY | ::MONTHLY_REPEAT_LAST_WEEKDAY ]
     */
    protected $repeatMonthBy;

    /**
     * @var int The week day number
     */
    protected $repeatMonthLastWeekday;

    /**
     * @var string Time string of the last possible time for repetition
     */
    protected $repeatPeriodEnd;

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * AbstractRepetition constructor.
     * @param $timezone string | \DateTimeZone
     */
    public function __construct($timezone = null)
    {
        if ($timezone) {
            $timezone = is_object($timezone) ? $timezone : new \DateTimeZone($timezone);
        } else {
            $timezone = \Core::make('site')->getSite()->getConfigRepository()->get('timezone'); // use the site's time zone.
            if ($timezone) {
                $timezone = new \DateTimeZone($timezone);
            } else {
                $timezone = new \DateTimeZone(date_default_timezone_get());
            }
        }
        $this->setTimezone($timezone);
    }

    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function getStartDateTimestamp()
    {
        $datetime = new \DateTime($this->getStartDate(), $this->getTimezone());
        return $datetime->getTimestamp();
    }

    public function getEndDateTimestamp()
    {
        $datetime = new \DateTime($this->getEndDate(), $this->getTimezone());
        return $datetime->getTimestamp();
    }

    /**
     * @return bool
     */
    public function isStartDateAllDay()
    {
        return (bool)$this->startDateAllDay;
    }

    /**
     * Toggle whether `start_date` is all day.
     *
     * @param bool $start_date_all_day
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
        return (bool)$this->endDateAllDay;
    }

    /**
     * Toggle whether `end_date` is all day.
     *
     * @param bool $end_date_all_day
     */
    public function setEndDateAllDay($end_date_all_day)
    {
        $this->endDateAllDay = $end_date_all_day;
    }

    /**
     * @param int|null $now The timestamp to check against
     *
     * @return bool
     */
    public function isActive($now = null)
    {
        return (bool)$this->getActiveRange($now);
    }

    /**
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        if (!$this->timezone) {
            $this->timezone = new \DateTimeZone(date_default_timezone_get());
        }
        return $this->timezone;
    }


    /**
     * @return array|null Returns a list [ start, end ] or null if miss
     */
    public function getActiveRange($now = null)
    {
        if (!$now) {
            $now = \Core::make('helper/date')->getOverridableNow(true);
        }

        $dh = \Core::make('helper/date');

        if (!$this->repeats()) {
            $start_date = $this->getStartDate();
            $end_date = $this->getEndDate();

            $start_time = null;
            $end_time = null;

            $start_date_object = $dh->toDateTime($start_date, 'app');
            $end_date_object = $dh->toDateTime($end_date, 'app');
            if (is_object($start_date_object)) {
                $start_time = $start_date_object->getTimestamp();
            }
            if (is_object($end_date_object)) {
                $end_time = $end_date_object->getTimestamp();
            }
            if (!$start_date) {
                return null;
            }
            if ($start_date && $start_time > $now) {
                return null;
            }
            if ($end_date && $end_time < $now) {
                return null;
            }

            if ($end_date) {
                return $this->rangeFromTime($start_time, $end_time);
            } else {
                return $this->rangeFromTime($start_time, PHP_INT_MAX);
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
                    $start_time = new \DateTime();
                    $start_time->setTimestamp(strtotime($startsOn));

                    $now_time = new \DateTime();
                    $now_time->setTimestamp($now);

                    $week_diff_start = new \DateTime();
                    if (($days_past_sunday = date('w', $start_time->getTimestamp())) != 0) {
                        $week_diff_start->setTimestamp(
                            strtotime("-{$days_past_sunday} days", $start_time->getTimestamp()));
                    } else {
                        $week_diff_start->setTimestamp($start_time->getTimestamp());
                    }

                    $week_diff_now = new \DateTime();
                    if (($days_past_sunday = date('w', $now_time->getTimestamp())) != 0) {
                        $week_diff_now->setTimestamp(strtotime("-{$days_past_sunday} days", $now_time->getTimestamp()));
                    } else {
                        $week_diff_now->setTimestamp($now_time->getTimestamp());
                    }

                    $diff = $week_diff_now->diff($week_diff_start);
                    $num_weeks = floor($diff->days / 7);

                    if (!$this->getRepeatEveryNum() || ($num_weeks % $this->getRepeatEveryNum()) == 0) {
                        // now we check to see if it's on the right day
                        $startDOW = date('w', $start_time->getTimestamp());
                        $endDOW = date('w', strtotime($this->getEndDate()));
                        $dow = date('w', $now_time->getTimestamp());

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
                    $now_datetime = new \DateTime();
                    $now_datetime->setTimestamp($now);

                    $start_datetime = new \DateTime($startsOn);

                    $start_datetime_time = date('H:i:s', $start_datetime->getTimestamp());
                    $normalized_now_datetime = new \DateTime(
                        date("Y-m-01 {$start_datetime_time}", $now_datetime->getTimestamp()));
                    $normalized_start_datetime = new \DateTime(date("Y-m-01 H:i:s", $start_datetime->getTimestamp()));

                    $diff = $this->dateDiffNoDST($normalized_now_datetime, $normalized_start_datetime);
                    $month_difference = $diff->m + ($diff->y * 12);

                    $checkTime = false;

                    $wotm = -1;
                    $month = date('m', $start_datetime->getTimestamp());
                    $wotm_timestamp = $start_datetime->getTimestamp();
                    do {
                        ++$wotm;
                        $wotm_timestamp = strtotime('-1 week', $wotm_timestamp);
                    } while (date('m', $wotm_timestamp) === $month);

                    if (($month_difference % $this->getRepeatEveryNum()) == 0) {
                        $repeat = $this->getRepeatMonthBy();

                        switch ($repeat) {

                            case self::MONTHLY_REPEAT_MONTHLY:
                                // that means it has to be the same day of the month. e.g. the 29th, etc..
                                if (date('d', $now) == date('d', strtotime($this->getStartDate()))) {
                                    $checkTime = true;
                                }
                                break;

                            case self::MONTHLY_REPEAT_WEEKLY:
                                $now_wotm = -1;
                                $now_month = date('m', $start_datetime->getTimestamp());
                                $wotm_timestamp = $start_datetime->getTimestamp();
                                do {
                                    ++$now_wotm;
                                    $wotm_timestamp = strtotime('-1 week', $wotm_timestamp);
                                } while (date('m', $wotm_timestamp) === $now_month);

                                if ($now_wotm === $wotm) {
                                    if (date('l', $now_datetime->getTimestamp()) === date(
                                            'l',
                                            $start_datetime->getTimestamp())
                                    ) {
                                        $checkTime = true;
                                    }
                                }

                                break;

                            case self::MONTHLY_REPEAT_LAST_WEEKDAY:
                                $weekday = $this->getDayString($this->getRepeatMonthLastWeekday());
                                $now_last_day = strtotime('Last ' . $weekday . ' of ' . date('F Y', $now));

                                // that means it has to be the same day of the month. e.g. the 29th, etc..
                                if (date('d', $now_last_day) == date('d', $now)) {
                                    $checkTime = true;
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
        return $this->getRepeatPeriod() !== self::REPEAT_NONE;
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
     * Set the start date.
     *
     * @param $start_date
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
     * Set the end date.
     *
     * @param $end_date
     */
    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    protected function rangeFromTime($start, $end = null)
    {
        if (!$end) {
            $end = $start + (strtotime($this->getEndDate()) - strtotime($this->getStartDate()));
        }

        return array($start, $end);
    }

    /**
     * @return string Time string of the last possible time for an occurrence
     */
    public function getRepeatPeriodEnd()
    {
        // Note, since this might go in as 2017-08-01 00:00:00 we need to change it to be a date
        if ($this->repeatPeriodEnd) {
            $datetime = new \DateTime($this->repeatPeriodEnd);
            return $datetime->format('Y-m-d');
        }
    }

    /**
     * @param $repeat_period_end
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
     */
    public function setRepeatPeriodWeekDays($repeat_period_week_days)
    {
        $this->repeatPeriodWeekDays = $repeat_period_week_days;
    }

    /**
     * @return int [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY | ::MONTHLY_REPEAT_LAST_WEEKDAY ]
     */
    public function getRepeatMonthBy()
    {
        return max(0, min(3, $this->repeatMonthBy));
    }

    /**
     * @param int $repeat_month_by [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY | ::MONTHLY_REPEAT_LAST_WEEKDAY ]
     */
    public function setRepeatMonthBy($repeat_month_by)
    {
        $this->repeatMonthBy = max(0, min(3, $repeat_month_by));
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
            if ($this->isStartDateAllDay()) {
                $date = $dh->formatDate($this->getStartDate());
            } else {
                $date = $dh->formatDateTime($this->getStartDate());
            }
            $text .= t('Starts %s. ', $date);
        } else {
            $text .= t('Already Started. ');
        }
        if ($this->getEndDate() != '') {
            if ($this->isEndDateAllDay()) {
                $date = $dh->formatDate($this->getEndDate());
            } else {
                $date = $dh->formatDateTime($this->getEndDate());
            }
            $text .= t('Ends %s. ', $date);
        } else {
            $text .= t('No End Date. ');
        }
        if ($this->repeats()) {
            switch ($this->getRepeatPeriod()) {
                case self::REPEAT_DAILY:
                    $text .= t('Repeats Daily until %s. ', $dh->formatDate($this->getRepeatPeriodEnd()));
                    break;
                case self::REPEAT_WEEKLY:
                    $text .= t('Repeats Weekly until %s. ', $dh->formatDate($this->getRepeatPeriodEnd()));
                    break;
                case self::REPEAT_MONTHLY:
                    $text .= t('Repeats Monthly until %s. ', $dh->formatDate($this->getRepeatPeriodEnd()));
                    break;
            }
            if ($this->getRepeatEveryNum() > 1) {
                switch ($this->getRepeatPeriod()) {
                    case self::REPEAT_DAILY:
                        $item = t('days');
                        break;
                    case self::REPEAT_WEEKLY:
                        $item = t('weeks');
                        break;
                    case self::REPEAT_MONTHLY:
                        $item = t('months');
                        break;
                }
                $text .= t('Repeats every %s %s. ', $this->getRepeatEveryNum(), $item);
            }
        }

        return $text;
    }

    /**
     * This method is deprecated, use `getRepeatEveryNum`.
     *
     * @deprecated
     */
    public function getRepeatPeriodEveryNum()
    {
        return $this->getRepeatEveryNum();
    }

    /**
     * @return int The week day number
     */
    public function getRepeatMonthLastWeekday()
    {
        return $this->repeatMonthLastWeekday;
    }

    /**
     * @param int $repeatMonthLastWeekday
     */
    public function setRepeatMonthLastWeekday($repeatMonthLastWeekday)
    {
        $this->repeatMonthLastWeekday = min(6, max(0, $repeatMonthLastWeekday));
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return array[]
     */
    public function activeRangesBetween($start, $end)
    {
        $occurrences = array();
        $dh = \Core::make('date');

        $start_date = $this->getStartDate();
        $end_date = $this->getEndDate();
        if (!$end_date) {
            $end_date = $this->getStartDate();
        }

        $repetition_start = (new \DateTime($start_date, $this->getTimezone()))->getTimestamp();
        $repetition_end = (new \DateTime($end_date, $this->getTimezone()))->getTimestamp();
        $repetition_final = null;

        if ($this->getRepeatPeriodEnd()) {
            $repetition_final = (new \DateTime($this->getRepeatPeriodEnd(), $this->getTimezone()))->getTimestamp();
        }

        $repetition_num = $this->getRepeatEveryNum();

        if ($repetition_start > $start) {
            $start = $repetition_start;
        }

        if ($repetition_final && $end > $repetition_final) {
            $end = strtotime('+1 day', $repetition_final);
        }

        if ($start > $end) {
            return $occurrences;
        }

        if (!$this->repeats()) {
            if ($repetition_start >= $start && $repetition_start <= $end) {
                $occurrences[] = array($repetition_start, $repetition_end);
            }
        } else {

            switch ($this->getRepeatPeriod()) {

                case $this::REPEAT_DAILY:
                    $today = floor($start / 86400);
                    $repetition_day = floor($repetition_start / 86400);

                    if ($repetition_day > $today) {
                        $today = $repetition_day;
                    }

                    if ($repetition_difference = ($today - $repetition_day) % $repetition_num) {
                        $today -= $repetition_difference;
                        $today += $repetition_num;
                    }

                    $day_difference = $today - $repetition_day;

                    $current_date = new \DateTime();
                    $current_date->setTimezone($this->getTimezone());
                    $current_date->setTimestamp($repetition_start);
                    $current_date->modify("+{$day_difference} days");
                    $current_date_timestamp = $current_date->getTimestamp();

                    while ($current_date_timestamp < $end) {
                        $occurrences[] = array(
                            $current_date_timestamp,
                            $current_date_timestamp + $repetition_end - $repetition_start
                        );

                        $current_date->modify("+{$repetition_num} days");
                        $current_date_timestamp = $current_date->getTimestamp();
                    }

                    break;

                case $this::REPEAT_WEEKLY:
                    $start_time = new \DateTime();
                    $start_time->setTimezone($this->getTimezone());
                    $start_time->setTimestamp($start);
                    if ($start_time->format('w') != 0) {
                        $start_time->modify('last sunday');
                    }

                    $repetition_start_time = new \DateTime();
                    $repetition_start_time->setTimezone($this->getTimezone());
                    $repetition_start_time->setTimestamp($repetition_start);

                    $diff = $start_time->diff($repetition_start_time);
                    if ($difference = (floor($diff->days / 7) % $repetition_num)) {
                        $interval = \DateInterval::createFromDateString("{$difference} weeks");
                        $start_time->add($interval);
                    }

                    $current_date = new \DateTime(
                        $start_time->format('Y-m-d') . ' ' . $repetition_start_time->format('H:i:s'),
                        $this->getTimezone()
                    );
                    $current_date_timestamp = $current_date->getTimestamp();

                    while ($current_date_timestamp < $end) {
                        foreach ($this->getRepeatPeriodWeekDays() as $day) {

                            $ts = new \DateTime();
                            $ts->setTimestamp($current_date_timestamp);
                            $ts->setTimezone($this->getTimezone());
                            $ts->modify("+{$day} days");
                            $day_of_the_week = $ts->getTimestamp();

                            if ($day_of_the_week >= $start && $day_of_the_week <= $end) {
                                $occurrences[] = array(
                                    $day_of_the_week,
                                    $day_of_the_week + $repetition_end - $repetition_start,
                                );
                            }
                        }

                        $current_date->modify("+{$repetition_num} weeks");
                        $current_date_timestamp = $current_date->getTimestamp();
                    }

                    break;

                case $this::REPEAT_MONTHLY:
                    $start_time = new \DateTime();
                    $start_time->setTimezone($this->getTimezone());
                    $start_time->setTimestamp($start);

                    $start_datetime = new \DateTime($start_time->format('Y-m-01 H:i:s'), $this->getTimezone());
                    $repetition_start_datetime = new \DateTime();
                    $repetition_start_datetime->setTimezone($this->getTimezone());
                    $repetition_start_datetime->setTimestamp($repetition_start);

                    $diff = $repetition_start_datetime->diff($start_datetime);

                    if ($remainder = (($diff->m + ($diff->y * 12)) % $repetition_num)) {
                        $start_datetime->add(\DateInterval::createFromDateString("{$remainder} months"));
                    }

                    $interval = \DateInterval::createFromDateString("{$repetition_num} months");

                    $current_datetime = new \DateTime();
                    $current_datetime->setTimezone($this->getTimezone());
                    $current_datetime->setTimestamp($start_datetime->getTimestamp());

                    switch ($this->getRepeatMonthBy()) {

                        case $this::MONTHLY_REPEAT_WEEKLY:
                            $dotw = $repetition_start_datetime->format('l');
                            $wotm = -1;
                            $month = $repetition_start_datetime->format('m');
                            $wotm_step = $repetition_start_datetime->getTimestamp();

                            do {
                                ++$wotm;
                                $wotm_step = strtotime('-1 week', $wotm_step);
                                $tmp = new \DateTime();
                                $tmp->setTimestamp($wotm_step);
                                $tmp->setTimezone($this->getTimezone());
                                $wotm_step_month = $tmp->format('m');
                            } while ($wotm_step_month === $month);

                            $last_datetime = null;
                            while ($current_datetime->getTimestamp() < $end) {

                                $occurrence_start_date = clone $current_datetime;
                                $occurrence_start_date->setTimezone($this->getTimezone());
                                if ($current_datetime->format('l') != $dotw) {
                                    $occurrence_start_date->modify("next {$dotw}");
                                }

                                $occurrence_start_date->modify("+{$wotm} weeks");

                                $occurrence_start_time = new \DateTime();
                                $occurrence_start_time->setTimestamp($repetition_start);
                                $occurrence_start_time->setTimezone($this->getTimezone());

                                $occurrence_start = new \DateTime(
                                    $occurrence_start_date->format('Y-m-d ') .
                                    $occurrence_start_time->format('H:i:s'),
                                    $this->getTimezone()
                                );

                                $occurrence_start_timestamp = $occurrence_start->getTimestamp();

                                if ($occurrence_start_timestamp >= $start && $occurrence_start_timestamp <= $end) {
                                    if ($occurrence_start->format('m') === $current_datetime->format('m')) {
                                        $occurrences[] = array(
                                            $occurrence_start_timestamp,
                                            $occurrence_start_timestamp + $repetition_end - $repetition_start,
                                        );
                                    }
                                }

                                $last_datetime = clone $current_datetime;
                                $current_datetime->add($interval);
                            }
                            break;

                        case $this::MONTHLY_REPEAT_MONTHLY:
                            $current_datetime = clone $start_datetime;
                            $dotm = $repetition_start_datetime->format('j');
                            $time = $repetition_start_datetime->format('H:i:s');

                            while ($current_datetime->getTimestamp() < $end) {

                                $occurrence_start_date_time = new \DateTime();
                                $occurrence_start_date_time->setTimezone($this->getTimezone());
                                $occurrence_start_date_time->setTimestamp($current_datetime->getTimestamp());
                                $occurrence_start = $occurrence_start_date_time->format("Y-m-{$dotm} {$time}");

                                $occurrence_start = new \DateTime($occurrence_start, $this->getTimezone());
                                $occurrence_start = $occurrence_start->getTimestamp();

                                if ($occurrence_start && $occurrence_start >= $start && $occurrence_start <= $end) {
                                    $occurrences[] = array(
                                        $occurrence_start,
                                        $occurrence_start + $repetition_end - $repetition_start,
                                    );
                                }

                                $current_datetime->add($interval);
                            }
                            break;

                        case $this::MONTHLY_REPEAT_LAST_WEEKDAY:
                            $current_datetime = clone $start_datetime;
                            $weekday = $this->getDayString($this->getRepeatMonthLastWeekday());

                            while ($current_datetime->getTimestamp() < $end) {
                                $occurrence_start = $current_datetime->getTimestamp();

                                $occurrence_start_date_time1 = new \DateTime();
                                $occurrence_start_date_time1->setTimestamp($occurrence_start);
                                $occurrence_start_date_time1->setTimezone($this->getTimezone());
                                $date1 = $occurrence_start_date_time1->format('F Y');

                                $occurrence_start_date_time2 = new \DateTime();
                                $occurrence_start_date_time2->setTimestamp($occurrence_start);
                                $occurrence_start_date_time2->setTimezone($this->getTimezone());
                                $occurrence_start_date_time2->modify('Last ' . $weekday . ' of ' . $date1);

                                $occurrence_start = $occurrence_start_date_time2->format('Y-m-d ') .
                                    $repetition_start_datetime->format('H:i:s');

                                $occurrence_start = new \DateTime($occurrence_start, $this->getTimezone());
                                $occurrence_start = $occurrence_start->getTimestamp();

                                if ($occurrence_start >= $start && $occurrence_start <= $end) {
                                    $occurrences[] = array(
                                        $occurrence_start,
                                        $occurrence_start + $repetition_end - $repetition_start,
                                    );
                                }

                                $last_datetime = clone $current_datetime;
                                $current_datetime->add($interval);
                            }
                            break;
                    }

            }
        }

        return $occurrences;
    }

    protected function getDayString($day)
    {
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

        return array_get($days, $day);
    }

    /**
     * Returns the difference between two DateTime objects without considering DST changes.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return \DateInterval
     */
    protected function dateDiffNoDST(\DateTime $from, \DateTime $to)
    {
        $fromUTC = new \DateTime($from->format('Y-m-d\TH:i:s+00:00'));
        $toUTC = new \DateTime($to->format('Y-m-d\TH:i:s+00:00'));

        return $fromUTC->diff($toUTC);
    }

    public function jsonSerialize()
    {
        $repeatPeriod = null;
        $pdRepeatPeriodDaysEvery = 0;
        $pdRepeatPeriodWeeksEvery = 0;
        $pdRepeatPeriodMonthsEvery = 0;
        if ($this->getRepeatPeriod() == self::REPEAT_DAILY) {
            $repeatPeriod = 'daily';
            $pdRepeatPeriodDaysEvery = $this->getRepeatEveryNum();
        } else if ($this->getRepeatPeriod() == self::REPEAT_WEEKLY) {
            $repeatPeriod = 'weekly';
            $pdRepeatPeriodWeeksEvery = $this->getRepeatEveryNum();
        } else if ($this->getRepeatPeriod() == self::REPEAT_MONTHLY) {
            $repeatPeriod = 'monthly';
            $pdRepeatPeriodMonthsEvery = $this->getRepeatEveryNum();
        }

        $pdRepeatPeriodMonthsRepeatBy = null;
        $rmb = $this->getRepeatMonthBy();
        if ($rmb) {
            if ($rmb === self::MONTHLY_REPEAT_MONTHLY) {
                $pdRepeatPeriodMonthsRepeatBy = 'month';
            } elseif ($rmb === self::MONTHLY_REPEAT_WEEKLY) {
                $pdRepeatPeriodMonthsRepeatBy = 'week';
            } elseif ($rmb === self::MONTHLY_REPEAT_LAST_WEEKDAY) {
                $pdRepeatPeriodMonthsRepeatBy = 'lastweekday';
            }
        }

        $pdEndRepeatDate = null;
        $pdEndRepeatDateSpecific = $this->getRepeatPeriodEnd();
        if ($pdEndRepeatDateSpecific) {
            $pdEndRepeatDate = 'date';
        }
        $repetitionID = 0;
        if ($this->getID()) {
            $repetitionID = $this->getID();
        }

        $startDateTimestamp = null;
        $endDateTimestamp = null;
        if ($this->getStartDate()) {
            $startDateTimestamp = $this->getStartDateTimestamp();
        }
        if ($this->getEndDate()) {
            $endDateTimestamp = $this->getEndDateTimestamp();
        }
        return [
            'repetitionID' => $repetitionID,
            'timezone' => $this->getTimezone(),
            'pdStartDateTimestamp' => $startDateTimestamp,
            'pdEndDateTimestamp' => $endDateTimestamp,
            'pdStartDate' => $this->getStartDate(),
            'pdEndDate' => $this->getEndDate(),
            'pdStartDateAllDay' => $this->isStartDateAllDay(),
            'pdRepeats' => $this->repeats(),
            'pdRepeatPeriod' => $repeatPeriod,
            'pdRepeatPeriodDaysEvery' => $pdRepeatPeriodDaysEvery,
            'pdRepeatPeriodWeeksEvery' => $pdRepeatPeriodWeeksEvery,
            'pdRepeatPeriodMonthsEvery' => $pdRepeatPeriodMonthsEvery,
            'pdRepeatPeriodMonthsRepeatBy' => $pdRepeatPeriodMonthsRepeatBy,
            'pdRepeatPeriodMonthsRepeatLastDay' => $this->getRepeatMonthLastWeekday(),
            'pdRepeatPeriodWeekDays' => $this->getRepeatPeriodWeekDays(),
            'pdEndRepeatDate' => $pdEndRepeatDate,
            'pdEndRepeatDateSpecific' => $pdEndRepeatDateSpecific,
        ];
    }
}
