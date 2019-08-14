<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\MarketplaceDashboardPageController;

class Addons extends MarketplaceDashboardPageController
{
    /**
     * @since 5.7.1
     */
    public function getMarketplaceType()
    {
        return 'addons';
    }

    /**
     * @since 5.7.1
     */
    public function getMarketplaceDefaultHeading()
    {
        return t('Add-Ons');
    }
}
