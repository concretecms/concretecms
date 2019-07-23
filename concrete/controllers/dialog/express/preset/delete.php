<?php
namespace Concrete\Controller\Dialog\Express\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\EditResponse;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Permissions;

class Delete extends PresetDelete
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
                        $response->setMessage(t('%s deleted successfully.', $searchPreset->getPresetName()));
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
}
