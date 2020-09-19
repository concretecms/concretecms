<?php

namespace Concrete\Core\Automation\Process\Dispatcher\Response;

use Concrete\Core\Automation\Process\Response\ResponseInterface as CommandResponseInterface;

interface ResponseFactoryInterface
{

    public function createResponse(CommandResponseInterface $response);

}
