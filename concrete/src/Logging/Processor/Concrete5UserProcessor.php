<?php

namespace Concrete\Core\Logging\Processor;

use Concrete\Core\User\User;

/**
 * A processor for adding the concrete5 user into the extra log info
 */
class Concrete5UserProcessor
{

    /**
     * @var User
     */
    protected $user;

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record)
    {
        if (!isset($this->user)) {
            $this->user = new User();
        }
        if ($this->user->isRegistered()) {
            $record['extra']['user'] = [$this->user->getUserID(), $this->user->getUserName()];
        }

        return $record;
    }

}


