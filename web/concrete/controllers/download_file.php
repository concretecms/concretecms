<?php

defined('C5_EXECUTE') or die("Access Denied.");
class DownloadFileController extends Controller {

	/** 
	 * Automatically loads the file block class
	 */
	public function on_start() {
		Loader::block('file');
	}

	public function view($fID = 0, $rcID=NULL) {
		// get the block
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$file = File::getByID($fID);			
			if ($file instanceof File && $file->getFileID() > 0) {	
				
				$fp = new Permissions($file);
				if (!$fp->canRead()) {
					return false;
				}
				
				// if block password is blank download
				if (!$file->getPassword()) {
					return $this->download($file,$rcID);			
				}
				// otherwise show the form
				$this->set('rcID',$rcID);
				$this->set('fID', $fID);
				$this->set('filename', $file->getFilename());
				$this->set('filesize', filesize( $file->getPath() ) );
			}			
		}
	}
	
	public function view_inline($fID = 0) {
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$file = File::getByID($fID);
			$fp = new Permissions($file);
			if (!$fp->canRead()) {
				return false;
			}
			
			$mimeType = $file->getMimeType();
			$fc = Loader::helper('file');
			$contents = $fc->getContents($file->getPath());
			header("Content-type: $mimeType");
			print $contents;
			exit;
		}
	}
	
	public function submit_password($fID = 0) {
		if ($fID > 0 && Loader::helper('validation/numbers')->integer($fID)) {
			$f = File::getByID($fID);
	
			if ($f->getPassword() == $_POST['password'])
				return $this->download($f);
			
			$this->set('error', t("Password incorrect. Please try again."));
			$this->view($fID);
		}
	}
	
	private function download($file,$rcID=NULL) {
		//$mime_type = finfo_file(DIR_FILES_UPLOADED."/".$filename);
		//header('Content-type: $mime_type');
		// everything else lets just download
		$filename = $file->getFilename();
		$file->trackDownload($rcID);
		$ci = Loader::helper('file');
		if ($file->getStorageLocationID() > 0) { 
			$ci->forceDownload($file->getPath());		
		} else {
			header('Location: ' . $file->getRelativePath(true));
			exit;
		}
	}
	
}

?>
