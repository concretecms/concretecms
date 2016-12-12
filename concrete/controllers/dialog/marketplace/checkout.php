<?php
namespace Concrete\Controller\Dialog\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Package\Package;

class Checkout extends MarketplaceItem
{

    protected $viewPath = '/dialogs/marketplace/checkout';

    public function view()
    {
        $this->set('mri', $this->item);
    }

}
