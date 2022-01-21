<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Page\Container as ContainerEntity;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Container extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Containers');
    }

    /**
     * @param ContainerEntity $container
     */
    public function getItemName($container)
    {
        return $container->getContainerName();
    }

    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManager::class);
        $repo = $em->getRepository(ContainerEntity::class);
        return $repo->findBy(['package' => $package]);
    }

    public function removeItem($item)
    {
        if ($item instanceof ContainerEntity) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManager::class);
            $em->remove($item);
            $em->flush();
        }
    }

}
