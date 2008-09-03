<?

class DashboardLogsController extends Controller {

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
			array(View::url('/dashboard/logs'), 'Concrete5', $c5selected),
			array(View::url('/dashboard/logs', 'database'), 'Database', $dbSelected),
			array(View::url('/dashboard/logs', 'custom'), 'Custom', $custSelected)
		);
		$this->set('subnav', $subnav);
	}
	
	public function clear_database_log() {
		DatabaseLogEntry::clear();
		$this->redirect('/dashboard/logs/', 'database');
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
	
	public function view($type = null, $page = 0) {

		$this->set('title', 'Concrete5 Logs');
		$pageBase = View::url('/dashboard/logs', $type);
		$paginator = Loader::helper('pagination');
		$total = Log::getTotal($type, 1);

		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%', 10);
		$limit=$paginator->getLIMIT();

		$entries = Log::getList($type, 1, $limit);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);

	}
	
	public function custom($page = 0) {
		$this->set('title', 'Custom Logs Defined by Your Application');
		$pageBase = View::url('/dashboard/logs', 'custom');
		$paginator = Loader::helper('pagination');
		$total = Log::getTotal(false, 0);

		$paginator->init(intval($page), $total, $pageBase . '/%pageNum%', 10);
		$limit=$paginator->getLIMIT();

		$entries = Log::getList(false, 0, $limit);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);
	}
}
?>