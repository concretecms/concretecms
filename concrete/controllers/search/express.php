<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Support\Facade\Express as ExpressFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;
use Permissions;

class Express extends Standard
{
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make('\Concrete\Controller\Dialog\Express\AdvancedSearch');
    }

    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManager::class);
        $preset = $em->find('Concrete\Core\Entity\Search\SavedExpressSearch', $presetID);

        return $preset;
    }

    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('eKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }

        return $fields;
    }

    protected function loadEntity()
    {
        if (!isset($this->entity)) {
            $entity = ExpressFacade::getObjectByID($this->request->query->get('exEntityID'));
            $this->entity = $entity;
        }
    }

    protected function canAccess()
    {
        $this->loadEntity();
        $ep = new Permissions($this->entity);

        return $ep->canViewExpressEntries();
    }

    public function expressSearchPreset($entityID, $presetID)
    {
        if ($this->canAccess()) {
            $preset = $this->getSavedSearchPreset($presetID);
            if (is_object($preset)) {
                $query = $preset->getQuery();
                if (is_object($query)) {
                    $advancedSearch = $this->getAdvancedSearchDialogController();
                    $advancedSearch->setEntityID($entityID);
                    $provider = $advancedSearch->getSearchProvider();
                    $result = $provider->getSearchResultFromQuery($query);
                    $result->setBaseURL($advancedSearch->getSavedSearchBaseURL($preset));

                    $result = $this->onAfterSearchPreset($result, $preset);

                    return new JsonResponse($result->getJSONObject());
                }
            }
        }

        $this->app->shutdown();
    }
}
