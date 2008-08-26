<?

class NewsDashboardModuleController extends Controller {
	
	const FEED = 'http://www.concrete5.org/blog/category/news/feed/';
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(NewsDashboardModuleController::FEED);
		$feed->debug = true;
		$feed->set_item_limit(1);
		$posts = $feed->get_items();
		$this->set('posts', $posts);
	}
}