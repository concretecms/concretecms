<?php
namespace Concrete\Core\Foundation\Command;

interface BusInterface
{

    /**
     * @return string
     */
    public static function getHandle();

    /**
     * Build the command bus
     *
     * @param \Concrete\Core\Foundation\Command\Dispatcher $dispatcher
     *
     * @return \League\Tactician\CommandBus
     */
    public function build(Dispatcher $dispatcher);

}
