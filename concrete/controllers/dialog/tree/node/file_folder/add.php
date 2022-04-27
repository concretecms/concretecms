<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation as StorageLocationEntity;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Support\Facade\Url;
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
        $selectedLocationID = null;
        if ($node instanceof FileFolder) {
            $selectedLocation = $node->getTreeNodeStorageLocationObject();
            if ($selectedLocation) {
                $selectedLocationID = $selectedLocation->getID();
            }
        }
        foreach ($storageLocations as $location) {
            if ($location->isDefault()) {
                $locations[$location->getID()] = t('%s (default)', h($location->getName()));
            } else {
                $locations[$location->getID()] = h($location->getName());
            }
        }
        $this->set('selectedLocationID', $selectedLocationID);
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
            $folder = $filesystem->addFolder($this->getNode(), $folderName, $fslID);

            // Let's store the new folder IDs in session so we can highlight them on redirect.
            $this->app->make('session')->getFlashBag()->set('file_manager.updated_nodes', [$folder->getTreeNodeID()]);

            // Redirect to the folder in the file manager, with date modified descending as our query.
            // Note, it'd be nice if it were easier to build this than simply hard coding the logic but
            // this isn't too bad.
            // Note: I tried using $this->app->to() and it didn't work (?!?) so I'm using the facade.
            $redirectURL = (string) Url::to(
                '/dashboard/files/search/', 'folder', $folder->getTreeNodeParentID()
            )->setQuery(['ccm_order_by' => 'dateModified', 'ccm_order_by_direction' => 'desc']);
            $response->setMessage(t('Folder added.'));
            $response->setAdditionalDataAttribute('folder', $folder);
            $response->setRedirectURL($redirectURL);
            return new JsonResponse($response);
        }

        return new JsonResponse($this->error);
    }
}
