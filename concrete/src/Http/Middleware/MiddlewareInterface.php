<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;

interface MiddlewareInterface
{

    /**
     * Process the request and return a response
     * @param \Concrete\Core\Http\Request $request The request object
     * @param DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame);

}
