<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;
use \Concrete\Core\Page\Controller\MarketplaceDashboardPageController;

class Themes extends MarketplaceDashboardPageController {

    public function getMarketplaceType()
    {
        return 'themes';
    }

    public function view_detail($mpID = null)
    {
        parent::view_detail($mpID);
        $html = \Core::make('helper/html');
        $this->addFooterItem($html->javascript(ASSETS_URL . '/' . DIRNAME_BLOCKS . '/image_slider/view.js'));
        $this->addHeaderItem($html->css(ASSETS_URL . '/' . DIRNAME_BLOCKS . '/image_slider/view.css'));
    }


    public function getMarketplaceDefaultHeading()
    {
        return t('Themes');
    }



}