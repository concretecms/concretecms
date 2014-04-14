<?
namespace Concrete\Controller\Backend;
use Controller;
use \Concrete\Core\View\DialogView;
use Request;
abstract class UI extends Controller {

	abstract protected function canAccess();
	protected $error;

	public function __construct() {
		$this->error = Loader::helper('validation/error');
		$this->view = new DialogView($this->viewPath);
		$this->view->setController($this);
		$request = Request::getInstance();
		$this->request = $request;

		set_exception_handler(function($exception) {
			print $exception->getMessage();
		});
	}

	public function getViewObject() {
		if ($this->canAccess()) {
			return parent::getViewObject();
		}
		throw new Exception(t('Access Denied'));
	}

	protected function validateAction() {
		if (!helper('validation/token')->validate($this->getControllerActionPath())) {
			$this->error->add(helper('validation/token')->getErrorMessage());
			return false;
		}
		return true;
	}

	public function action() {
		$url = call_user_func_array('parent::action', func_get_args());
		$url .= '?ccm_token=' . Loader::helper('validation/token')->generate($this->getControllerActionPath());
		return $url;
	}

}

