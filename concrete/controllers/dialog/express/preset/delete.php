<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Permissions;

class Delete extends PresetDelete
{
    protected function getEntity()
    {
        $searchPreset = $this->getSearchPreset();
        if ($searchPreset instanceof SavedExpressSearch) {
            $entity = $searchPreset->getEntity();
            $entityID = $entity ? (int) $entity->getID() : 0;
            $requestedEntityID = $this->request->query->get('exEntityID', $this->request->request->get('exEntityID'));
            $requestedEntityID = is_scalar($requestedEntityID) ? (int) $requestedEntityID : 0;
            if ($requestedEntityID !== 0 && $requestedEntityID !== $entityID) {
                return null;
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

    public function getDeleteSearchPresetAction()
    {
        $action = parent::getDeleteSearchPresetAction();
        $url = \League\Url\Url::createFromUrl($action);
        $entity = $this->getEntity();
        $url->getQuery()->modify(['exEntityID' => $entity ? $entity->getID() : null]);
        return (string) $url;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedExpressSearch');
        }

        return null;
    }

    public function remove_search_preset()
    {
        if ($this->validateAction()) {
            $app = Application::getFacadeApplication();
            $securityHelper = $app->make('helper/security');
            $presetID = $securityHelper->sanitizeInt($this->request->request->get('presetID'));
            if (!empty($presetID)) {
                $searchEntity = $this->getSavedSearchEntity();
                if (is_object($searchEntity)) {
                    $searchPreset = $searchEntity->findOneById($presetID);
                    if (!is_object($searchPreset)) {
                        $this->error->add(t('Invalid search preset.'));
                    }
                    if (!$this->error->has()) {
                        $response = new EditResponse();
                        $response->setMessage(t('%s deleted successfully.', h($searchPreset->getPresetName())));
                        $response->setAdditionalDataAttribute('presetID', $presetID);
                        $em = $this->app->make(\Doctrine\ORM\EntityManager::class);
                        $em->remove($searchPreset);
                        $em->flush();

                        return new JsonResponse($response);
                    }
                }
            }
        }
        $this->error->add(t('You can\'t delete this search preset.'));

        return new JsonResponse($this->error);
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
