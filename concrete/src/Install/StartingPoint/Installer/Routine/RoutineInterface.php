<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine;

use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;

interface RoutineInterface extends HandlerAwareCommandInterface
{

    public function getClass(): string;

    public function getText(): ?string;

}
