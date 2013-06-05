<?
defined('C5_EXECUTE') or die("Access Denied.");


class Concrete5_Controller_Dashboard_Blocks_Stacks_List extends Controller {

	public function on_start() {
		// This node is not meant for people to snoop
		$this->redirect('/');
		exit;
	}
		
}