<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Controller\Panel\Page\CheckIn;
use Concrete\Core\Page\Controller\DashboardPageController;

class Welcome extends DashboardPageController
{
    public function view()
    {
        $this->setThemeViewTemplate('desktop.php');
    }
}
