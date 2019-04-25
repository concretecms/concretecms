<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\PrivateMessage\Mailbox;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;

class Account extends AbstractController
{
    public function removeInboxNewMessageStatus()
    {
        $user = new User();
        if (!$user->isRegistered()) {
            return JsonResponse::create(['error' => 'User must be register.'], JsonResponse::HTTP_FORBIDDEN);
        }

        if ($this->app->make('token')->validate('ccm_remove_pm_new_status')) {
            $db = $this->app->make(Connection::class);
            $db->update('UserPrivateMessagesTo', ['msgIsNew' => 0],
                ['msgMailboxID' => Mailbox::MBTYPE_INBOX, 'uID' => $user->getUserID()]);
            return JsonResponse::create(['remove_inbox_new_message_status' => true], JsonResponse::HTTP_OK);
        }

        return null;
    }
}
