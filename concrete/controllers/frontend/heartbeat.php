<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\User\User;

class Heartbeat extends Controller
{
    public function view()
    {
        $sessionValidator = $this->app->make(SessionValidator::class);
        if ($sessionValidator->hasActiveSession()) {
            // This also "touches" the session so that it remains open
            $user = $this->app->make(User::class);
            if ($user->isRegistered()) {
                $user->updateOnlineCheck();
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
