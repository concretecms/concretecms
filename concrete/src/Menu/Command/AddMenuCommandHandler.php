<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Menu\Type\TypeInterface;
use Concrete\Core\Tree\TreeType;
use Doctrine\ORM\EntityManager;

class AddMenuCommandHandler extends Command
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    public function __invoke(AddMenuCommand $command)
    {
        $menuType = TreeType::getByHandle($command->getType()->getTreeTypeHandle());
        $menuClass = $this->app->make($menuType->getTreeTypeClass());
        $tree = $menuClass::add();

        $menu = new Menu();
        $menu->setName($command->getName());
        $menu->setTreeID($tree->getTreeID());
        $menu->setType($command->getType()->getDriverHandle());
        $this->entityManager->persist($menu);
        $this->entityManager->flush();
        return $menu;
    }

}
