<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class NewsDashboardModuleController extends Controller {

	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/news/feed';
	const FEED_READ_MORE = "http://www.concrete5.org/community/";
	
	public function __construct() {
		Loader::helper('feed');
		$posts = Cache::get('dashboard_feed', 'news');
		if (!is_array($posts)) {
			$posts = array();
		}
		$this->set('posts', $posts);
		$this->set('feed_read_more', NewsDashboardModuleController::FEED_READ_MORE);
	}
}