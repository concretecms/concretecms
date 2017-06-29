<?php
namespace Concrete\Controller\Dialog\File\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Tree\Node\Node;
use URL;

class Folder extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/bulk/folder';
    protected $files = array();
    protected $canEdit = false;

    public function on_start()
    {
        parent::on_start();
        $this->populateFiles();
    }

    protected function canAccess()
    {
        return $this->canEdit;
    }

    protected function populateFiles()
    {
        if (is_array($_REQUEST['fID'])) {
            foreach ($_REQUEST['fID'] as $fID) {
                $f = File::getByID($fID);
                if (is_object($f)) {
                    $this->files[] = $f;
                }
            }
        }

        if (count($this->files) > 0) {
            $this->canEdit = true;
            foreach ($this->files as $f) {
                $fp = new \Permissions($f);
                if (!$fp->canViewFileInFileManager()) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }

        return $this->canEdit;
    }

    public function view()
    {
        $this->set('files', $this->files);
    }

    public function submit()
    {
        $destNodeID = intval($this->request->request->get('folderID'));
        $destNode = Node::getByID($destNodeID);
        $validate = true;
        if (is_object($destNode)) {
            $dp = new \Permissions($destNode);
            if (!$dp->canAddTreeSubNode()) {
                $validate = false;
            }
        } else {
            $validate = false;
        }

        $sourceNodes = array();
        foreach ($this->files as $file) {
            $sourceNode = $file->getFileNodeObject();
            if (is_object($sourceNode) && $sourceNode->getTreeNodeID() !== $destNodeID) {
                $dp = new \Permissions($sourceNode);
                if ($dp->canEditTreeNode()) {
                   $sourceNodes[] = $sourceNode;
                }
            }
        }

        if ($this->validateAction() && $validate && count($sourceNodes)) {
            foreach($sourceNodes as $sourceNode) {
                $sourceNode->move($destNode);
            }
        }

        $response = new EditResponse();
        $response->setFiles($this->files);
        $response->setMessage(t('Files moved to folder successfully.'));
        $response->setAdditionalDataAttribute('folder', $destNode->getTreeNodeJSON());
        $response->outputJSON();
    }
}
