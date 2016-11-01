<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Controller\Element\Search\Pages\Header;

class Search extends DashboardSitePageController
{
    public $helpers = array('form');

    public function view()
    {
        $header = new Header();
        $this->set('headerMenu', $header);
        $this->requireAsset('core/sitemap');

        $search = $this->app->make('Concrete\Controller\Search\Pages');
        $result = $search->getCurrentSearchObject();

        if (is_object($result)) {
            $this->set('result', $result);
            $result = json_encode($result->getJSONObject());
            $this->addFooterItem(
                "<script type=\"text/javascript\">$(function() { $('#ccm-dashboard-content').concretePageAjaxSearch({result: " . $result . "}); });</script>"
            );
        }
    }
}
