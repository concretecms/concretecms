<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Entry\Notifier\NotificationInterface;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;

abstract class AbstractFormBlockSubmissionNotification implements NotificationInterface
{

    protected $blockController;
    protected $app;

    public function __construct(Application $app, ExpressFormBlockController $blockController)
    {
        $this->blockController = $blockController;
        $this->app = $app;
    }


}