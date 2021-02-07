<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class UserCommand extends Command
{
    /**
     * @var int
     */
    protected $userID;

    public function __construct(int $userID)
    {
        $this->userID = $userID;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }
}
