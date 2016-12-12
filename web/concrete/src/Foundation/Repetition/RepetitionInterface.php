<?php
namespace Concrete\Core\Foundation\Repetition;

interface RepetitionInterface
{

    /**
     * Repeat constants
     */
    const REPEAT_NONE = 0;
    const REPEAT_DAILY = 1;
    const REPEAT_WEEKLY = 2;
    const REPEAT_MONTHLY = 4;

    /**
     * Monthly repeat constants
     */
    const MONTHLY_REPEAT_WEEKLY = 1;
    const MONTHLY_REPEAT_MONTHLY = 2;
    const MONTHLY_REPEAT_LAST_WEEKDAY = 3;

    /**
     * The ID of this repetition, null for unsaved
     *
     * @return int|string|null
     */
    public function getID();

    /**
     * Set the start date
     *
     * @param $start_date
     * @return void
     */
    public function setStartDate($start_date);

    /**
     * Set the end date
     *
     * @param $end_date
     * @return void
     */
    public function setEndDate($end_date);

    /**
     * Toggle whether `start_date` is all day
     *
     * @param bool $start_date_all_day
     * @return void
     */
    public function setStartDateAllDay($start_date_all_day);

    /**
     * Toggle whether `end_date` is all day
     *
     * @param bool $end_date_all_day
     * @return void
     */
    public function setEndDateAllDay($end_date_all_day);

    /**
     * @param $repeat_period
     * @return void
     */
    public function setRepeatPeriod($repeat_period);

    /**
     * @param $repeat_period_week_days
     * @return mixed
     */
    public function setRepeatPeriodWeekDays($repeat_period_week_days);

    /**
     * @param $repeat_every_num
     * @return mixed
     */
    public function setRepeatEveryNum($repeat_every_num);

    /**
     * @param int $repeat_month_by [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY ]
     * @return mixed
     */
    public function setRepeatMonthBy($repeat_month_by);

    /**
     * @param $repeat_period_end
     * @return mixed
     */
    public function setRepeatPeriodEnd($repeat_period_end);

    /**
     * @return mixed
     */
    public function getStartDate();

    /**
     * @return bool
     */
    public function isStartDateAllDay();

    /**
     * @return bool
     */
    public function isEndDateAllDay();

    /**
     * @return mixed
     */
    public function getEndDate();

    /**
     * @return mixed
     */
    public function repeats();

    /**
     * @return mixed
     */
    public function getRepeatPeriod();

    /**
     * @return mixed
     */
    public function getRepeatPeriodWeekDays();

    /**
     * @return int self::MONTHLY_REPEAT_* enum [ ::MONTHLY_REPEAT_WEEKLY | ::MONTHLY_REPEAT_MONTHLY ]
     */
    public function getRepeatMonthBy();

    /**
     * @return int
     */
    public function getRepeatEveryNum();

    /**
     * @return string
     */
    public function getRepeatPeriodEnd();

    /**
     * @return mixed
     */
    public function isActive($now = null);

    /**
     * @return mixed
     */
    public function getTextRepresentation();

    /**
     * @return bool Success or failure
     */
    public function save();

    /**
     * Get all active time slots that start within two time periods
     * @param int $start
     * @param int $end
     * @return array[]
     */
    public function activeRangesBetween($start, $end);

}
