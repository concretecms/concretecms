<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
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
                $this->refreshPageEditMode($user);
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    private function refreshPageEditMode(User $loggedInUser): void
    {
        if (($cID = $this->request->query->getInt('cID')) !== 0) {
            $page = Page::getByID($cID);
            if ($page && !$page->isError() && $page->isCheckedOutByMe()) {
                $loggedInUser->refreshCollectionEdit($page);
            }
        }
    }
}
