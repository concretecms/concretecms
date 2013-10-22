<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_RequestController extends Controller {

	protected $request;
	protected $view;
	protected $route;

	public function __construct(Route $route, Request $request) {
		$this->request = $request;
		$this->route = $route;
		$this->view = new RequestView($route->getPath());
		$this->view->setController($this);
	}

}