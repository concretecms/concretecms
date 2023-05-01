<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics\Menus;

use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Menu\Command\DeleteMenuCommand;
use Concrete\Core\Menu\Command\UpdateMenuCommand;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Tree;

class Details extends DashboardPageController
{

    public function view($menuId = null)
    {
        $menu = $this->entityManager->find(Menu::class, $menuId);
        $this->set('menu', $menu);
        if ($menu->getTreeID()) {
            $this->set("tree", Tree::getByID($menu->getTreeID()));
        }
    }

    public function edit($menuId = null)
    {
        $menu = $this->entityManager->find(Menu::class, $menuId);
        $this->set('menu', $menu);
        $this->render('/dashboard/system/basics/menus/edit');
    }

    public function save($menuId = null)
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $menu = $this->entityManager->find(Menu::class, $menuId);
        if (!$menu) {
            $this->error->add(t('Invalid menu.'));
        }
        if (!$this->error->has()) {
            $command = new UpdateMenuCommand($menu->getId());
            $command->setName($this->request->request->get('name'));
            $this->app->executeCommand($command);
            $this->flash('success', t('Menu updated successfully.'));
            return $this->buildRedirect(['/dashboard/system/basics/menus']);
        }
        $this->view($menuId);
    }

    public function delete($menuId = null)
    {
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $menu = $this->entityManager->find(Menu::class, $menuId);
        if (!$menu) {
            $this->error->add(t('Invalid menu.'));
        }
        if (!$this->error->has()) {
            $command = new DeleteMenuCommand($menu->getId());
            $this->app->executeCommand($command);
            $this->flash('success', t('Menu deleted successfully.'));
            return $this->buildRedirect(['/dashboard/system/basics/menus']);
        }
        $this->view($menuId);
    }
}
