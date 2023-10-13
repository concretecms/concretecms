<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\User\Group\Command\Traits\ExistingGroupTrait;

class DeleteGroupCommand extends Command
{
    use ExistingGroupTrait;

    public const ONCHILDGROUPS_MOVETOROOT = 1;

    public const ONCHILDGROUPS_MOVETOPARENT = 2;

    public const ONCHILDGROUPS_ABORT = 3;

    public const ONCHILDGROUPS_DELETE = 4;

    /**
     * @var bool
     */
    private $onlyIfEmpty = false;

    /**
     * @var bool
     */
    private $onChildGroups = self::ONCHILDGROUPS_MOVETOROOT;

    /**
     * @var bool
     */
    private $extendedResults = false;

    public function __construct(int $groupId)
    {
        $this->setGroupID($groupId);
    }

    public function isOnlyIfEmpty(): bool
    {
        return $this->onlyIfEmpty;
    }

    /**
     * @return $this
     */
    public function setOnlyIfEmpty(bool $value): object
    {
        $this->onlyIfEmpty = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setExtendedResults(bool $value): object
    {
        $this->extendedResults = $value;

        return $this;
    }

    public function isExtendedResults(): bool
    {
        return $this->extendedResults;
    }

    /**
     * @return $this
     *
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOROOT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_ABORT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_DELETE
     */
    public function setOnChildGroups(int $value): object
    {
        $this->onChildGroups = $value;

        return $this;
    }

    /**
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOROOT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_ABORT
     * @see \Concrete\Core\User\Group\Command\DeleteGroupCommand::ONCHILDGROUPS_DELETE
     */
    public function getOnChildGroups(): int
    {
        return $this->onChildGroups;
    }
}
