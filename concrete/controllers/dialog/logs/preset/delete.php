<?php
namespace Concrete\Controller\Dialog\Logs\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
{
    protected function canAccess()
    {
        $taskPermission = Key::getByHandle("view_log_entries");
        if (is_object($taskPermission)) {
            return $taskPermission->validate();
        } else {
            // This is a previous concrete5 versions that don't have the new task permission installed
            $app = Application::getFacadeApplication();
            $u = $app->make(User::class);
            return $u->isRegistered();
        }
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedLogSearch');
        }

        return null;
    }
}
