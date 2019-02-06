<?php

namespace Concrete\Core\Logging\Processor;

use Concrete\Core\User\User;

/**
 * A processor for adding the concrete5 user into the extra log info
 */
class Concrete5UserProcessor
{

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record)
    {
        $u = new User();
        if ($u->isRegistered()) {
            $record['extra']['user'] = [$u->getUserID(), $u->getUserName()];
        }

        return $record;
    }

}


