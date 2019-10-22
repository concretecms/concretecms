<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\MarketplaceDashboardPageController;

class Themes extends MarketplaceDashboardPageController
{
    public function getMarketplaceType()
    {
        return 'themes';
    }
    
    public function getMarketplaceDefaultHeading()
    {
        return t('Themes');
    }
}
