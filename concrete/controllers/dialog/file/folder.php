<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class Folder extends BackendInterfaceFileController
{
    protected $viewPath = '/dialogs/file/folder';

    protected function canAccess()
    {
        return $this->permissions->canViewFileInFileManager();
    }

    public function view()
    {

    }

    public function submit()
    {
        $destNode = Node::getByID($this->request->request->get('folderID'));
        if (is_object($destNode)) {
            $dp = new \Permissions($destNode);
            if (!$dp->canAddTreeSubNode()) {
                throw new UserMessageException(t('You are not allowed to move files to this location.'));
            }
        } else {
            throw new UserMessageException(t('You have not selected a valid folder.'));
        }

        $sourceNode = $this->file->getFileNodeObject();

        if (is_object($sourceNode)) {
            $dp = new \Permissions($sourceNode);
            if (!$dp->canEditTreeNode()) {
                throw new UserMessageException(t('You are not allowed to move this file.'));
            }
        } else {
            throw new UserMessageException(t('Invalid source file object.'));
        }

		if ($sourceNode->getTreeNodeParentID() === $destNode->getTreeNodeID()) {
			throw new UserMessageException(t('The selected destination is the same as the current folder.'));
		}

        if ($this->validateAction()) {
            $sourceNode->move($destNode);
            $response = new EditResponse();
            $response->setFile($this->file);
            $response->setMessage(t('File moved to folder successfully.'));
            $response->setAdditionalDataAttribute('folder', $destNode->getTreeNodeJSON());
			return new JsonResponse($response);
        }
    }
}
