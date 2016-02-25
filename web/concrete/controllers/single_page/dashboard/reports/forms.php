<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Block\ExpressForm\Controller;
use Concrete\Core\File\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Loader;
use UserInfo;
use Page;
use Concrete\Block\Form\MiniSurvey;
use Concrete\Block\Form\Statistics as FormBlockStatistics;

class Forms extends DashboardPageController
{
    protected $pageSize = 10;

    public function view()
    {
        $forms = Category::getNodeByName(Controller::FORM_RESULTS_CATEGORY_NAME);
        $forms->populateDirectChildrenOnly();
        $nodes = $forms->getChildNodes();
        $this->set('nodes', $nodes);
    }


}
