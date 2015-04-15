<?php

namespace Concrete\Controller\SinglePage;
use \PageController;
use Loader;
use Page;
use Permissions;
use File;

class DownloadFile extends PageController {
		
	protected $force = 0;

	public function view($fID = 0, $rcID=NULL) {
		// get the block
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$file = File::getByID($fID);
			if ($file instanceof File && $file->getFileID() > 0) {

				$rcID = Loader::helper('security')->sanitizeInt($rcID);
				if ($rcID > 0) {
					$rc = Page::getByID($rcID, 'ACTIVE');
					if (is_object($rc) && !$rc->isError()) {
						$rcp = new Permissions($rc);
						if ($rcp->canViewPage()) {
							$this->set('rc', $rc);
						}
					}
				}
				$fp = new Permissions($file);
				if (!$fp->canViewFile()) {
					return false;
				}

				// if block password is blank download
				if (!$file->getPassword()) {
					if($this->force) {
						return $this->force_download($file,$rcID);
					} else {
						return $this->download($file,$rcID);
					}
				}
				// otherwise show the form
				$this->set('force',$this->force);
				$this->set('rcID',$rcID);
				$this->set('fID', $fID);
				$this->set('filename', $file->getFilename());
                $fre = $file->getFileResource();
				$this->set('filesize', $fre->getSize());
			}
		}
	}
	
	public function force($fID=0, $rcID=NULL) {
		$this->force = true;
		return $this->view($fID, $rcID);
	}

	public function view_inline($fID = 0) {
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$file = File::getByID($fID);
			$fp = new Permissions($file);
			if (!$fp->canViewFile()) {
				return false;
			}

            $fre = $file->getFileResource();
            $fsl = $file->getFileStorageLocationObject()->getFileSystemObject();
            $mimeType = $file->getMimeType();
			header("Content-type: $mimeType");
			print $file->getFileContents();
			exit;
		}
	}

	public function submit_password($fID = 0) {
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$f = File::getByID($fID);
			
			$rcID = ($this->post('rcID')?$this->post('rcID'):NULL);
			$rcID = Loader::helper('security')->sanitizeInt($rcID);

			if ($f->getPassword() == $this->post('password')) {
				if($this->post('force')) {
					return $this->force_download($f);
				} else {
					return $this->download($f);
				}
			}
			
			$this->set('error', t("Password incorrect. Please try again."));
			
			$this->set('force', ($this->post('force') ? 1 : 0));
			
			$this->view($fID, $rcID);
		}
	}

	protected function download(\Concrete\Core\File\File $file, $rcID=NULL) {
		$filename = $file->getFilename();
		$file->trackDownload($rcID);
		$ci = Loader::helper('file');
        $fsl = $file->getFileStorageLocationObject();
        $configuration = $fsl->getConfigurationObject();
        $fv = $file->getVersion();
        if ($configuration->hasPublicURL()) {
            return \Redirect::url($fv->getURL())->send();
        } else {
            return $fv->forceDownload();
        }
	}

	protected function force_download($file, $rcID=NULL) {
		$file->trackDownload($rcID);
		$ci = Loader::helper('file');
        return $file->forceDownload();
	}

}

?>
