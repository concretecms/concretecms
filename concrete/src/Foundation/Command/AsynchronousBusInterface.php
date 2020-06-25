<?php

namespace Concrete\Core\Foundation\Command;

interface AsynchronousBusInterface extends BusInterface
{
    /**
     * Get the queue handle.
     */
    public function getQueue(): string;
}
