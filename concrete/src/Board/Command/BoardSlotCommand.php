<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class BoardSlotCommand extends Command
{

    use BoardInstanceTrait;

    /**
     * @var int
     */
    protected $startDate = 0;

    /**
     * @var int
     */
    protected $endDate = 0;

    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var int
     */
    protected $bID;

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
     * @return int
     */
    public function getBlockID(): int
    {
        return $this->bID;
    }

    /**
     * @param int $bID
     */
    public function setBlockID(int $bID): void
    {
        $this->bID = $bID;
    }

    /**
     * @return int
     */
    public function getStartDate(): int
    {
        return $this->startDate;
    }

    /**
     * @param int $startDate
     */
    public function setStartDate(int $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return int
     */
    public function getEndDate(): int
    {
        return $this->endDate;
    }

    /**
     * @param int $endDate
     */
    public function setEndDate(int $endDate): void
    {
        $this->endDate = $endDate;
    }





}
