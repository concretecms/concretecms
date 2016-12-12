<?php
namespace Concrete\Controller\Dialog\File\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\StorageLocation\StorageLocation as FileStorageLocation;
use Permissions;

class Storage extends BackendInterfaceController
{
    public $helpers = array('form');
    protected $viewPath = '/dialogs/file/bulk/storage';
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
                $fp = new Permissions($f);
                if (!$fp->canAdmin()) {
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
        if ($this->validateAction()) {
            $post = $this->request->request->all();
            $fsl = FileStorageLocation::getByID($post['fslID']);
            if (is_object($fsl)) {
                $fIDs = $post['fID'];
                if (is_array($fIDs)) {
                    foreach ($fIDs as $fID) {
                        $f = File::getByID($fID);
                        if (is_object($f)) {
                            $fp = new Permissions($f);
                            if ($fp->canEditFilePermissions()) {
                                try {
                                    $f->setFileStorageLocation($fsl);
                                } catch (\Exception $e) {
                                    $json = new \Concrete\Core\Application\EditResponse();
                                    $err = new \Concrete\Core\Error\Error();
                                    $err->add($e->getMessage());
                                    $json->setError($err);
                                    $json->outputJSON();
                                }
                            }
                        }
                    }
                }
            } else {
                $json = new \Concrete\Core\Application\EditResponse();
                $err = new \Concrete\Core\Error\Error();
                $err->add(t('Please select valid file storage location.'));
                $json->setError($err);
                $json->outputJSON();
            }

            $response = new EditResponse();
            $response->setFiles($this->files);
            $response->setMessage(t('File storage locations updated successfully.'));
            $response->outputJSON();
        }
    }
}
