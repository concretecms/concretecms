<?php 
	/*
	 * Functions for embedding video in your page. Can automatically determine width and height
	 * of FLV files from standard metadata.
	 * @author Tony Trupp <tony@concrete5.org>
	 * @author Remo Laubacher <remo.laubacher@gmail.com>
	 * @author Andrew Embler <andrew@concrete5.org>
	 */
	 
	Loader::block('library_file');	
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class VideoBlockController extends BlockController {
 
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 200;
		protected $btTable = 'btVideo';
		
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
			return LibraryFileBlockController::getFile($this->fID);
		}
		
		//auto detect flash movie size
		function getFlvMetaData($flvFileName) {
			$fp = fopen($flvFileName,'r');
			fseek($fp,27);
			$onMetaData = fread($fp,10);
			
			//if ($onMetaData != 'onMetaData') exit('No meta data available in this file! Fix it using this tool: http://www.buraks.com/flvmdi/');
			
			fseek($fp,16,SEEK_CUR);
			$ret['duration'] = array_shift(unpack('d',strrev(fread($fp,8))));
			
			fseek($fp,8,SEEK_CUR);
			$ret['width'] = array_shift(unpack('d',strrev(fread($fp,8))));
			
			fseek($fp,9,SEEK_CUR);
			$ret['height'] = array_shift(unpack('d',strrev(fread($fp,8))));
			
			return $ret;
		}		
		
		function save($data) { 
			$args['fID']    = intval($data['fID']);	
			$args['width']  = (intval($data['width'])>0)  ? intval($data['width'])  : 425;
			$args['height'] = (intval($data['height'])>0) ? intval($data['height']) : 334;		
			
			$flvFile = LibraryFileBlockController::getFile($data['fID']);
			$flvFileName = DIR_FILES_UPLOADED .'/'. $flvFile->getFilename();			
			$flvMetaData = $this->getFlvMetaData($flvFileName);
			if(intval($data['width'])==0 && intval($flvMetaData['width'])>0 )
				$args['width']  = $flvMetaData['width'];
			if(intval($data['width'])==0 && intval($flvMetaData['height'])>0  ) 				
				$args['height']  = $flvMetaData['height'];			
		
			parent::save($args);
		}				
		
		function delete() {
			LibraryFileBlockController::delete($this->fID);
			parent::delete();
		}
		
	}

?>
