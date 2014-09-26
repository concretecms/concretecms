<?php
namespace Concrete\Core\Page\Controller;

abstract class MarketplaceDashboardPageController extends DashboardPageController
{

    abstract public function getMarketplaceType();

    public function on_start()
    {
        parent::on_start();
        $this->setThemeViewTemplate('marketplace.php');
        $this->set('type', $this->getMarketplaceType());
    }
}
