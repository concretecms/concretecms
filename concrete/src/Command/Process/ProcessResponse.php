<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Entity\Command\Process;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessResponse extends JsonResponse
{

    public function __construct(Process $process)
    {
        parent::__construct($process);
    }


}
