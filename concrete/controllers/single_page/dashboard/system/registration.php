<?php

namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Registration extends DashboardPageController
{
    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }
}
