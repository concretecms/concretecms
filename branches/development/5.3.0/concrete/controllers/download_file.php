<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
class DownloadFileController extends Controller {

	/** 
	 * Automatically loads the file block class
	 */
	public function on_start() {
		Loader::block('file');
	}

	public function view($bID = 0) {
		// get the block
		$block = $this->getBlock($bID);
		if (is_object($block)) {
			$file = $block->getFileObject();
			
			if ($file) {
				
				// if block password is blank download
				if (!$block->getPassword())
					return $this->download($file);			
			
				// otherwise show the form
				$this->set('bID', $bID);
				$this->set('filename', $file->getFilename());
				$this->set('filesize', filesize(DIR_FILES_UPLOADED."/".$file->getFilename()));
			}
			
		}

	}
	
	public function submit_password($bID = 0) {
		$block = $this->getBlock($bID);
		$file = $block->getFileObject();
	
		if ($block->getPassword() == $_POST['password'])
			return $this->download($file);
		
		$this->set('error', t("Password incorrect. Please try again."));
		$this->view($bID);
	}
	
	private function download($file) {
		//$mime_type = finfo_file(DIR_FILES_UPLOADED."/".$filename);
		//header('Content-type: $mime_type');
		// everything else lets just download
		$filename = $file->getFilename();
		$ci = Loader::helper('file');
		$ci->forceDownload($filename);
	}
	
	private function getBlock($bID) {
		$b = Block::getByID($bID);
		if (is_object($b)) {
			return $b->getInstance();
		}
	}
}

?>
