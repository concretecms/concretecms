<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\User\Group\Command\Traits\ExistingGroupTrait;

class DeleteGroupCommand extends Command
{
    use ExistingGroupTrait;

    /**
     * @var bool
     */
    private $onlyIfEmpty = false;

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
}
