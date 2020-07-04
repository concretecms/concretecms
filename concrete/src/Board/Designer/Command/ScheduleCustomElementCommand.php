<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\Item;

class ScheduleCustomElementCommand
{

    const LOCK_TYPE_LOCKED = 'L';
    const LOCK_TYPE_UNLOCKED = 'U';

    /**
     * @var CustomElement
     */
    protected $element;

    /**
     * @var Instance[]
     */
    protected $instances;

    /**
     * @var string
     */
    protected $startDate;

    /**
     * @var string|null
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var int
     */
    protected $slot;

    /**
     * L or U
     * @var string
     */
    protected $lockType;

    /**
     * @param CustomElement $element
     * @param Item[] $items
     */
    public function __construct(CustomElement $element)
    {
        $this->element = $element;
    }

    /**
     * @return CustomElement
     */
    public function getElement(): CustomElement
    {
        return $this->element;
    }

    /**
     * @return Instance[]
     */
    public function getInstances(): array
    {
        return $this->instances;
    }

    /**
     * @param Instance[] $instances
     */
    public function setInstances(array $instances): void
    {
        $this->instances = $instances;
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
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @param string|null $endDate
     */
    public function setEndDate(?string $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getLockType(): string
    {
        return $this->lockType;
    }

    /**
     * @param string $lockType
     */
    public function setLockType(string $lockType): void
    {
        $this->lockType = $lockType;
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




}
