<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManager;
use Permissions;

class Edit extends PresetEdit
{

    public function getEditSearchPresetAction()
    {
        $action = parent::getEditSearchPresetAction();
        $url = \League\Url\Url::createFromUrl($action);
        $entity = $this->getEntity();
        $query = ['exEntityID' => $entity ? $entity->getID() : null];
        $cID = $this->request->query->getInt('cID');
        if ($cID > 0) {
            $query['cID'] = $cID;
        }
        $url->getQuery()->modify($query);
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
        // Same URL as the one built by the advanced search dialog: the presets are handled by the dashboard pages
        $pagePath = '/dashboard/express/entries';
        $cID = $this->request->query->getInt('cID');
        if ($cID > 0) {
            $page = Page::getByID($cID);
            if ($page && !$page->isError()) {
                $pagePath = $page->getCollectionPath();
            }
        }

        return (string) $this->app->make('url')->to($pagePath, 'preset', $search->getID());
    }

    protected function getSearchPreset()
    {
        $em = $this->app->make(EntityManager::class);
        if (!is_object($em)) {
            return null;
        }

        $queryPresetID = $this->request->query->get('presetID');
        $queryPresetID = is_scalar($queryPresetID) ? (int) $queryPresetID : 0;
        $requestPresetID = $this->request->request->get('presetID');
        $requestPresetID = is_scalar($requestPresetID) ? (int) $requestPresetID : 0;

        if ($queryPresetID !== 0 && $requestPresetID !== 0 && $queryPresetID !== $requestPresetID) {
            return null;
        }

        $presetID = $requestPresetID ?: $queryPresetID;
        if ($presetID === 0) {
            return null;
        }

        return $em->find(SavedExpressSearch::class, $presetID);
    }
}
