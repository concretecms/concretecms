<?php

namespace Concrete\Core\Command\Process\Command;

interface ProcessMessageInterface
{

    const EXIT_CODE_SUCCESS = 0;
    const EXIT_CODE_FAILURE = 1;

    /**
     * Returns the UUID of the process this message belongs to.
     *
     * @return string
     */
    public function getProcess(): string;


}
