<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_RequestController extends Controller {

	protected $view;
	protected $RequestViewPath;

	public function setViewObject(RequestView $view) {
		$this->view = $view;
		$this->view->setController($this);
	}

	public function __construct() {
		if ($this->requestViewPath) {
			$this->view = new RequestView($this->requestViewPath);
			$this->view->setController($this);
		}
	}

	public function getViewObject() {
		return $this->view;
	}

}