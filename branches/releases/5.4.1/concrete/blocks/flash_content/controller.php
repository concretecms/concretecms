<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
	
	class FlashContentBlockController extends BlockController {

		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 240;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;
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
			return File::getByID($this->fID);
		}		
		function getLinkText() {return $this->fileLinkText;}
		
		public function on_page_view() {
			$html = Loader::helper('html');
			$this->addHeaderItem($html->javascript('swfobject.js'));
		}
	}
?>