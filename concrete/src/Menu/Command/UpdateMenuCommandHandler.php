<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Entity\Navigation\Menu;
use Concrete\Core\Foundation\Command\Command;
use Doctrine\ORM\EntityManager;

class UpdateMenuCommandHandler extends Command
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(UpdateMenuCommand $command)
    {
        $menu = $this->entityManager->find(Menu::class, $command->getMenuId());
        $menu->setName($command->getName());
        $this->entityManager->persist($menu);
        $this->entityManager->flush();
        return $menu;
    }

}
