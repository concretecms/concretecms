<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Calendar extends DashboardPageController
{
    public function view()
    {
        $child = $this->getPageObject()->getFirstChild();
        $this->redirect($child->getCollectionPath());
    }
}
