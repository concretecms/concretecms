<?php
namespace Concrete\Controller\Backend\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Legacy\TaskPermission;

class Connect extends MarketplaceItem
{

    public function view()
    {
        // we also perform the "does the user need to buy it?" query here to save some requests
        $r = new EditResponse();
        $r->setAdditionalDataAttribute('isConnected', $this->marketplace->isConnected());
        $r->setAdditionalDataAttribute('connectionError', $this->marketplace->getConnectionError());
        if (is_object($this->item)) {
            $r->setAdditionalDataAttribute('purchaseRequired', $this->item->purchaseRequired());
            $r->setAdditionalDataAttribute('remoteURL', $this->item->getRemoteURL());
            $r->setAdditionalDataAttribute('localURL', $this->item->getLocalURL());
            if (!$this->item->purchaseRequired()) {
                $this->item->enableFreeLicense();
            }
        }
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $tp = new TaskPermission();
        return $tp->canInstallPackages();
    }

}
