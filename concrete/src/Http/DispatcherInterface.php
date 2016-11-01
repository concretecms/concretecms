<?php

namespace Concrete\Core\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface DispatcherInterface
{

    /**
     * Take a request and populate the provided response.
     * Optionally this method may return new intances of Response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SymfonyResponse
     */
    public function dispatch(SymfonyRequest $request);

}
