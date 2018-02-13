<?php
namespace Concrete\Core\Application;

use Concrete\Core\Foundation\Bus\Bus;

/**
 * Interface CommandBusAwareInterface
 * This interface declares awareness of the concrete5 command bus.
 *
 * \@package Concrete\Core\Application
 */
interface CommandBusAwareInterface
{
    /**
     * @param Bus $application
     */
    public function setCommandBus(Bus $bus);
}
