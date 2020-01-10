<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation as StorageLocationEntity;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/file_folder/add';
    protected $helpers = ['form', 'validation/token'];

    protected function getNode()
    {
        if (!isset($this->node)) {
            $filesystem = new Filesystem();
            $treeNodeID = (int) $this->request->query->get('treeNodeID');
            if (empty($treeNodeID)) {
                $treeNodeID = (int) $this->request->request->get('treeNodeID');
            }
            if ($treeNodeID) {
                $node = FileFolder::getByID($treeNodeID);
                if ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder) {
                    $this->node = $node;
                }
            }
        }

        if (!isset($this->node)) {
            $this->node = $filesystem->getRootFolder();
        }

        return $this->node;
    }

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Permissions($node);

        return $np->canAddTreeSubNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
        $storageLocations = $this->app->make(StorageLocationFactory::class)->fetchList();
        $locations = [];
        foreach ($storageLocations as $location) {
            if ($location->isDefault()) {
                $locations[$location->getID()] = t('%s (default)', h($location->getName()));
            } else {
                $locations[$location->getID()] = h($location->getName());
            }
        }
        $this->set('locations', $locations);
    }

    public function add_file_folder_node()
    {
        $token = $this->app->make('token');
        $response = new EditResponse();
        $response->setError($this->error);

        if (!$token->validate('add_file_folder_node')) {
            $this->error->add($token->getErrorMessage());
        }

        $folderName = $this->request->request->get('fileFolderName');
        if (!is_string($folderName) || trim($folderName) === '') {
            $this->error->add(t('Folder Name cannot be empty.'));
        }

        $fslID = (int) $this->request->request->get('fileFolderFileStorageLocation');
        if (!$fslID) {
            $this->error->add(t('Please select a storage location'));
        } else {
            $em = $this->app->make(EntityManagerInterface::class);
            $storageLocation = $em->find(StorageLocationEntity::class, (int) $fslID);
            if (!is_object($storageLocation)) {
                $this->error->add(t('Please select a valid storage location'));
            }
        }

        if (!$this->error->has()) {
            $filesystem = new Filesystem();
            $folder = $filesystem->addFolder($this->node, $folderName, $fslID);
            $response->setMessage(t('Folder added.'));
            $response->setAdditionalDataAttribute('folder', $folder);

            return new JsonResponse($response);
        }

        return new JsonResponse($this->error);
    }
}
