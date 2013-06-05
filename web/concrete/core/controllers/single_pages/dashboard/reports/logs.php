<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Reports_Logs extends Controller {
	
	public $helpers = array('form', 'html');
	
	public function clear($token = '', $type = false) {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			if (!$type) { 
				Log::clearAll();
			} else {
				Log::clearByType($type);
			}
			$this->redirect('/dashboard/reports/logs');
		} else {
			$this->redirect('/dashboard/reports/logs');
		}
	}
	
	public function view($page = 0) {
		$this->set('title', t('Logs'));
		$pageBase = View::url('/dashboard/reports/logs', 'view');
		$paginator = Loader::helper('pagination');
		
		$total = Log::getTotal($_REQUEST['keywords'], $_REQUEST['logType']);
		$paginator->init(intval($page), $total, $pageBase . '%pageNum%/?keywords=' . $_REQUEST['keywords'] . '&logType=' . $_REQUEST['logType'], 10);
		$limit=$paginator->getLIMIT();

		$types = Log::getTypeList();
		$txt = Loader::helper('text');
		$logTypes = array();
		$logTypes[''] = '** ' . t('All');
		foreach($types as $t) {
			if ($t == '') {
				$logTypes[''] = '** ' . t('All');
			} else {
				$logTypes[$t] = $txt->unhandle($t);
			}
		}

		$entries = Log::getList($_REQUEST['keywords'], $_REQUEST['logType'], $limit);
		$this->set('keywords', $keywords);
		$this->set('pageBase', $pageBase);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);
		$this->set('logTypes', $logTypes);
			

	}
	
}
?>