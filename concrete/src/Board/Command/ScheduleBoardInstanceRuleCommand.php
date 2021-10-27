<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\Command;

/**
 * This command is used by the in-page board instance rule scheduler. At this point, the instance rule
 * has already been created, but it doesn't have a slot yet, or a start date/time/timezone. So this command
 * is responsible primarily for setting those attributes, and setting the particular slot for the rule (which
 * also puts it out of "draft" mode.)
 */
class ScheduleBoardInstanceRuleCommand extends Command
{

    /**
     * @var int
     */
    protected $boardInstanceSlotRuleID;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $startDate = '';

    /**
     * @var string
     */
    protected $endDate = '';

    /**
     * @var string
     */
    protected $startTime = '';

    /**
     * @var string
     */
    protected $endTime = '';


    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var string
     */
    protected $timezone = 0;

    /**
     * @return int
     */
    public function getBoardInstanceSlotRuleID(): int
    {
        return $this->boardInstanceSlotRuleID;
    }

    /**
     * @param int $boardInstanceSlotRuleID
     */
    public function setBoardInstanceSlotRuleID(int $boardInstanceSlotRuleID): void
    {
        $this->boardInstanceSlotRuleID = $boardInstanceSlotRuleID;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getStartTime(): string
    {
        return $this->startTime;
    }

    /**
     * @param string $startTime
     */
    public function setStartTime(string $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return string
     */
    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * @param string $endTime
     */
    public function setEndTime(string $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @param int $slot
     */
    public function setSlot(int $slot): void
    {
        $this->slot = $slot;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


}
