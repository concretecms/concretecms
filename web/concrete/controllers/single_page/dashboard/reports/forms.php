<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Block\ExpressForm\Controller;
use Concrete\Controller\Element\Dashboard\Reports\Forms\Header;
use Concrete\Core\Page\Controller\DashboardExpressEntriesPageController;
use Concrete\Core\Tree\Node\Type\Category;

class Forms extends DashboardExpressEntriesPageController
{

    protected function getResultsTreeNodeObject()
    {
        return Category::getNodeByName(Controller::FORM_RESULTS_CATEGORY_NAME);
    }

    public function view($folder = null)
    {
        $this->renderList($folder);
    }


}
