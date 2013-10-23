<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Sitemap_Full extends DashboardBaseController {

	public function view() {
		$v = View::getInstance();
		$v->requireAsset('core/sitemap');
	}
}