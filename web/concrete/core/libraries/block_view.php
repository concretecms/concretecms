<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_BlockView extends View {

	protected $block;
	protected $blockType;
	protected $blockTypePkgHandle;

	/**
	 * Includes a file from the core elements directory. Used by the CMS.
	 * @access private
	 */
	public function renderElement($element, $args = array()) {
		Loader::element($element, $args);
	}


	/**
	 * Creates a URL that can be posted or navigated to that, when done so, will automatically run the corresponding method inside the block's controller.
	 * <code>
	 *     <a href="<?=$this->action('get_results')?>">Get the results</a>
	 * </code>
	 * @param string $task
	 * @return string $url
	 */
	public function action($task) {
		try {
			if (is_object($this->block)) {
				if (is_object($this->block->getProxyBlock())) {
					$b = $this->block->getProxyBlock();
				} else {
					$b = $this->block;
				}
				
				if (is_object($b)) {
					return $b->getBlockPassThruAction() . '&amp;method=' . $task;
				}
			}
		} catch(Exception $e) {}
	}

	/** 
	 * Begin the render
	 */
	public function start($obj) {
		if ($obj instanceof Block) {
			$this->blockType = $obj->getBlockTypeObject();
			$this->block = $obj;
		} else {
			$this->blockType = $obj;
		}
		$this->blockTypePkgHandle = $this->blockType->getPackageHandle();
	}

	public function startRender() {}
	public function setupRender() {


	}

	public function finishRender() {}

	public function inc($file, $args = array()) {
		extract($args);
		extract($this->getScopeItems());
		$env = Environment::get();
		include($env->getPath(DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . $file, $this->blockTypePkgHandle));
	}

	public function setupController() {
		if (!isset($this->controller)) {
			if (isset($this->block)) {
				$this->controller = $this->block->getInstance();
				$this->controller->setBlockObject($this->block);
			} else {
				$this->controller = Loader::controller($this->blockType);
			}
		}
	}

}
	