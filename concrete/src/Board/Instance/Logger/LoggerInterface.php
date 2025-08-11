<?php
namespace Concrete\Core\Board\Instance\Logger;

interface LoggerInterface
{
    public function write($message, ?object $data = null);
}
