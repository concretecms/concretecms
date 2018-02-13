<?php

namespace Concrete\Core\Application;

use Concrete\Core\Foundation\Bus\Bus;

/**
 * Trait CommandBusAwareTrait
 * A trait used with CommandBusAwareInterface
 */
trait CommandBusAwareTrait
{

    /** @var Bus */
    public $commandBus;

    /**
     * @param Bus $bus
     */
    public function setCommandBus(Bus $bus)
    {
        $this->commandBus = $bus;
    }

}
