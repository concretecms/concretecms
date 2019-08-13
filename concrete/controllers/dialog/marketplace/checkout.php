<?php
namespace Concrete\Controller\Dialog\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;

/**
 * @since 5.7.1
 */
class Checkout extends MarketplaceItem
{
    protected $viewPath = '/dialogs/marketplace/checkout';

    public function view()
    {
        $this->set('mri', $this->item);
    }
}
