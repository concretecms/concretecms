<?php

namespace Concrete\Core\Automation\Process\Dispatcher\Response;

use Concrete\Core\Automation\Process\Dispatcher\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class HttpResponse extends JsonResponse implements ResponseInterface
{

}
