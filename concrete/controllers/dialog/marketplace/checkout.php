<?php
namespace Concrete\Controller\Dialog\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;

/**
 * @deprecated This will be removed in version 10
 */
class Checkout extends MarketplaceItem
{
    public function view()
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }
}
