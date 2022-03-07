<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Controller\Dialog\Tree\Node\Delete as NodeDelete;
use Concrete\Core\Tree\Node\Node as NodeObject;

class Delete extends NodeDelete
{

    protected $viewPath = '/dialogs/tree/node/file_folder/delete';

    protected function validateRequest(): array
    {
        list($error, $node) = parent::validateRequest();
        $node->populateChildren();
        $childCount = count($node->getChildNodes());
        if ($childCount > 0) {
            $error->add(t('This folder contains one or more files or sub-folders. You may not remove it until it is empty.'));
        }
        return [$error, $node];
    }

    protected function deleteNode(NodeObject $node)
    {
        $this->flash('success', t('File folder deleted successfully.'));
        return parent::deleteNode($node);
    }
}
