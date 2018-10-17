<?php
namespace Concrete\Controller\Dialog\Tree\Node\Category;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation as StorageLocationEntity;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends Node
{
    protected $viewPath = '/dialogs/tree/node/category/edit';
    protected $helpers = ['form', 'validation/token'];

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canEditTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $storageLocations = $this->app->make(StorageLocationFactory::class)->fetchList();
        $locations = [];
        foreach ($storageLocations as $location) {
            $locations[$location->getID()] = $location->getName();
        }
        $this->set('node', $node);
        $this->set('locations', $locations);
    }

    public function update_category_node()
    {
        $token = $this->app->make('token');
        $error = $this->app->make('error');
        $node = $this->getNode();
        if (!$token->validate('update_category_node')) {
            $error->add($token->getErrorMessage());
        }

        $title = $this->post('treeNodeCategoryName');
        if (!$title) {
            $error->add(t('Please enter a valid name'));
        }

        $fslID = $this->post('treeNodeStorageLocationID');
        if (!$fslID) {
            $error->add(t('Please select a storage location'));
        } else {
            $em = $this->app->make('database/orm')->entityManager();
            $storageLocation = $em->find(StorageLocationEntity::class, (int) $fslID);
            if (!is_object($storageLocation)) {
                $error->add(t('Please select a valid storage location'));
            }
        }

        if (!$error->has()) {
            $node->setTreeNodeName($title);
            $node->setTreeNodeStorageLocation($fslID);
            $r = $node->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
