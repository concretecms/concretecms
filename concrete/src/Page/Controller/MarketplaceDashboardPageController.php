<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Marketplace\RemoteItem;
use TaskPermission;
use Marketplace;
use Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;

/**
 * Abstract controller for extending Concrete CMS through the Dashboard.
 * 
 */

abstract class MarketplaceDashboardPageController extends DashboardPageController
{
    abstract public function getMarketplaceType();
    abstract public function getMarketplaceDefaultHeading();

    public function view_detail($mpID = null)
    {
        return $this->buildRedirect('/dashboard/extend/connect');
    }

    public function view()
    {
        return $this->buildRedirect('/dashboard/extend/connect');
    }
}
