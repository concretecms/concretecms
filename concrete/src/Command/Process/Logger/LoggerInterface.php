<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Command\Task\Output\OutputInterface;

interface LoggerInterface extends OutputInterface
{

    public function logExists(): bool;

    public function remove(): void;

    public function readAsArray(): array;
}
