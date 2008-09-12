<?php 
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
	
	class FlashContentBlockController extends BlockController {

		protected $btDescription = "Embeds Flash (swf) files, including flash detection.";
		protected $btName = "Flash Content";
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 240;
		protected $btTable = 'btFlashContent';

		function getFileID() {return $this->fID;}
		function getFileObject() {
			return LibraryFileBlockController::getFile($this->fID);
		}		
		function getLinkText() {return $this->fileLinkText;}
		
		function delete() {
			LibraryFileBlockController::delete($this->fID);
			parent::delete();
		}
		
	}
?>