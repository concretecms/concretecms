<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Controller for a block that displays flash content on a page. 
 *
 * @package Blocks
 * @subpackage Flash Content
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_FlashContent extends BlockController {

		protected $btInterfaceWidth = 380;
		protected $btInterfaceHeight = 200;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockRecord = true;
		protected $btWrapperClass = 'ccm-ui';
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		protected $btTable = 'btFlashContent';
		protected $btExportFileColumns = array('fID');

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