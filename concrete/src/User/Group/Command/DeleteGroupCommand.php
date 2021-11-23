<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\User\Group\Command\Traits\ExistingGroupTrait;

class DeleteGroupCommand extends Command
{
    use ExistingGroupTrait;

    public function __construct(int $groupId)
    {
        $this->setGroupID($groupId);
    }
}
