<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\AsyncCommandInterface;

class RescanFileAsyncCommand extends RescanFileCommand implements AsyncCommandInterface
{

    public static function getHandler(): string
    {
        return RescanFileCommandHandler::class;
    }

}
