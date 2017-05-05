<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Notifier\NotificationInterface;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;

class FormBlockSubmissionNotification extends AbstractFormBlockSubmissionNotification
{


    public function notify(Entry $entry, $updateType)
    {
        $subject = new EntrySubject($entry);
        $type = $this->app->make('manager/notification/types')->driver('new_form_submission');
        $notifier = $type->getNotifier();
        $subscription = $type->getSubscription($subject);
        $notified = $notifier->getUsersToNotify($subscription, $subject);
        $notification = $type->createNotification($subject);
        $notifier->notify($notified, $notification);
    }

}