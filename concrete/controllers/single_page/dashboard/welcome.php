<?php

namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Welcome extends DashboardPageController
{
    public function view()
    {
        $this->setThemeViewTemplate('desktop.php');
    }
}
