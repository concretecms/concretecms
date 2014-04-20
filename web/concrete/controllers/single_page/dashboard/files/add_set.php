<?
namespace Concrete\Controller\SinglePage\Dashboard\Files;
use \Concrete\Core\Page\Controller\DashboardPageController;
use User;
use FileSet;
use Loader;
class AddSet extends DashboardPageController {

	public $helpers = array('form','validation/token','concrete/ui'); 

	public function do_add() {
		extract($this->getHelperObjects());
		
		
		if (!$validation_token->validate("file_sets_add")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}
		
		if (!trim($this->post('file_set_name'))) {
			$this->set('error', array(t('Please Enter a Name')));
			return;
		}
		$setName = trim($this->post('file_set_name'));
		if (preg_match('/[<>;{}?"`]/i', $setName)) {
			$this->set('error', array(t('File Set Name cannot contain the characters: %s', Loader::helper('text')->entities('<>;{}?"`'))));
			return;
		}

		$fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
		$fs = FileSet::add($setName, $fsOverrideGlobalPermissions);
		$this->redirect('/dashboard/files/sets', 'file_set_added');		
	}
	
}
