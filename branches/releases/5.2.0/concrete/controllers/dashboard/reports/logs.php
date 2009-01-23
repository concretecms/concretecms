<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardReportsLogsController extends Controller {
	
	public $helpers = array('form', 'html');
	
	public function clear($type, $token = '') {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			Log::clearAll();
			$this->redirect('/dashboard/reports/logs');
		} else {
			$this->redirect('/dashboard/reports/logs');
		}
	}
	
	public function view($page = 0, $keywords = '') {
		$this->set('title', t('Logs'));
		$pageBase = View::url('/dashboard/reports/logs', 'view');
		$paginator = Loader::helper('pagination');
		if ($keywords == '') {
			$keywords = $_POST['keywords'];
		}
		$total = Log::getTotal($keywords, $type);
		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%/' . $keywords, 10);
		$limit=$paginator->getLIMIT();

		$entries = Log::getList($keywords, null, $limit);
		$this->set('keywords', $keywords);
		$this->set('pageBase', $pageBase);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);

	}
	
}
?>