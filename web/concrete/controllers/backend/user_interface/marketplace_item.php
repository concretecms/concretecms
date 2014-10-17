<?php
namespace Concrete\Controller\Backend\UserInterface;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem;

abstract class MarketplaceItem extends UserInterface
{

    protected $marketplace;
    protected $item;

    public function __construct()
    {
        parent::__construct();
        $this->marketplace = Marketplace::getInstance();
        $this->item = RemoteItem::getByID($this->request->query->get('mpID'));
    }

    protected function canAccess()
    {
        $tp = new TaskPermission();
        return $this->marketplace->isConnected() && $tp->canInstallPackages() && is_object($this->item);
    }

}

