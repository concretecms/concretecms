<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\User\Group\Command\Traits\ExistingGroupTrait;

class DeleteGroupCommand implements CommandInterface
{
    use ExistingGroupTrait;
}
