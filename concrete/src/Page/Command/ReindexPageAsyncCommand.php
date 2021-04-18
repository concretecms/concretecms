<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Command\AsyncCommandInterface;

class ReindexPageAsyncCommand extends PageCommand implements AsyncCommandInterface
{

    public static function getHandler(): string
    {
        return ReindexPageCommandHandler::class;
    }

}
