<?php
namespace Concrete\Controller\Dialog\File\Bulk;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\Set;

class Sets extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/file/bulk/sets';
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
        $this->requireAsset('javascript', 'jquery/tristate');
        $this->set('files', $this->files);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $post = $this->request->request->all();
            foreach($post as $key => $value) {
                if (preg_match('/fsID:/', $key)) {
                    $id = explode(':', $key);
                    $fsID = $id[1];

                    $fs = Set::getByID($fsID);
                    $fsp = new \Permissions($fs);
                    foreach($this->files as $file) {
                        if ($fsp->canAddFile($file)) {
                            switch($value) {
                                case '0':
                                    if($file->inFileSet($fs)) {
                                        $fs->removeFileFromSet($file);
                                    }
                                    break;
                                case '1':
                                    // do nothing
                                    break;
                                case '2':
                                    $fs->addFileToSet($file);
                                    break;
                            }
                        }
                    }
                }
            }

            $fsNew = $this->request->request->get('fsNew');
            $fsNewShare = $this->request->request->get('fsNewShare');

            if (is_array($fsNew)) {
                foreach($fsNew as $i => $name) {
                    if ($name) {
                        foreach($this->files as $file) {
                            $type = ($fsNewShare[$i] == 1) ? Set::TYPE_PUBLIC : Set::TYPE_PRIVATE;
                            $fs = Set::createAndGetSet($fsNew[$i], $type);
                            $fs->addFileToSet($file);
                        }
                    }
                }
            }


            $sets = array();
            foreach($this->files as $file) {
                foreach($file->getFileSets() as $set) {
                    $o = $set->getJSONObject();
                    if (!in_array($o, $sets)) {
                        $sets[] = $o;
                    }
                }
            }

            $response = new EditResponse();
            $response->setFiles($this->files);
            $response->setAdditionalDataAttribute('sets', $sets);
            $response->setMessage(t('File sets updated successfully.'));
            $response->outputJSON();

        }
    }

}

