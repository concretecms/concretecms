<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Controller_Panel extends Controller {

	abstract protected function canViewPanel();
	protected $page;
	protected $error;

	public function __construct() {
		$this->error = Loader::helper('validation/error');
		$this->view = new DialogView($this->viewPath);
		$this->view->setController($this);
		$request = Request::getInstance();
		$this->page = Page::getByID($request->query->get('cID'));
		$this->request = $request;
		$this->permissions = new Permissions($this->page);		
		$this->set('c', $this->page);
		$this->set('cp', $this->permissions);
	}

	public function getViewObject() {
		if ($this->permissions->canViewPage()) {
			if ($this->canViewPanel()) {
				return parent::getViewObject();
			}
		}
		throw new Exception(t('Access Denied'));
	}

	protected function validateSubmitPanel() {
		if (!Loader::helper('validation/token')->validate($this->viewPath)) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			return false;
		}
		return true;
	}

	public function action() {
		$url = call_user_func_array('parent::action', func_get_args());
		$url .= '?ccm_token=' . Loader::helper('validation/token')->generate($this->viewPath);
		$url .= '&cID=' . $this->page->getCollectionID();
		return $url;
	}

}

