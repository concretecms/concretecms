<?
defined('C5_EXECUTE') or die("Access Denied.");


class DashboardBlocksStacksListController extends Controller {

	public function on_start() {
		// This node is not meant for people to snoop
		$this->redirect('/');
		exit;
	}
		
}