<?php
namespace Concrete\Controller\Backend\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Application\EditResponse;

class Connect extends MarketplaceItem
{

    public function view()
    {
        // we also perform the "does the user need to buy it?" query here to save some requests
        $r = new EditResponse();
        $r->setAdditionalDataAttribute('isConnected', $this->marketplace->isConnected());
        $r->setAdditionalDataAttribute('connectionError', $this->marketplace->getConnectionError());
        $r->setAdditionalDataAttribute('purchaseRequired', $this->item->purchaseRequired());
        $r->setAdditionalDataAttribute('remoteURL', $this->item->getRemoteURL());
        if (!$this->item->purchaseRequired()) {
            $this->item->enableFreeLicense();
        }
        $r->outputJSON();
    }

}
