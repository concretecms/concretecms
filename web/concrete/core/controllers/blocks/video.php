<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Functions for embedding video in your page. Can automatically determine width and height of FLV files from standard metadata.
 *
 * @package Blocks
 * @subpackage Video
 * @author Tony Trupp <tony@concrete5.org>
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_Video extends BlockController {
 
		protected $btInterfaceWidth = 320;
		protected $btInterfaceHeight = 220;
		protected $btTable = 'btVideo';
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		protected $btExportFileColumns = array('fID');
		protected $btWrapperClass = 'ccm-ui';
		
		public $width  = '';
		public $height = '';
		public $fID = 0;
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Embeds uploaded video into a web page. Supports AVI, WMV, Quicktime/MPEG4 and FLV formats.");
		}
		
		public function getBlockTypeName() {
			return t("Video Player");
		}

		public function getJavaScriptStrings() {
			return array('flv-required' => t('You must select a valid FLV file.'));
		}

		function getFileID() {return $this->fID;}
		function getFileObject() {
			return File::getByID($this->fID);
		}

		function save($data) { 
			$args['fID']    = intval($data['fID']);	
			$args['width']  = (intval($data['width'])>0)  ? intval($data['width'])  : 425;
			$args['height'] = (intval($data['height'])>0) ? intval($data['height']) : 334;		
			
			parent::save($args);
		}				

		public function on_page_view() {
			$html = Loader::helper('html');
			$this->addHeaderItem($html->javascript('swfobject.js'));
		}
		
		
	}

?>
