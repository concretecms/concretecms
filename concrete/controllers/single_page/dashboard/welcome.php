<?php

namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

/**
 * @since 8.0.0
 */
class Welcome extends DashboardPageController
{
    public function view()
    {
        $this->setThemeViewTemplate('desktop.php');
    }
}
