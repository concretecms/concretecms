<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Tree\Tree;
use Doctrine\ORM\EntityManager;

class DeleteMenuCommandHandler extends Command
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteMenuCommand $command)
    {
        $menu = $this->entityManager->find(Menu::class, $command->getMenuId());
        $tree = Tree::getByID($menu->getTreeID());
        $tree->delete();

        $this->entityManager->remove($menu);
        $this->entityManager->flush();
        return $menu;
    }

}
