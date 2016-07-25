<?php
namespace Concrete\Core\Support\Facade;

class Events extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'director';
    }

    public static function fire($eventName, $event = null)
    {
        $app = Facade::getFacadeApplication();
        $args = func_get_args();
        // legacy support
        if (!($event instanceof \Symfony\Component\EventDispatcher\Event)) {
            array_shift($args);
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('arguments', $args);
        }

        $app['director']->dispatch($eventName, $event);
    }
}
