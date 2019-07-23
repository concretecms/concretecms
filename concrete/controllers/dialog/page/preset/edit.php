<?php
namespace Concrete\Controller\Dialog\Page\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use URL;

class Edit extends PresetEdit
{
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        if (is_object($dh)) {
            return $dh->canRead();
        }

        return false;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedPageSearch');
        }

        return null;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/pages/preset', $search->getID());
    }
}
