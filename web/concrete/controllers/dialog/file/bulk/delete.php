<?php
namespace Concrete\Controller\Dialog\File\Bulk;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Validation\CSRF\Token;
use FilePermissions;
use \Concrete\Core\Http\ResponseAssetGroup;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use FileAttributeKey;
use Permissions;
use Loader;
use File;

class Delete extends BackendInterfaceController {

	protected $viewPath = '/dialogs/file/bulk/delete';
	protected $files = array();
    protected $canEdit = false;

	protected function canAccess() {
		$this->populateFiles();
		return $this->canEdit;
	}

	protected function populateFiles() {
        if (is_array($_REQUEST['fID'])) {
			foreach($_REQUEST['fID'] as $fID) {
				$f = File::getByID($fID);
				if (is_object($f)) {
					$this->files[] = $f;
				}
			}
		}

		if (count($this->files) > 0) {
			$this->canEdit = true;
			foreach($this->files as $f) {
				$fp = new Permissions($f);
				if (!$fp->canDeleteFile()) {
					$this->canEdit = false;
				}
			}
		} else {
			$this->canEdit = false;
		}

		return $this->canEdit;
	}

	public function view() {

		$this->populateFiles();
        $files = array();
        $fcnt = 0;
        if (is_array($_REQUEST['fID'])) {
            foreach($_REQUEST['fID'] as $fID) {
                $files[] = File::getByID($fID);
            }
        } else {
            $files[] = File::getByID($_REQUEST['fID']);
        }

        $fcnt = 0;
        foreach($files as $f) {
            $fp = new Permissions($f);
            if ($fp->canDeleteFile()) {
                $fcnt++;
            }
        }
        $this->set('fcnt', $fcnt);
        $this->set('form', Loader::helper('form'));
        $this->set('files', $files);
        $this->set('dh', \Core::make('helper/date'));
	}

	public function deleteFiles() {
        /** @var Token $token */
        $token = $this->app->make('token');

        if (!$token->validate('files/bulk_delete')) {
            throw new \Exception($token->getErrorMessage());
        }

        $fr = new FileEditResponse();
        $files = array();
        if (is_array($_POST['fID'])) {
            foreach($_POST['fID'] as $fID) {
                $f = File::getByID($fID);
                $fp = new Permissions($f);
                if ($fp->canDeleteFile()) {
                    $files[] = $f;
                    $f->delete();
                } else {
                    throw new \Exception(t('Unable to delete one or more files.'));
                }
            }
        }

        $fr->setMessage(t2('%s file deleted successfully.', '%s files deleted successfully.', count($files)));
        $fr->outputJSON();
	}

}

