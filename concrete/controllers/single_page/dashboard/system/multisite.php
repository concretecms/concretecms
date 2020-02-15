<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Multisite extends DashboardPageController
{
    public function view()
    {
        $child = $this->getPageObject()->getFirstChild();
        $this->redirect($child->getCollectionPath());
    }
}
