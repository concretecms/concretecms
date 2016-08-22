<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;

interface DelegateInterface
{

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param \Concrete\Core\Http\Request $request
     * @return Response
     */
    public function next(Request $request);

}
