<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Session\SessionValidator;

class Heartbeat extends Controller
{
    public function view()
    {
        $sessionValidator = $this->app->make(SessionValidator::class);
        if ($sessionValidator->hasActiveSession()) {
            // "Touch" the session so that it remains open
            $this->app->make('session');
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
