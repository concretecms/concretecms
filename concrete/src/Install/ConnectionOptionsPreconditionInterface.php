<?php

namespace Concrete\Core\Install;

use Concrete\Core\Database\Connection\Connection;

/**
 * Interface for the checks to be performed against the connection before installing concrete5 but after the configuration has been specified.
 */
interface ConnectionOptionsPreconditionInterface extends OptionsPreconditionInterface
{
    /**
     * Set the connection to the database.
     *
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection);
}
