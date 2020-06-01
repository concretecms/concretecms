<?php

namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Boards extends DashboardPageController
{
    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }
}
