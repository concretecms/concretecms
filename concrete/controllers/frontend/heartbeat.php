<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Database\Connection\Connection;
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
        if (($cID = $this->request->query->getInt('cID')) <= 0) {
            return;
        }
        $page = Page::getByID($cID);
        if (!$page || $page->isError() || !$page->isCheckedOutByMe()) {
            return;
        }
        $cn = $this->app->make(Connection::class);
        $cn->executeStatement(
            'UPDATE Pages SET cCheckedOutDatetimeLastEdit = ? WHERE cID = ? AND cCheckedOutUID = ? LIMIT 1',
            [
                $this->app->make('helper/date')->getOverridableNow(),
                $cID,
                (int) $loggedInUser->getUserID(),
            ]
        );
    }
}
