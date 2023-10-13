<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Command\UpdateDashboardMenuCommand;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Menu\Type\DashboardType;
use Concrete\Core\Page\Controller\DashboardPageController;

class Dashboard extends DashboardPageController
{

    protected function getAvailableMenus(): array
    {
        $dashboardMenus = [];
        $menus = $this->entityManager->getRepository(Menu::class)->findAll();
        foreach ($menus as $menu) {
            if ($menu->getTypeDriver() instanceof DashboardType) {
                $dashboardMenus[] = $menu;
            }
        }
        return $dashboardMenus;
    }

    public function view()
    {
        $dashboardMenus = ['' => t('** Choose Menu')];
        foreach ($this->getAvailableMenus() as $menu) {
            $dashboardMenus[$menu->getID()] = $menu->getName();
        }
        $fullNavigationFactory = $this->app->make(FullNavigationFactory::class);
        $dashboardMenu = $fullNavigationFactory->getMenu();
        $this->set('dashboardMenuID', $dashboardMenu ? $dashboardMenu->getId() : null);
        $this->set('dashboardMenus', $dashboardMenus);
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $command = new UpdateDashboardMenuCommand($this->request->request->get("dashboardMenuID"));
            $this->app->executeCommand($command);
            $this->flash('success', t('Dashboard options saved successfully.'));
            $this->redirect('/dashboard/system/basics/dashboard');
        }
        $this->view();
    }
}
