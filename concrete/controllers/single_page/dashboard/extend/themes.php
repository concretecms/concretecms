<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\MarketplaceDashboardPageController;

class Themes extends MarketplaceDashboardPageController
{
    public function getMarketplaceType()
    {
        return 'themes';
    }

    public function view_detail($mpID = null)
    {
        parent::view_detail($mpID);
        $html = \Core::make('helper/html');
        $this->requireAsset('responsive-slides');
    }

    public function getMarketplaceDefaultHeading()
    {
        return t('Themes');
    }
}
