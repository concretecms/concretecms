<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;
use \Concrete\Core\Page\Controller\MarketplaceDashboardPageController;
use TaskPermission;
use Marketplace;
use \Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;
use Loader;

class Addons extends MarketplaceDashboardPageController {

    public function getMarketplaceType()
    {
        return 'addons';
    }

    public function getMarketplaceDefaultHeading()
    {
        return t('Add-Ons');
    }


}