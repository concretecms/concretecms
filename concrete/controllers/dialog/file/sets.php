<?php
namespace Concrete\Controller\Dialog\File;

use \Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\Set\Set;

class Sets extends BackendInterfaceFileController
{

    protected $viewPath = '/dialogs/file/sets';

    protected function canAccess()
    {
        return $this->permissions->canViewFileInFileManager();
    }

    public function view()
    {
    }

    public function submit()
    {
        $requestSets = array();
        if (is_array($this->request->request->get('fsID'))) {
            $requestSets = $this->request->request->get('fsID');
        }
        if ($this->validateAction()) {
            $sets = Set::getMySets();
            foreach($sets as $set) {
                $fsp = new \Permissions($set);
                if (in_array($set->getFileSetID(), $requestSets) && $fsp->canAddFile($this->file) && !$this->file->inFileSet($set)) {
                    // This was checked and it wasn't in the file set previously
                    $set->addFileToSet($this->file);
                }

                if ($this->file->inFileSet($set) && !in_array($set->getFileSetID(), $requestSets) && $fsp->canAddFile($this->file)) {
                    // This was not checked but it used to be in the set.
                    $set->removeFileFromSet($this->file);
                }
            }
        }

        $fsNew = $this->request->request->get('fsNew');
        $fsNewShare = $this->request->request->get('fsNewShare');

        if (is_array($fsNew)) {
            foreach($fsNew as $i => $name) {
                if ($name) {
                    $type = ($fsNewShare[$i] == 1) ? Set::TYPE_PUBLIC : Set::TYPE_PRIVATE;
                    $fs = Set::createAndGetSet($fsNew[$i], $type);
                    $fs->addFileToSet($this->file);
                }
            }
        }

        $response = new EditResponse();
        $response->setFile($this->file);
        $response->setMessage(t('File sets updated successfully.'));
        $response->outputJSON();
    }

}

