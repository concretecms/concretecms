<?php

namespace Concrete\Core\Automation\Task\Response;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface as RunnerResponseInterface;

interface ResponseFactoryInterface
{

    public function createResponse(RunnerResponseInterface $response);

}
