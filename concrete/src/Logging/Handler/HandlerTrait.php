<?php

namespace Concrete\Core\Logging\Handler;

use Concrete\Core\Error\UserMessageException;

trait HandlerTrait
{
    protected function shouldWrite(array $record): bool
    {
        $exception = $record['context']['exception'] ?? null;
        if ($exception instanceof UserMessageException) {
            return false;
        }

        return true;
    }
}
