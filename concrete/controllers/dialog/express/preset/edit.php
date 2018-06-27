<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use URL;
use Permissions;

class Edit extends PresetEdit
{
    protected function getEntity()
    {
        $entity = null;
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            $entityID = $this->request->query->get('objectID'); 
            if (empty($entityID) && !empty($this->request->request->get('objectID'))) {
                $entityID = $this->request->request->get('objectID');
            }
            $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')->findOneById($entityID);
            if (is_object($entity)) {
                $this->objectID = $entityID;
            }
        }

        return $entity;
    }

    protected function canAccess()
    {
        $entity = $this->getEntity();
        if (is_object($entity)) {
            $ep = new Permissions($entity);

            return $ep->canViewExpressEntries();
        }

        return false;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedExpressSearch');
        }

        return null;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/express/preset', $this->objectID, $search->getID());
    }
}
