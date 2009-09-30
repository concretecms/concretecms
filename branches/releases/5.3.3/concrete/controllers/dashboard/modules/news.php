<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class NewsDashboardModuleController extends Controller {

	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/news/feed';
	const FEED_READ_MORE = "http://www.concrete5.org/community/";
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(NewsDashboardModuleController::FEED);
		$feed->set_timeout(3);
		$posts = $feed->get_items(0,2);
		$this->set('posts', $posts);
		$this->set('feed_read_more', NewsDashboardModuleController::FEED_READ_MORE);
	}
}