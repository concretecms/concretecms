<?php
namespace Concrete\Controller\Dialog\File\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\File\EditResponse as FileEditResponse;
use FileAttributeKey;
use Permissions;
use Loader;
use File;

class Properties extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/bulk/properties';
    protected $controllerActionPath = '/ccm/system/dialogs/file/bulk/properties';
    protected $files;
    protected $canAccess = false;

    protected function canAccess()
    {
        return $this->canAccess;
    }

    protected function setFiles($files)
    {
        $this->files = $files;
    }

    protected function checkPermissions($file)
    {
        $fp = new Permissions($file);
        return $fp->canEditFileProperties();
    }

    public function on_start()
    {
        parent::on_start();
        if (!isset($this->files)) {
            $this->files = array();
        }

        if (is_array($_REQUEST['fID'])) {
            foreach ($_REQUEST['fID'] as $fID) {
                $f = File::getByID($fID);
                if (is_object($f) && !$f->isError()) {
                    $this->files[] = $f;
                }
            }
        }

        if (count($this->files) > 0) {
            $this->canAccess = true;
            foreach ($this->files as $f) {
                if (!$this->checkPermissions($f)) {
                    $this->canAccess = false;
                }
            }
        } else {
            $this->canAccess = false;
        }
    }

    public function view()
    {
        $r = ResponseAssetGroup::get();
        $r->requireAsset('core/app/editable-fields');
        $form = Loader::helper('form');
        $attribs = FileAttributeKey::getList();
        $this->set('files', $this->files);
        $this->set('attributes', $attribs);
    }

    public function updateAttribute()
    {
        $fr = new FileEditResponse();
        $ak = FileAttributeKey::get($_REQUEST['name']);
        if ($this->validateAction()) {
            if ($this->canAccess) {
                foreach ($this->files as $f) {
                    $fv = $f->getVersionToModify();
                    $controller = $ak->getController();
                    $value = $controller->createAttributeValueFromRequest();
                    $fv->setAttribute($ak, $value);
                    $f->reindex();
                }

                $fr->setFiles($this->files);
                $val = $f->getAttributeValueObject($ak);
                $fr->setAdditionalDataAttribute('value',  $val->getDisplayValue());
                $fr->setMessage(t('Files updated successfully.'));
            }
        }
        $fr->outputJSON();
    }

    public function clearAttribute()
    {
        $fr = new FileEditResponse();
        $ak = FileAttributeKey::get($_REQUEST['akID']);
        if ($this->validateAction()) {
            if ($this->canAccess) {
                foreach ($this->files as $f) {
                    $fv = $f->getVersionToModify();
                    $fv->clearAttribute($ak);
                    $f->reindex();
                }
                $fr->setFiles($this->files);
                $fr->setAdditionalDataAttribute('value',  false);
                $fr->setMessage(t('Attributes cleared successfully.'));
            }
        }
        $fr->outputJSON();
    }
}
