<?php

namespace Concrete\Core\Foundation\Command;

class SynchronousBus extends AbstractSynchronousBus
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Command\BusInterface::getHandle()
     */
    public static function getHandle(): string
    {
        return 'core_sync';
    }
}
