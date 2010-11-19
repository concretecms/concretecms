<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	class YoutubeBlockController extends BlockController {
		
		var $pobj;
		 
		protected $btTable = 'btYouTube';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "170";
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;

		public $title = '';
		public $videoURL = "";
		public $mode = "youtube";
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Embeds a Youtube Video in your web page.");
		}
		
		public function getBlockTypeName() {
			return t("Youtube Video");
		}
		
		function on_page_view() {
			$html = Loader::helper('html');
			$this->addHeaderItem($html->javascript('swfobject.js'));
		}
		
		public function getJavaScriptStrings() {
			return array('youtube-required' => t('Please enter a valid Youtube URL.'));
		}
		
		function __construct($obj = null) {		
			parent::__construct($obj);	
			if(!$this->title) $this->title=t("My Video");
		}
		
		function view(){ 
			$this->set('bID', $this->bID);	
			$this->set('title', $this->title);
			$this->set('videoURL', $this->videoURL);
			$this->set('mode', $this->mode);
		}
		
		function save($data) { 
			$args['title'] = isset($data['title']) ? trim($data['title']) : '';
			$args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';			
			parent::save($args);
		}
		
	}
	
?>