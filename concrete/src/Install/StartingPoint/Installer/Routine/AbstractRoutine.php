<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine;

use Concrete\Core\Foundation\Command\Traits\HandlerAwareCommandTrait;

abstract class AbstractRoutine implements RoutineInterface
{

    use HandlerAwareCommandTrait;

    public function getClass(): string
    {
        return get_class($this);
    }



}
