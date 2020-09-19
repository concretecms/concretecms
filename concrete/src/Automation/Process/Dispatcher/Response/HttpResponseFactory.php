<?php

namespace Concrete\Core\Automation\Process\Dispatcher\Response;

use Concrete\Core\Automation\Process\Response\ResponseInterface as CommandResponseInterface;

class HttpResponseFactory implements ResponseFactoryInterface
{

    public function createResponse(CommandResponseInterface $response): ResponseInterface
    {
        return new HttpResponse();
    }
}
