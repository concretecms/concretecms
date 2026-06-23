<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use URL;
use Permissions;

class Edit extends PresetEdit
{

    public function getEditSearchPresetAction()
    {
        $action = parent::getEditSearchPresetAction();
        $url = \League\Url\Url::createFromUrl($action);
        $entity = $this->getEntity();
        $url->getQuery()->modify(['exEntityID' => $entity ? $entity->getID() : null]);
        return (string) $url;
    }

    protected function getEntity()
    {
        $searchPreset = $this->getSearchPreset();
        if ($searchPreset instanceof SavedExpressSearch) {
            $entity = $searchPreset->getEntity();
            $entityID = $entity ? (int) $entity->getID() : 0;
            $requestedEntityID = (int) $this->request->query->get('exEntityID');
            if ($requestedEntityID !== 0 && $requestedEntityID !== $entityID) {
                return null;
            }
            if ($entityID !== 0) {
                $this->exEntityID = $entityID;
            }

            return $entity;
        }

        return null;
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
        return (string) URL::to('/ccm/system/search/express/preset', $search->getEntity()->getID(), $search->getID());
    }

    protected function getSearchPreset()
    {
        $em = $this->app->make(EntityManager::class);
        if (!is_object($em)) {
            return null;
        }

        $presetID = $this->request->query->get('presetID', $this->request->request->get('presetID'));
        $presetID = is_scalar($presetID) ? (int) $presetID : 0;
        if ($presetID === 0) {
            return null;
        }

        return $em->find(SavedExpressSearch::class, $presetID);
    }
}
