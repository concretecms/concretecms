<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardController extends Controller {

	public function view() {
		$this->set('latest_version', Config::get('APP_VERSION_LATEST'));
	}
	
}