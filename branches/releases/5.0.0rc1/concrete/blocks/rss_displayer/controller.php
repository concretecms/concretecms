<?php 
	class RssDisplayerBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "Fetch, parse and display the contents of an RSS or Atom feed.";
		protected $btName = "RSS Displayer";
		protected $btTable = 'btRssDisplay';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "170";
		
		public $itemsToDisplay = "5";
		public $showSummary = "1"; 
		public $launchInNewWindow = "1"; 
		public $title = "";		
		
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
				$this->set('errorMsg', "Oops, it looks like you've entered an invalid feed address!");
			$this->set('posts', $posts);
			$this->set('title', $this->title);
		}
		
		function save($data) { 
			$args['url'] = isset($data['url']) ? $data['url'] : '';
			$args['itemsToDisplay'] = (intval($data['itemsToDisplay'])>0) ? intval($data['itemsToDisplay']) : 5 ;
			$args['showSummary'] = ($data['showSummary']==1) ? 1 : 0;
			$args['launchInNewWindow'] = ($data['launchInNewWindow']==1) ? 1 : 0;
			$args['title'] = isset($data['title']) ? $data['title'] : '';
			parent::save($args);
		}
		
	}
	
?>