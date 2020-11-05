<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use URL;
use Permissions;

class Edit extends PresetEdit
{

    public function getEditSearchPresetAction()
    {
        $entityID = $this->request->query->get('exEntityID');
        $action = parent::getEditSearchPresetAction();
        $url = \League\Url\Url::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $entityID]);
        return (string) $url;
    }

    protected function getEntity()
    {
        $entity = null;
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            $entityID = $this->request->query->get('exEntityID');
            $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')->findOneById($entityID);
            if (is_object($entity)) {
                $this->exEntityID = $entityID;
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
        return (string) URL::to('/ccm/system/search/express/preset', $this->exEntityID, $search->getID());
    }
}
