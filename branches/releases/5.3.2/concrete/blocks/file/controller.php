<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
	
	class FileBlockController extends BlockController {

		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 250;
		protected $btTable = 'btContentFile';

		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Link to files stored in the asset library.");
		}
		
		public function getBlockTypeName() {
			return t("File");
		}

		public function getJavaScriptStrings() {
			return array('file-required' => t('You must select a file.'));	
		}
		

		function getFileID() {return $this->fID;}
		
		function getFileObject() {
			return File::getByID($this->fID);
		}
		
		function getLinkText() {
			if ($this->fileLinkText) {
				return $this->fileLinkText;
			} else {
				$f = $this->getFileObject();
				return $f->getTitle();
			}
		}
		
		
		
	}
?>
