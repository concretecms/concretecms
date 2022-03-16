<?php

namespace Concrete\Core\Page\Container\Command;

class UpdateContainerCommand extends ContainerCommand
{

    public static function getHandler(): string
    {
        return PersistContainerCommandHandler::class;
    }

}
