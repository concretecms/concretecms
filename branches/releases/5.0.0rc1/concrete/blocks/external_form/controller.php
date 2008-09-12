<?php 

	class ExternalFormBlockController extends BlockController {
		
		var $pobj;
		protected $btDescription = "Include external forms in the filesystem and place them on pages.";
		protected $btName = "External Form";
		protected $btTable = 'btExternalForm';
		protected $btInterfaceWidth = "300";
		protected $btInterfaceHeight = "200";
		public $helpers = array('file');
		
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
		
		private function getController() {
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
				$fp = new $class($this->pobj);

				return $fp;
			}
		}
		
		public function __call($nm, $a) {
			$cnt = $this->getController();
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
				$forms = array_merge($forms, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL));
			}
			if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE)) {
				$forms = array_merge($forms, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE));
			}
			
			return $forms;
		}
			
	}
	
	
	
?>