<?php

namespace Concrete\Core\Page\Container\Command;

class AddContainerCommand extends ContainerCommand
{

    public static function getHandler(): string
    {
        return PersistContainerCommandHandler::class;
    }

}
