<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\MarketplaceDashboardPageController;

class Themes extends MarketplaceDashboardPageController
{
    /**
     * @since 5.7.1
     */
    public function getMarketplaceType()
    {
        return 'themes';
    }

    /**
     * @since 5.7.1
     */
    public function view_detail($mpID = null)
    {
        parent::view_detail($mpID);
        $html = \Core::make('helper/html');
        $this->requireAsset('responsive-slides');
    }

    /**
     * @since 5.7.1
     */
    public function getMarketplaceDefaultHeading()
    {
        return t('Themes');
    }
}
