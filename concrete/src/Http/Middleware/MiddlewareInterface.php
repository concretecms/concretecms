<?php

namespace Concrete\Core\Http\Middleware;


use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface
{

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame);

}
