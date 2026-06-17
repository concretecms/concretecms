<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\AsyncCommandInterface;

class RegenerateBoardInstanceAsyncCommand extends RegenerateBoardInstanceCommand implements AsyncCommandInterface
{

    public function isDefer(): bool
    {
        return false;
    }

    public static function getHandler(): string
    {
        return RegenerateBoardInstanceCommandHandler::class;
    }
}
