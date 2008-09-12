<?php 

class NewsDashboardModuleController extends Controller {
	
	const FEED = 'http://www.concrete5.org/blog/category/news/feed/';
	const FEED_READ_MORE = "http://www.concrete5.org/blog/";
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(NewsDashboardModuleController::FEED);
		$posts = $feed->get_items(0,2);
		$this->set('posts', $posts);
		$this->set('feed_read_more', NewsDashboardModuleController::FEED_READ_MORE);
	}
}