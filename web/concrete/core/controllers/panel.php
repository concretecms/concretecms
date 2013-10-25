<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Controller_Panel extends Controller {

	abstract protected function canViewPanel();
	protected $page;

	public function __construct() {
		$this->view = new DialogView($this->viewPath);
		$this->view->setController($this);
		$request = Request::getInstance();
		$this->page = Page::getByID($request->query->get('cID'));
		$this->permissions = new Permissions($this->page);		
		$this->set('c', $this->page);
	}

	public function getViewObject() {
		if ($this->permissions->canViewPage()) {
			if ($this->canViewPanel()) {
				return parent::getViewObject();
			}
		}
		throw new Exception(t('Access Denied'));
	}

}

