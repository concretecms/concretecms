<?php

namespace Concrete\Core\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface DelegateInterface
{

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function next(Request $request);

}
