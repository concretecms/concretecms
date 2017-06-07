<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\Tree\Node\Node;
use URL;

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
        $validate = true;
        if (is_object($destNode)) {
            $dp = new \Permissions($destNode);
            if (!$dp->canAddTreeSubNode()) {
                $validate = false;
            }
        } else {
            $validate = false;
        }

        $sourceNode = $this->file->getFileNodeObject();

        if (is_object($sourceNode)) {
            $dp = new \Permissions($sourceNode);
            if (!$dp->canEditTreeNode()) {
                throw new \Exception(t('You are not allowed to move files to this folder.'));
                exit;
            }
        } else {
            $validate = false;
        }

        if ($this->validateAction() && $validate) {
            $sourceNode->move($destNode);
        }

        $response = new EditResponse();
        $response->setFile($this->file);
        $response->setMessage(t('File moved to folder successfully.'));
        $response->setAdditionalDataAttribute('folder', $destNode->getTreeNodeJSON());
        $response->outputJSON();
    }
}
