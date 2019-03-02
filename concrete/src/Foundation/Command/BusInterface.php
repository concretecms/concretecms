<?php
namespace Concrete\Core\Foundation\Command;

use League\Tactician\CommandBus;

interface BusInterface
{

    /**
     * @return string
     */
    public static function getHandle();

    /**
     * @return CommandBus
     */
    public function build(Dispatcher $dispatcher);

}
