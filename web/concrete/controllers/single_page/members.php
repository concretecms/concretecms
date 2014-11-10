<?
namespace Concrete\Controller\SinglePage;
use \Concrete\Core\Page\Controller\PageController;
class Members extends PageController {
	
	public function view() {
        $this->redirect('/members/directory');
	}

}