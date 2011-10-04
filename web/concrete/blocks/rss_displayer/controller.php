<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class RssDisplayerBlockController extends BlockController {
		
		protected $btTable = 'btRssDisplay';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "170";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		
		public $itemsToDisplay = "5";
		public $showSummary = "1"; 
		public $launchInNewWindow = "1"; 
		public $title = "";		
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Fetch, parse and display the contents of an RSS or Atom feed.");
		}
		
		public function getBlockTypeName() {
			return t("RSS Displayer");
		}		
		
		public function getJavaScriptStrings() {
			return array(
				'feed-address' => t('Please enter a valid feed address.'),
				'feed-num-items' => t('Please enter the number of items to display.')
			);
		}
		
		function __construct($obj = null) {		
			parent::__construct($obj);
		}
		
		function view(){ 
			$fp = Loader::helper("feed");			
			$feed = $fp->load($this->url); 
			$feed->set_item_limit( intval($this->itemsToDisplay) );
			$feed->init();
			$feed->handle_content_type();
			$posts = $feed->get_items();
			
			if( $feed->error() )
				$this->set('errorMsg', t("Oops, it looks like you've entered an invalid feed address!") );
			$this->set('posts', $posts);
			$this->set('title', $this->title);
		}
		
		function save($data) { 
			$args['url'] = isset($data['url']) ? $data['url'] : '';
			$args['dateFormat'] = $data['dateFormat'];
			$args['itemsToDisplay'] = (intval($data['itemsToDisplay'])>0) ? intval($data['itemsToDisplay']) : 5 ;
			$args['showSummary'] = ($data['showSummary']==1) ? 1 : 0;
			$args['launchInNewWindow'] = ($data['launchInNewWindow']==1) ? 1 : 0;
			$args['title'] = isset($data['title']) ? $data['title'] : '';
			parent::save($args);
		}
		
	}
	
?>