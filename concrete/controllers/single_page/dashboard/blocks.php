<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class Blocks extends DashboardPageController
{
    public function view()
    {
        $this->redirect('/dashboard/blocks/stacks');
    }
}
