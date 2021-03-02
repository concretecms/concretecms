<?php
namespace Concrete\Controller\Dialog\Groups\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use URL;

class Edit extends PresetEdit
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
            return $em->getRepository('Concrete\Core\Entity\Search\SavedGroupSearch');
        }

        return null;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/groups/preset', $search->getID());
    }
}
