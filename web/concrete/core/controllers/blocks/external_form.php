<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * External Form block controller. 
 *
 * @package Blocks
 * @subpackage External Form
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_ExternalForm extends BlockController {
		
		protected $btTable = 'btExternalForm';
		protected $btInterfaceWidth = "370";
		protected $btInterfaceHeight = "100";
		public $helpers = array('file');
		protected $btCacheBlockRecord = true;
		protected $btWrapperClass = 'ccm-ui';		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Include external forms in the filesystem and place them on pages.");
		}
		
		public function getBlockTypeName() {
			return t("External Form");
		}

		public function getJavaScriptStrings() {
			return array('form-required' => t('You must select a form.'));	
		}
		
		
		function getFilename() {return $this->filename;}
		
		function getExternalFormFilenamePath() {
			if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename)) {
				$filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename;
			} else if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename)) {
				$filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename;
			}
			return $filename;
		}

		// we use call so that any methods passed to the custom form block controller,
		// if they're not implemented here, we attempt to find them in the
		// controller for that specific custom form block
		
		protected function getControllerFile() {
			if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS . '/' . $this->filename)) {
				$filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS . '/' . $this->filename;
				$class = Object::camelcase(substr($this->filename, 0, strrpos($this->filename, '.php')));
				
			} else if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS_CORE . '/' . $this->filename)) {
				$filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS_CORE . '/' . $this->filename;
				$class = Object::camelcase(substr($this->filename, 0, strrpos($this->filename, '.php')));
			}
			
			
			if (isset($filename)) {
				require_once($filename);
				$class .= 'ExternalFormBlockController';
				$fp = new $class($this->getBlockObject());
			}
			
			if(is_object($fp)) {
				$fp->on_start();
				return $fp;
			} else {
				throw new Exception(t('Unable load external form block controller file: %s',$this->filename)); 
			}
		}
		
		public function view() {
			$this->set('controller', $this);		
		}
		
		public function __call($nm, $a) {
			$cnt = $this->getControllerFile();
			return $cnt->runTask($nm, $a);
		}

		public function add() {
			$this->set('filenames', $this->getFormList());	
		}
		public function edit() {
			$this->set('filenames', $this->getFormList());	
		}
		

		public function getFormList() {
				
			$forms = array();
			$fh = Loader::helper('file');
			
			if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL)) {
				$forms = array_merge($forms, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL, array('controllers')));
			}
			if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE)) {
				$forms = array_merge($forms, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE, array('controllers')));
			}
			
			return $forms;
		}
			
	}