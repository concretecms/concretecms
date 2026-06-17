<?php
namespace Concrete\Core\Board\Instance\Logger;

class NullLogger implements LoggerInterface
{
    public function write($message, ?object $data = null): void
    {
        // nothing
    }
}
