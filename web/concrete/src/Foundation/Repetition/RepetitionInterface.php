<?php
namespace Concrete\Core\Foundation\Repetition;

interface RepetitionInterface
{

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
     * @param $repeat_month_by
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
     * @return mixed
     */
    public function getRepeatMonthBy();

    /**
     * @return mixed
     */
    public function getRepeatEveryNum();

    /**
     * @return mixed
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

}
