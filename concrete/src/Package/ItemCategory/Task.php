<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Automation\Task as TaskEntity;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Task extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Tasks');
    }

    public function getItemName($task)
    {
        return $task->getController()->getName();
    }

    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManager::class);
        $repo = $em->getRepository(TaskEntity::class);
        return $repo->findBy(['package' => $package]);
    }

    public function removeItem($item)
    {
        if ($item instanceof TaskEntity) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManager::class);
            $em->remove($item);
            $em->flush();
        }
    }

}
