<?
defined('C5_EXECUTE') or die("Access Denied.");


class Concrete5_Controller_Page_Dashboard_Blocks_Stacks_List extends DashboardController {

	public function on_start() {
		// This node is not meant for people to snoop
		$this->redirect('/');
		exit;
	}
		
}