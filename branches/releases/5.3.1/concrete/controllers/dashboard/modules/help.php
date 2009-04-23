<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class HelpDashboardModuleController extends Controller {
	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/help/feed';
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(HelpDashboardModuleController::FEED);
		$feed->set_timeout(3);
		$posts = $feed->get_items(0, 2);
		$this->set('posts', $posts);
	}
}