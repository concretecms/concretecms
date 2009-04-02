<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class BlogSearchController extends Controller{
	
		const DATE_FORMAT = 'F jS, Y \a\t h:i a';
		const PAGE_TYPE_HANDLE_BLOG = 'blog';		
		private $pages;
		
		public function __construct() {
			Loader::model('page_list');
			$nh = Loader::helper('navigation');

			$this->pages = new PageList();
			$this->pages->filterByCollectionTypeID($this->getCollectionIdForBlogPageType());
			
			$this->set('navigation', $nh);
			$this->set('date_format', $this->getDateFormat());
		}

		public function view(){
			$this->setPageList();			
		}
		
		private function getCollectionIdForBlogPageType(){
			$db = Loader::db();
			$q = "SELECT ctID 
			FROM PageTypes
			WHERE ctHandle = ?";
			$v = Array(self::PAGE_TYPE_HANDLE_BLOG);
			$rs = $db->query($q,$v);
			$row = $rs->FetchRow();
			return $row['ctID'];
		}
		
		private function setPageList() {
			$page_list = $this->pages->get();			
			$this->set('page_list', $page_list);		
		}
				
		private function getDateFormat(){
			return self::DATE_FORMAT;
		}
	}