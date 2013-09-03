<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_View {

	protected $controller;
	protected $template;

	abstract public function start($mixed);
	abstract public function startRender();
	abstract public function executeRender();
	abstract protected function setupController();

	public function setViewTemplate($template) {
		$this->template = $template;
	}

	public function render($mixed) {
		$this->start($mixed);
		$this->setupController();
		$this->startRender();

		$this->controller->on_before_render();
		extract($this->controller->getSets());
		extract($this->controller->getHelperObjects());

		$this->executeRender();
	}

	protected function getViewContents() {
		ob_start();
		include($this->template);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}