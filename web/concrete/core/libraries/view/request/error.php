<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ErrorRequestView extends RequestView {
	
	protected $error;

	public function start($error) {
		$this->error = $error;
	}
	public function action($action) {
		throw new Exception(t('Action is not available here.'));
	}

	protected function setupController() {}
	public function startRender() {}
	public function setupRender() {
		$env = Environment::get();
		$r = $env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . FILENAME_THEMES_ERROR . '.php');
		$this->setViewTemplate($r);
	}

	public function getScopeItems() {
		return array('innerContent' => $this->error->content, 'titleContent' => $this->error->title);
	}
	public function finishRender() {
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}

}
