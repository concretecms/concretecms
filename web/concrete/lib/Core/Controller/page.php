<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Controller_Page extends Controller {

	protected $supportsPageCache = false;
	protected $action;
	protected $parameters = array();

	public function supportsPageCache() {
		return $this->supportsPageCache;
	}

	public function __construct(Page $c) {
		$this->c = $c;
		$this->view = new PageView($this->c);
	}

	public function getPageObject() {
		return $this->c;
	}

	public function getTheme() {
		if (!$this->theme) {
			$theme = parent::getTheme();
			if (!$theme) {
				$theme = $this->c->getCollectionThemeObject();
				if (is_object($theme)) {
					$this->theme = $theme->getThemeHandle();
				}
			} else {
				$this->theme = $theme;
			}
		}
		return $this->theme;
	}

	public function getRequestAction() {
		return $this->action;
	}

	public function getRequestActionParameters() {
		return $this->parameters;
	}

	public function passthru($arHandle = false, $bID = false, $action = false) {
		$args = func_get_args();
		$args = array_slice(func_get_args(), 3);
		$this->action = 'passthru';
		$this->parameters = func_get_args();
	}


	public function setupRequestActionAndParameters(Request $request) {
		$task = substr($request->getPath(), strlen($this->c->getCollectionPath()) + 1);
		$task = str_replace('-/', '', $task);
		$taskparts = explode('/', $task);
		if (isset($taskparts[0]) && $taskparts[0] != '') {
			$method = $taskparts[0];
		}
		if ($method == '') {
			if (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
				$method = $this->c->getCollectionHandle();
			} else {
				$method = 'view';
			}
		}

		$foundTask = false;

		try {
			$r = new ReflectionMethod(get_class($this), $method);
			$cl = $r->getDeclaringClass();
			if (is_object($cl)) {
				if ($cl->getName() != 'Controller' && strpos($method, 'on_') !== 0 && strpos($method, '__') !== 0 && $r->isPublic()) {
					$foundTask = true;
				}
			}
		} catch(Exception $e) {

		}

		if ($foundTask) {
			$this->action = $method;
			if (isset($taskparts[1])) {
				array_shift($taskparts);
				$this->parameters = $taskparts;
			}
		} else {
			$this->action = 'view';
			if ($taskparts[0]) {
				$this->parameters = $taskparts;
			}
		}
	}

	public function validateRequest() {
		$valid = true;
		if (!is_callable(array($this, $this->action)) && count($this->parameters) > 0) {
			$valid = false;
		}

		if (is_callable(array($this, $this->action))  && (get_class($this) != 'PageForbiddenController')) {
			// we use reflection to see if the task itself, which now much exist, takes fewer arguments than 
			// what is specified
			$r = new ReflectionMethod(get_class($this), $this->action);
			if ($r->getNumberOfParameters() < count($this->parameters)) {
				$valid = false;
			}
		}

		return $valid;
	}
}