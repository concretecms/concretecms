<?php
namespace Concrete\Controller\Dialog\Page\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
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
}
