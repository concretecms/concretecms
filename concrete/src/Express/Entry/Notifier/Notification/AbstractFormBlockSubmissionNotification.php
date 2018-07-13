<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Entry\Notifier\NotificationInterface;
use Concrete\Core\Block\BlockController;
use Exception;

abstract class AbstractFormBlockSubmissionNotification implements NotificationInterface
{

    protected $blockController;
    protected $app;

    public function __construct(Application $app, $blockController)
    {
        if($blockController instanceof BlockController){
            $this->blockController = $blockController;
            $this->app = $app;
        }else{
            throw new Exception("Invalid block controller");
        }
    }

}
