<?php

namespace Concrete\Core\Command\Task\Response;

use Concrete\Core\Command\Task\Runner\Response\ResponseInterface as RunnerResponseInterface;

interface ResponseFactoryInterface
{

    public function createResponse(RunnerResponseInterface $response);

}
