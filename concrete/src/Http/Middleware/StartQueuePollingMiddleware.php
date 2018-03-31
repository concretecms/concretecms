<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class StartQueuePollingMiddleware implements MiddlewareInterface
{

    /**
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);
        if ($response instanceof EnqueueItemsResponse) {

        }
        return $response;
    }

}
