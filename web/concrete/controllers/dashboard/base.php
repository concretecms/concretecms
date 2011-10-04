<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardBaseController extends Controller {
	
	protected $error; 
	protected $quickNavTitle = false;
	protected $quickNavLinkAttributes = array();
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}

	public function outputDashboardPaneHeader($title = false) {
		$c = Page::getCurrentPage();
		$html = '<div class="ccm-dashboard-pane-header">';
		if (!$this->quickNavTitle) {
			$quickNavTitle = $c->getCollectionName();
		}
		$html .= '<ul class="ccm-dashboard-pane-header-icons">';
		$html .= '<li><a href="javascript:void(0)" id="ccm-add-to-quick-nav" ccm-quick-nav-title="' . $quickNavTitle . '" onclick="ccm_addToQuickNav(' . $c->getCollectionID() . ')" class="ccm-icon-favorite">' . t('Add to Favorites') . '</a></li>';
		$html .= '<li><a href="javascript:void(0)" onclick="ccm_closeDashboardPane()" class="ccm-icon-close">' . t('Close') . '</a></li>';
		$html .= '</ul>';
		if (!$title) {
			$title = $c->getCollectionName();
		}
		$html .= '<h3>' . $title . '</h3>';
		$html .= '</div>';
		return $html;
	}
}