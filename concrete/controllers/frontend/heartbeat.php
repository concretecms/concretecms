<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;

class Heartbeat extends Controller
{
    public function view()
    {
        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
