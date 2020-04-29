<?php

namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Optimization extends DashboardPageController
{
    public function view()
    {
        return $this->buildRedirect('/dashboard/system/optimization/cache');
    }
}
