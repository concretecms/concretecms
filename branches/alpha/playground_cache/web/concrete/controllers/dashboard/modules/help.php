<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class HelpDashboardModuleController extends Controller {
	
	const FEED = 'http://www.concrete5.org/blog/category/help/feed/';
	
	public function __construct() {
		$fp = Loader::helper("feed");
		$feed = $fp->load(HelpDashboardModuleController::FEED);
		$posts = $feed->get_items(0, 2);
		$this->set('posts', $posts);
	}
}