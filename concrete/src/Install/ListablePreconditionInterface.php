<?php

namespace Concrete\Core\Install;

/**
 * Interface for the checks to be performed and shown during installation in the table/grid.
 */
interface ListablePreconditionInterface extends PreconditionInterface
{

    public function getComponent(): string;

}
