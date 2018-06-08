<?php
namespace Concrete\Controller\Dialog\File\Preset;

use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Tree\Node\Type\SearchPreset as TreeNodeSearchPreset;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Delete extends PresetDelete
{
    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        }

        return false;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedFileSearch');
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
                        $node = TreeNodeSearchPreset::getNodeBySavedSearchID($presetID);
                        if (is_object($node)) {
                            $response->setAdditionalDataAttribute('treeNodeID', $node->getTreeNodeID());
                            $response->setAdditionalDataAttribute('treeJSONObject', $node->getJSONObject());
                            $node->delete();
                        }
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
