<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Controller_Frontend_Edit extends Controller {

	abstract protected function canAccess();
	protected $error;

	public function __construct() {
		$this->error = Loader::helper('validation/error');
		$this->view = new DialogView($this->viewPath);
		$this->view->setController($this);
		$request = Request::getInstance();
		$this->request = $request;
	}

	public function getViewObject() {
		if ($this->canAccess()) {
			return parent::getViewObject();
		}
		throw new Exception(t('Access Denied'));
	}

	protected function validateAction() {
		if (!Loader::helper('validation/token')->validate($this->viewPath)) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			return false;
		}
		return true;
	}

	public function action() {
		$url = call_user_func_array('parent::action', func_get_args());
		$url .= '?ccm_token=' . Loader::helper('validation/token')->generate($this->viewPath);
		return $url;
	}

}

