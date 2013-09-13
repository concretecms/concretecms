<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_BlockView extends View {

	protected $block;
	protected $blockType;
	protected $blockTypePkgHandle;
	protected $blockViewHeaderFile;
	protected $blockViewFooterFile;

	public function __construct($mixed) {
		if ($mixed instanceof Block) {
			$this->blockType = $mixed->getBlockTypeObject();
			$this->block = $mixed;
		} else {
			$this->blockType = $mixed;
		}
		$this->blockTypePkgHandle = $this->blockType->getPackageHandle();
	}		

	public function start($view) {
		$this->viewToRender = $view;
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

	public function startRender() {}
	public function setupRender() {
		$env = Environment::get();
		if ($this->viewToRender == 'scrapbook') {
			$scrapbookTemplate = $this->getBlockPath(FILENAME_BLOCK_VIEW_SCRAPBOOK) . '/' . FILENAME_BLOCK_VIEW_SCRAPBOOK;
			if (file_exists($scrapbookTemplate)) {
				$view = 'scrapbook';
			} else {
				$view = 'view';
			}
		}
		if (!in_array($this->viewToRender, array('view', 'add', 'edit', 'scrapbook'))) {
			// then we're trying to render a custom view file, which we'll pass to the bottom functions as $_filename
			$customFilenameToRender = $view . '.php';
			$view = 'view';
		}

		switch($view) {
			case 'view':
				$this->setBlockViewHeaderFile(DIR_FILES_ELEMENTS_CORE . '/block_header_view.php');
				$this->setBlockViewFooterFile(DIR_FILES_ELEMENTS_CORE . '/block_footer_view.php');
				$bFilename = $this->block->getBlockFilename();
				$bvt = new BlockViewTemplate($this->block);
				if ($bFilename) {
					$bvt->setBlockCustomTemplate($bFilename); // this is PROBABLY already set by the method above, but in the case that it's passed by area we have to set it here
				} else if ($customFilenameToRender) {
					$bvt->setBlockCustomRender($customFilenameToRender); 
				}
				$this->setViewTemplate($bvt->getTemplate());
				break;
			case 'add':
				$this->setBlockViewHeaderFile(DIR_FILES_ELEMENTS_CORE . '/block_header_add.php');
				$this->setBlockViewFooterFile(DIR_FILES_ELEMENTS_CORE . '/block_footer_add.php');
				$this->setViewTemplate($env->getPath(DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . FILENAME_BLOCK_ADD, $this->blockTypePkgHandle));
				break;


		}


	}

	protected function setBlockViewHeaderFile($file) {
		$this->blockViewHeaderContentFile = $file;
	}

	protected function setBlockViewFooterFile($file) {
		$this->blockViewFooterfile = $file;
	}

	public function finishRender() {}
	public function postProcessViewContents($contents) {
		return $contents;
	}

	/**
	 * Returns the path to the current block's directory
	 * @access private
	 * @deprecated
	 * @return string
	*/
	public function getBlockPath($filename = null) {
		$obj = $this->blockType;			
		if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $filename)) {
			$base = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle();
		} else if ($obj->getPackageID() > 0) {
			if (is_dir(DIR_PACKAGES . '/' . $obj->getPackageHandle())) {
				$base = DIR_PACKAGES . '/' . $obj->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
			} else {
				$base = DIR_PACKAGES_CORE . '/' . $obj->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
			}
		} else {
			$base = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle();
		}
		return $base;
	}
	
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

			if (in_array($this->viewToRender, array('view', 'add', 'edit', 'composer'))) {
				$method = $view;
			} else {
				$method = 'view';
			}
			$this->controller->setupAndRun($method);
		}
	}

}
	