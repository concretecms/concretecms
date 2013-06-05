<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Displays a list of links from an RSS feed.
 *
 * @package Blocks
 * @subpackage RSS Displayer
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_RssDisplayer extends BlockController {
		
		protected $btTable = 'btRssDisplay';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "330";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btWrapperClass = 'ccm-ui';
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 3600;
		
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
		
		public function view(){ 
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
		
		public function save($data) { 
			$args['url'] = isset($data['url']) ? $data['url'] : '';
			$args['dateFormat'] = $data['dateFormat'];
			$args['itemsToDisplay'] = (intval($data['itemsToDisplay'])>0) ? intval($data['itemsToDisplay']) : 5 ;
			$args['showSummary'] = ($data['showSummary']==1) ? 1 : 0;
			$args['launchInNewWindow'] = ($data['launchInNewWindow']==1) ? 1 : 0;
			$args['title'] = isset($data['title']) ? $data['title'] : '';
			parent::save($args);
		}

		
		public function getSearchableContent() {
			$fp = Loader::helper("feed");			
			$feed = $fp->load($this->url); 
			$feed->set_item_limit( intval($this->itemsToDisplay) );
			$feed->init();
			$feed->handle_content_type();
			$posts = $feed->get_items();
			$searchContent='';
			foreach($posts as $item){
			    $searchContent.=$item->get_title().' '.strip_tags($item->get_description()).' ';
            }
        	return $searchContent;
		}
		
	}