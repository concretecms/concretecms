<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Controller\Panel\Page\CheckIn;
use Concrete\Core\Page\Controller\DashboardPageController;

class Welcome extends DashboardPageController
{
    public function view()
    {
        $controller = new CheckIn();
        $controller->setPageObject($this->getPageObject());
        $this->setThemeViewTemplate('dialog.php');
        $this->set('approveAction', $controller->action('submit'));
    }
}
