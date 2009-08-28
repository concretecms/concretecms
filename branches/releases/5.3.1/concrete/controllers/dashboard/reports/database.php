<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardReportsDatabaseController extends Controller {
	
	public $helpers = array('form', 'html');
	public function clear($token = '') {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			DatabaseLogEntry::clear();
			$this->redirect('/dashboard/reports/database');
		} else {
			$this->redirect('/dashboard/reports/database');
		}
	}
	
	public function view($page = 0) {
		$total = DatabaseLogEntry::getTotal();
		$pageBase = View::url('/dashboard/reports/database','view');
		$paginator = Loader::helper('pagination');
		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%', 50);
		$limit=$paginator->getLIMIT();
		$entries = DatabaseLogEntry::getList($limit); 
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);
	}
	
}
?>