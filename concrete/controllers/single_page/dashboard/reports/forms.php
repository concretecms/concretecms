<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Block\ExpressForm\Controller;
use Concrete\Core\Controller\Traits\DashboardExpressEntryDetailsTrait;
use Concrete\Core\Controller\Traits\DashboardSelectableExpressEntryListTrait;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Type\ExpressEntryResults;

class Forms extends DashboardPageController
{

    use DashboardSelectableExpressEntryListTrait;
    use DashboardExpressEntryDetailsTrait;

    protected function getParentNode($folder = null)
    {
        if ($folder) {
            $node = Node::getByID($folder);
            if (!($node instanceof ExpressEntryCategory) && !($node instanceof ExpressEntryResults)) {
                throw new \Exception(t('Invalid form entry node.'));
            }
        } else {
            $node = ExpressEntryCategory::getNodeByName(Controller::FORM_RESULTS_CATEGORY_NAME);
            if (!($node instanceof ExpressEntryCategory)) {
                throw new \Exception(t('Valid form entry category cannot be found. If you have removed or renamed this element you must reinstate it.'));
            }
        }
        return $node;
    }

    public function view($folder = null)
    {
        $parent = $this->getParentNode($folder);
        $parent->populateDirectChildrenOnly();
        $this->set('nodes', $parent->getChildNodes());

        if ($folder) {
            $factory = $this->createBreadcrumbFactory();
            $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $parent));
        }

        $this->setThemeViewTemplate('full.php');
        $this->render('/dashboard/express/entries/folder', false);
    }

}
