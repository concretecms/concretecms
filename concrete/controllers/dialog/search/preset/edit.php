<?php
namespace Concrete\Controller\Dialog\Search\Preset;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\EditResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;

abstract class Edit extends UserInterface
{
    protected $viewPath = '/dialogs/search/preset/edit';
    protected $validationToken = 'edit_search_preset';
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

    public function edit_search_preset()
    {
        if ($this->validateAction()) {
            $app = Application::getFacadeApplication();
            $securityHelper = $app->make('helper/security');
            $presetID = $securityHelper->sanitizeInt($this->request->request->get('presetID'));
            $newPresetName = $securityHelper->sanitizeString($this->request->request->get('presetName'));
            if (!empty($presetID) && !empty($newPresetName)) {
                $searchEntity = $this->getSavedSearchEntity();
                if (is_object($searchEntity)) {
                    $searchPreset = $searchEntity->findOneById($presetID);
                    if (!is_object($searchPreset)) {
                        $this->error->add(t('Invalid search preset.'));
                    }
                    if (!$this->error->has()) {
                        $response = new EditResponse();
                        $response->setMessage(t('%s edited successfully.', $newPresetName));
                        $response->setAdditionalDataAttribute('presetID', $presetID);
                        $response->setAdditionalDataAttribute('actionURL', (string) $this->getSavedSearchBaseURL($searchPreset));
                        $searchPreset->setPresetName($newPresetName);
                        $em = $this->app->make(EntityManager::class);
                        $em->persist($searchPreset);
                        $em->flush();

                        return new JsonResponse($response);
                    }
                }
            }
        }
        $this->error->add(t('You can\'t edit this search preset.'));

        return new JsonResponse($this->error);
    }

    public function getObjectID()
    {
        return (string) $this->objectID;
    }
}
