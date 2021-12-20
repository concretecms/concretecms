<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Automation\TaskSet as TaskSetEntity;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class TaskSet extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Task Sets');
    }

    public function getItemName($set)
    {
        return $set->getDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManager::class);
        $repo = $em->getRepository(TaskSetEntity::class);
        return $repo->findBy(['package' => $package]);
    }

    public function removeItem($item)
    {
        if ($item instanceof TaskSetEntity) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManager::class);
            $em->remove($item);
            $em->flush();
        }
    }

}
