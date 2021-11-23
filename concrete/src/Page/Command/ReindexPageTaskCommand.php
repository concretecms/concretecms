<?php

namespace Concrete\Core\Page\Command;

class ReindexPageTaskCommand extends PageCommand
{

    public static function getHandler(): string
    {
        return ReindexPageCommandHandler::class;
    }

}
