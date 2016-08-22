<?php

namespace Concrete\Core\Http;

interface DispatcherInterface
{
    /**
     * Take a request and populate the provided response.
     * Optionally this method may return new intances of Response
     * @param \Concrete\Core\Http\Request $requet
     * @return Response
     */
    public function dispatch(Request $requet);

}
