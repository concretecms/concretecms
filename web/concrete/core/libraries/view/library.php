<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_View {

	protected static $requestInstance;
	protected static $requestInstances = array();

	public static function getRequestInstance() {
		if (null === self::$requestInstance) {
			View::setRequestInstance(new PathRequestView());
		}
		return self::$requestInstance;
	}

	protected static function setRequestInstance(RequestView $v) {
		View::$requestInstances[] = $v;
		self::$requestInstance = $v;
	}

	protected static function revertRequestInstance() {
		array_pop(View::$requestInstances);
		self::$requestInstance = View::$requestInstances[count(View::requestInstances)];
	}

	public $controller;

	protected $template;
	protected $outputAssets = array();

	abstract public function start($mixed);
	abstract public function startRender();
	abstract public function setupRender();
	abstract protected function setupController();
	abstract public function finishRender();
	abstract public function action($action);


	public function addHeaderAsset($asset) {
		$v = View::getRequestInstance();
		$v->addHeaderAsset($asset);
	}

	public function addFooterAsset($asset) {
		$v = View::getRequestInstance();
		$v->addFooterAsset($asset);
	}

	public function addOutputAsset($asset) {
		$v = View::getRequestInstance();
		$v->addOutputAsset($asset);
	}
	
	public function setController($controller) {
		$this->controller = $controller;
	}
	
	public function setViewTemplate($template) {
		$this->template = $template;
	}

	/**
	 * Returns the value of the item in the POST array.
	 * @access public
	 * @param $key
	 * @return void
	*/
	public function post($key) {
		return $this->controller->post($key);
	}

	protected function onBeforeGetContents() {
		if (is_object($this->controller)) {
			$this->controller->on_before_render();
		}
	}

	protected function postProcessViewContents($contents) {return $contents;}
	protected function onAfterGetContents() {}

	public function getScopeItems() {
		$return = array_merge($this->controller->getSets(), $this->controller->getHelperObjects());
		$return['view'] = $this;
		$return['controller'] = $this->controller;
		return $return;
	}

	public function render($mixed) {
		if ($this instanceof RequestView) {
			$this->setRequestInstance($this);
		}
		$this->start($mixed);
		$this->setupController();
		$this->setupRender();
		$this->startRender();
		$scopeItems = $this->getScopeItems();
		$contents = $this->renderViewContents($scopeItems);
		$contents = $this->postProcessViewContents($contents);
		$this->deliverRender($contents);
		$this->finishRender();
		if ($this instanceof RequestView) {
			$this->revertRequestInstance();
		}
	}

	public function renderViewContents($scopeItems) {
		if (file_exists($this->template)) {
			extract($scopeItems);
			ob_start();
			$this->onBeforeGetContents();
			include($this->template);
			$this->onAfterGetContents();
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}

	public function deliverRender($contents) {
		print $contents;
	}

	/**
	 * url is a utility function that is used inside a view to setup urls w/tasks and parameters		
	 * @access public
	 * @param string $action
	 * @param string $task
	 * @return string $url
	*/	
	public function url($action, $task = null) {
		$dispatcher = '';
		if ((!URL_REWRITING_ALL) || !defined('URL_REWRITING_ALL')) {
			$dispatcher = '/' . DISPATCHER_FILENAME;
		}
		
		$action = trim($action, '/');
		if ($action == '') {
			return DIR_REL . '/';
		}
		
		// if a query string appears in this variable, then we just pass it through as is
		if (strpos($action, '?') > -1) {
			return DIR_REL . $dispatcher. '/' . $action;
		} else {
			$_action = DIR_REL . $dispatcher. '/' . $action . '/';
		}
		
		if ($task != null) {
			if (ENABLE_LEGACY_CONTROLLER_URLS) {
				$_action .= '-/' . $task;
			} else {
				$_action .= $task;			
			}
			$args = func_get_args();
			if (count($args) > 2) {
				for ($i = 2; $i < count($args); $i++){
					$_action .= '/' . $args[$i];
				}
			}
			
			if (strpos($_action, '?') === false) {
				$_action .= '/';
			}
		}
		
		return $_action;
	}

	// Legacy Items. Deprecated

	public function setThemeByPath($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
		$l = Router::get();
		$l->setThemeByPath($path, $theme, $wrapper);
	}


	public function renderError($title, $error, $errorObj = null) {
		Loader::helper('concrete/interface')->renderError($title, $error);
	}

	/** 
	 * @access private
	 */
	public function addHeaderItem($item) {
		$this->addHeaderAsset($item);
	}
	
	/** 
	 * @access private
	 */
	public function addFooterItem($item) {
		$this->addFooterAsset($item);
	}	

	/** 
	 * @access private
	 */
	public static function getInstance() {
		return View::getRequestInstance();
	}


}