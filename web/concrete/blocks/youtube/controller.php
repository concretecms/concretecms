<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class YoutubeBlockController extends BlockController {
		
		var $pobj;
		 
		protected $btTable = 'btYouTube';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "170";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;

		public $title = '';
		public $videoURL = "";
		public $vHeight = "344";
		public $vWidth = "425";
		public $vPlayer ='0';
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
			return array('youtube-required-channel' => t('Please enter a valid video URL (not a channel URL).'));
		}
		
		function __construct($obj = null) {		
			parent::__construct($obj);	
			if(!$this->title) $this->title=t("My Video");
		}
		
		function view(){ 
			$this->set('bID', $this->bID);	
			$this->set('title', $this->title);
			$this->set('videoURL', $this->videoURL);
			$this->set('vHeight', $this->vHeight);
			$this->set('vWidth', $this->vWidth);
			$this->set('vPlayer', $this->vPlayer);
			$this->set('mode', $this->mode);
		}
		
		function save($data) { 
			$args['title'] = isset($data['title']) ? trim($data['title']) : '';
			$args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';
			$args['vHeight'] = isset($data['vHeight']) ? trim($data['vHeight']) : '';
			$args['vWidth'] = isset($data['vWidth']) ? trim($data['vWidth']) : '';
			$args['vPlayer'] = ($data['vPlayer']==1) ? 1 : 0;
			parent::save($args);
		}
		
	}
	
?>