<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Menu\Command\AddMenuCommand;
use Concrete\Core\Menu\Type\Manager;
use Concrete\Core\Page\Controller\DashboardPageController;

class Menus extends DashboardPageController
{

    public function view()
    {
        $this->set('types', $this->app->make(Manager::class)->getDrivers());
        $this->set('menus', $this->entityManager->getRepository(Menu::class)->findAll());
    }

    public function add_menu()
    {
        if (!$this->token->validate('add_menu')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $type = $this->app->make(Manager::class)->driver($this->request->request->get('type'));
        if (!$this->error->has()) {
            $command = new AddMenuCommand();
            $command->setName($this->request->request->get('name'));
            $command->setType($type);
            $menu = $this->app->executeCommand($command);
            $this->flash('success', t('Menu created successfully.'));
            return $this->buildRedirect(['/dashboard/system/basics/menus/details', $menu->getId()]);
        }
    }
}
