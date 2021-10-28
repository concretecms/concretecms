<?php

namespace Concrete\Controller\Dialog\Logs\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Support\Facade\Url;

class Edit extends PresetEdit
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

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/search/logs/preset', $search->getID());
    }
}
