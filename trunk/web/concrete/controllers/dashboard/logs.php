<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardLogsController extends Controller {
	
	public $helpers = array('form', 'html');
	public function on_start() {
		$c5selected = false;
		$dbSelected = false;
		$appSelected = false;				
		switch($this->getTask()) {
			case "database":
				$dbSelected = true;
				break;
			case "custom":
				$custSelected = true;
				break;
			default:
				$c5selected = true;
				break;
		}					
		$subnav = array(
			array(View::url('/dashboard/logs'), t('Concrete5'), $c5selected),
			array(View::url('/dashboard/logs', 'database'), t('Database'), $dbSelected),
			array(View::url('/dashboard/logs', 'custom'), t('Custom'), $custSelected)
		);
		$this->set('subnav', $subnav);
	}
	
	public function clear_database_log($token = '') {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			DatabaseLogEntry::clear();
			$this->redirect('/dashboard/logs/', 'database');
		} else {
			$this->redirect('/dashboard/logs');
		}
	}
	
	public function clear_log($type, $token = '') {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			switch($type) {
				case "custom":
					Log::clearCustom();				
					$this->redirect("/dashboard/logs", "custom");
					break;
				default:
					Log::clearInternal();				
					$this->redirect("/dashboard/logs");
					break;
			}
		} else {
			$this->redirect('/dashboard/logs');
		}
	}
	

	public function database($page = 0) {
		$total = DatabaseLogEntry::getTotal();
		$pageBase = View::url('/dashboard/logs', 'database');
		$paginator = Loader::helper('pagination');
		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%', 50);
		$limit=$paginator->getLIMIT();
		$entries = DatabaseLogEntry::getList($limit);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);
	}
	
	public function view($type = 'none', $page = 0, $keywords = '') {
		$this->set('title', t('Concrete5 Logs'));
		$pageBase = View::url('/dashboard/logs', $type);
		if ($type == 'none') {
			$type = null;
		}
		$paginator = Loader::helper('pagination');
		if ($keywords == '') {
			$keywords = $_POST['keywords'];
		}
		$total = Log::getTotal($keywords, $type, 1);
		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%/' . $keywords, 10);
		$limit=$paginator->getLIMIT();

		$entries = Log::getList($keywords, $type, 1, $limit);
		$this->set('keywords', $keywords);
		$this->set('pageBase', $pageBase);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);

	}
	
	public function custom($page = 0, $keywords = '') {
		$this->set('title', t('Custom Application Logs'));
		$pageBase = View::url('/dashboard/logs', 'custom');
		$paginator = Loader::helper('pagination');
		
		if ($keywords == '') {
			$keywords = $_POST['keywords'];
		}
		$total = Log::getTotal($keywords, false, 0);

		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%/' . $keywords, 10);
		$limit=$paginator->getLIMIT();

		$entries = Log::getList($keywords, false, 0, $limit);
		$this->set('keywords', $keywords);
		$this->set('entries', $entries);
		$this->set('pageBase', $pageBase);
		$this->set('paginator', $paginator);
	}
}
?>