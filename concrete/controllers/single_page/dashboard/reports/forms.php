<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Block\ExpressForm\Controller;
use Concrete\Controller\Element\Dashboard\Reports\Forms\Header;
use Concrete\Core\Page\Controller\DashboardExpressEntriesPageController;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;

class Forms extends DashboardExpressEntriesPageController
{
    protected function getResultsTreeNodeObject()
    {
        $node = ExpressEntryCategory::getNodeByName(Controller::FORM_RESULTS_CATEGORY_NAME);
        if (!$node) {
            throw new \Exception(t('Forms category cannot be found. If you have removed or renamed this element you must reinstate it.'));
        }
        return $node;
    }

    public function view($folder = null)
    {
        $this->set('headerMenu', new Header($folder));
        $this->renderList($folder);
    }
}
