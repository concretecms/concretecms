<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\DispatcherInterface;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;

class DispatcherFrame implements FrameInterface
{

    /**
     * @var \Concrete\Core\Http\DispatcherInterface|Mock_BlockController_7fcd43c1|Mock_TrackableBlockController_a75af608
     */
    private $dispatcher;

    /**
     * DispatcherFrame constructor.
     * @param \Concrete\Core\Http\DispatcherInterface $dispatcher
     */
    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param \Concrete\Core\Http\Request $request
     * @return Response
     */
    public function next(Request $request)
    {
        return $this->dispatcher->dispatch($request);
    }

}
