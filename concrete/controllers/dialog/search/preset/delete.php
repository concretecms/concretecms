<?php
namespace Concrete\Controller\Dialog\Search\Preset;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\EditResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;

abstract class Delete extends UserInterface
{
    protected $viewPath = '/dialogs/search/preset/delete';
    protected $validationToken = 'remove_search_preset';
    public $objectID = null;

    public function view()
    {
        $app = Application::getFacadeApplication();
        $searchPreset = null;
        $securityHelper = $app->make('helper/security');
        $presetID = $securityHelper->sanitizeInt($this->request->query->get('presetID'));
        $searchEntity = $this->getSavedSearchEntity();
        if (!empty($presetID) && is_object($searchEntity)) {
            $searchPreset = $searchEntity->findOneById($presetID);
        }
        $this->set('searchPreset', $searchPreset);
        $this->set('form', $app->make('helper/form'));
        $this->set('token', $app->make('helper/validation/token'));
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
                        $em = $this->app->make(EntityManager::class);
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

    public function getObjectID()
    {
        return (string) $this->objectID;
    }
}
