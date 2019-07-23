<?php
namespace Concrete\Controller\Dialog\User\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
{
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');
        if (is_object($dh)) {
            return $dh->canAccessUserSearchInterface();
        }

        return false;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedUserSearch');
        }

        return null;
    }
}
