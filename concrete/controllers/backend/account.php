<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\PrivateMessage\Mailbox;
use Concrete\Core\User\User as LoggedUser;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Account extends AbstractController
{
    public function removeInboxNewMessageStatus()
    {
        $user = $this->app->make(LoggedUser::class);
        if (!$user->isRegistered()) {
            return JsonResponse::create(['error' => 'User must be register.'], JsonResponse::HTTP_FORBIDDEN);
        }
        $token = $this->app->make('token');
        if (!$token->validate('ccm_remove_pm_new_status')) {
            return JsonResponse::create(['error' => $token->getErrorMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $db = $this->app->make(Connection::class);
        $db->update('UserPrivateMessagesTo', ['msgIsNew' => 0],
            ['msgMailboxID' => Mailbox::MBTYPE_INBOX, 'uID' => $user->getUserID()]);

        return JsonResponse::create(['remove_inbox_new_message_status' => ['mailbox' => Mailbox::MBTYPE_INBOX]], JsonResponse::HTTP_OK);
    }
}
