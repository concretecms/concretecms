<?php

namespace Concrete\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;

class Mail extends DashboardPageController
{
    protected $sendUndefinedTasksToView = false;

    public function view()
    {
        return $this->buildRedirectToFirstAccessibleChildPage();
    }
}
