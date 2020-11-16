<?php

namespace Concrete\Core\Command\Process\Command;

interface ProcessMessageInterface
{

    /**
     * Returns the UUID of the process this message belongs to.
     *
     * @return string
     */
    public function getProcess(): string;


}
