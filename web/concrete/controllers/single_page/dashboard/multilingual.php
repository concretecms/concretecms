<?
namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
class Multilingual extends DashboardPageController
{

	public function view() {
		$this->redirect('/dashboard/multilingual/setup');
	}

	
}
