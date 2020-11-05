<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Board\Command\BoardSlotCommand;
use Concrete\Core\Entity\User\User;

class AddDesignerSlotToBoardCommand extends BoardSlotCommand
{

    /**
     * @var string
     */
    protected $batchIdentifier;

    /**
     * @var string
     */
    protected $ruleType;

    /**
     * @var boolean
     */
    protected $isLocked = false;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var User
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @param bool $isLocked
     */
    public function setIsLocked(bool $isLocked): void
    {
        $this->isLocked = $isLocked;
    }

    /**
     * @return string
     */
    public function getRuleType(): string
    {
        return $this->ruleType;
    }

    /**
     * @param string $ruleType
     */
    public function setRuleType(string $ruleType): void
    {
        $this->ruleType = $ruleType;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getBatchIdentifier(): string
    {
        return $this->batchIdentifier;
    }

    /**
     * @param string $batchIdentifier
     */
    public function setBatchIdentifier(string $batchIdentifier): void
    {
        $this->batchIdentifier = $batchIdentifier;
    }


}
