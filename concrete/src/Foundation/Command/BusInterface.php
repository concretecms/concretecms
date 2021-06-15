<?php

namespace Concrete\Core\Foundation\Command;

use League\Tactician\CommandBus;

interface BusInterface
{
    /**
     * Get the bus handle.
     */
    public static function getHandle(): string;

    /**
     * Build the command bus.
     *
     * @return \League\Tactician\CommandBus
     */
    public function build(Dispatcher $dispatcher): CommandBus;
}
