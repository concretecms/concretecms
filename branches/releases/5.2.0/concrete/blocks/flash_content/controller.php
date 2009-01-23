<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
	
	class FlashContentBlockController extends BlockController {

		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 240;
		protected $btTable = 'btFlashContent';
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Embeds SWF files, including flash detection.");
		}
		
		public function getBlockTypeName() {
			return t("Flash Content");
		}

		public function getJavaScriptStrings() {
			return array('file-required' => t('You must select a file.'));	
		}
		
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