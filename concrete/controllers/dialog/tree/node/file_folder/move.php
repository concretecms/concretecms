<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Controller\Dialog\Tree\Node as NodeController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Symfony\Component\HttpFoundation\JsonResponse;

class Move extends NodeController
{
    protected $viewPath = '/dialogs/tree/node/file_folder/move';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Checker($node);
        return $np->canEditTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        if (!($node instanceof FileFolder)) {
            throw new \UserMessageException(t('You may not move this folder node.'));
        }
        $this->set('currentFolder', $node);
    }

    public function submit()
    {
        $destNode = Node::getByID($this->request->request->get('folderID'));
        if (is_object($destNode)) {
            $dp = new Checker($destNode);
            if (!$dp->canAddTreeSubNode()) {
                throw new UserMessageException(t('You are not allowed to move folders to this location.'));
            }
        } else {
            throw new UserMessageException(t('You have not selected a valid folder.'));
        }

        $sourceNode = $this->getNode();

        if (is_object($sourceNode)) {
            $dp = new Checker($sourceNode);
            if (!$dp->canEditTreeNode()) {
                throw new UserMessageException(t('You are not allowed to move this folder.'));
            }
        } else {
            throw new UserMessageException(t('Invalid source file object.'));
        }

        if ($this->validateAction()) {
            $sourceNode->move($destNode);
            $response = new EditResponse();
            $response->setMessage(t('File moved to folder successfully.'));
            $response->setAdditionalDataAttribute('folder', $destNode->getTreeNodeJSON());
            return new JsonResponse($response);
        }
    }
}
