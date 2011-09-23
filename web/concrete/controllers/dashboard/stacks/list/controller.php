<?
defined('C5_EXECUTE') or die("Access Denied.");


class DashboardStacksListController extends Controller {

	public function on_start() {
		// This node is not meant for people to snoop
		$this->redirect('/');
		exit;
	}
		
}