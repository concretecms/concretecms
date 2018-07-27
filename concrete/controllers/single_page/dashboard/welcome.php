<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

class Welcome extends DashboardPageController
{
    public function view()
    {
        /** @var \Concrete\Core\Config\Repository\Repository $config */
        $config = $this->app->make('config');

        // If the welcome page is disabled, the user is redirected to "Waiting for me" page.
        // The welcome page can be disabled to prevent connections to concrete5.org.
        if ($config->get('concrete.external.disable_welcome_page', false) === true) {
            return Redirect::to('/dashboard/welcome/me');
        }

        $this->setThemeViewTemplate('desktop.php');
    }
}
