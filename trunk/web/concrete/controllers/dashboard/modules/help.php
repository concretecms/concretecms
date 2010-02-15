<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class HelpDashboardModuleController extends Controller {
	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/help/feed';
	
	public function __construct() {
		Loader::helper('feed');
		$posts = Cache::get('dashboard_feed', 'help');
		if (!is_array($posts)) {
			$posts = array();
		}
		$this->set('posts', $posts);
	}
}