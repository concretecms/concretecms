<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('pile');

class DashboardScrapbookUserController extends Controller {

	public function view() {

	}
	
	public function delete(){ 
		$pc = PileContent::get($_REQUEST['pcID']);
		$p = $pc->getPile();
		if ($p->isMyPile()) {
			$pc->delete();
		}
		$this->view(); 
	}
	
}

?>