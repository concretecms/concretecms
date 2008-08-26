<?

class HelpDashboardModuleController extends Controller {
	
	const FEED = 'http://www.concrete5.org/blog/category/help/feed/';
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(HelpDashboardModuleController::FEED);
		$feed->set_item_limit(1);
		$posts = $feed->get_items();
		$this->set('posts', $posts);
	}
}