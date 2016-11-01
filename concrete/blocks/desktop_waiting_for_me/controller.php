<?php
namespace Concrete\Block\DesktopWaitingForMe;

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Concrete\Core\Workflow\Progress\Category;
use Core;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;

    public function getBlockTypeDescription()
    {
        return t("Displays workflow actions waiting for you.");
    }

    public function getBlockTypeName()
    {
        return t("Waiting for Me");
    }

    public function view()
    {
        $this->requireAsset('core/notification');
        $u = new \User();
        $entityManager = $this->app->make('Doctrine\ORM\EntityManager');
        $r = $entityManager->getRepository('Concrete\Core\Entity\Notification\NotificationAlert');
        $alerts = $r->findMyAlerts($u);
        $this->set('items', $alerts);
        $this->set('token', $this->app->make('token'));

    }

}
