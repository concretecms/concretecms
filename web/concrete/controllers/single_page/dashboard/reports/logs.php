<?

namespace Concrete\Controller\SinglePage\Dashboard\Reports;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Logging\LogList;

class Logs extends DashboardPageController {
	
	public function view($page = 0) {
		$list = new LogList();
        $list->setItemsPerPage(1);
        $entries = $list->getPage();
        $this->set('list', $list);
        $this->set('entries', $entries);


	}
	
}
?>