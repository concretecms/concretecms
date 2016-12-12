<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\File\Set\SetList as FileSetList;
use FileSet;
use Permissions;
use PermissionKey;
use Loader;
use Exception;

class Sets extends DashboardPageController
{
    public $helpers = array('form', 'validation/token', 'concrete/ui');

    public function view()
    {
        $fsl = new FileSetList();
        if (isset($_REQUEST['fsKeywords'])) {
            $fsl->filterByKeywords($_REQUEST['fsKeywords']);
        }
        if (isset($_REQUEST['fsType'])) {
            $fsl->filterByType($_REQUEST['fsType']);
            $this->set('fsType', $_REQUEST['fsType']);
        } else {
            $fsl->filterByType(FileSet::TYPE_PUBLIC);
            $this->set('fsType', FileSet::TYPE_PUBLIC);
        }
        $fileSets = $fsl->getPage();
        $this->set('fileSets', $fileSets);
        $this->set('fsl', $fsl);
    }

    public function file_set_added()
    {
        $this->set('message', t('New file set added successfully.'));
        $this->view();
    }

    public function file_set_deleted()
    {
        $this->set('message', t('File set deleted successfully.'));
        $this->view();
    }

    public function delete($fsID, $token = '')
    {
        $fs = FileSet::getByID($fsID);

        $valt = Loader::helper('validation/token');
        if (!$valt->validate('delete_file_set', $token)) {
            throw new Exception($valt->getErrorMessage());
        }

        $fs->delete();
        $this->redirect('/dashboard/files/sets', 'file_set_deleted');
    }

    public function view_detail($fsID, $action = false)
    {
        $fs = FileSet::getByID($fsID);
        $this->set('fs', $fs);
        if ($action == 'file_set_updated') {
            $this->set('message', t('File set updated successfully.'));
        }
        $this->view();
    }

    public function file_sets_edit()
    {
        extract($this->getHelperObjects());

        $valt = Loader::helper('validation/token');
        if (!$valt->validate("file_sets_edit")) {
            $this->error->add($valt->getErrorMessage());
        }

        if (!$this->post('fsID')) {
            $this->error->add(t('Invalid ID'));
        }
        $setName = trim($this->post('file_set_name'));
        if (!$setName) {
            $this->error->add(t('Please Enter a Name'));
        }

        if (!$this->error->has()) {
            $file_set = FileSet::getByID($this->post('fsID'));


            $file_set->update($setName, $fsOverrideGlobalPermissions);
            $file_set->updateFileSetDisplayOrder($this->post('fsDisplayOrder'));

            $this->redirect("/dashboard/files/sets", 'view_detail', $this->post('fsID'), 'file_set_updated');
        } else {
            $this->view();
        }
    }
}
