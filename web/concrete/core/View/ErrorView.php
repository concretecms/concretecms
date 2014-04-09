<?

namespace Concrete\Core\View;
use View;

class ErrorView extends View {
	
	protected $error;

	protected function constructView($error) {
		$this->error = $error;
	}
	public function action($action) {
		throw new Exception(t('Action is not available here.'));
	}

	protected function setupController() {}
	protected function runControllerTask() {}
	public function setupRender() {
		$env = Environment::get();
		$r = $env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . FILENAME_THEMES_ERROR . '.php');
		$this->setViewTemplate($r);
	}

	public function onBeforeGetContents() {}

	public function getScopeItems() {
		return array('innerContent' => $this->error->content, 'titleContent' => $this->error->title);
	}
	public function finishRender() {
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}

}
