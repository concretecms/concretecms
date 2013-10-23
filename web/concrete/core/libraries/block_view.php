<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_BlockView extends View {

	protected $block;
	protected $area;
	protected $blockType;
	protected $blockTypePkgHandle;
	protected $blockViewHeaderFile;
	protected $blockViewFooterFile;
	protected $outputContent = false;
	protected $viewToRender = false;
	protected $viewPerformed = false;

	public function __construct($mixed) {
		if ($mixed instanceof Block) {
			$this->blockType = $mixed->getBlockTypeObject();
			$this->block = $mixed;
			$this->area = $mixed->getBlockAreaObject();
		} else {
			$this->blockType = $mixed;
		}
		$this->blockTypePkgHandle = $this->blockType->getPackageHandle();
	}		

	public function setAreaObject(Area $area) {
		$this->area = $area;
	}

	public function start($view) {
		/** 
		 * Legacy shit
		 */
		if ($view instanceof Block) {
			$this->block = $view;
			$this->viewToRender = 'view';
		} else {
			$this->viewToRender = $view;
		}
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
		if ($this->outputContent) {
			return false;
		}

		$view = $this->viewToRender;

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
				if ($this->controller->blockViewRenderOverride) {
					$template = DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . $this->controller->blockViewRenderOverride . '.php';
					$this->setViewTemplate($env->getPath($template, $this->blockTypePkgHandle));
				} else {
					if ($bFilename) {
						$bvt->setBlockCustomTemplate($bFilename); // this is PROBABLY already set by the method above, but in the case that it's passed by area we have to set it here
					} else if ($customFilenameToRender) {
						$bvt->setBlockCustomRender($customFilenameToRender); 
					}
					$this->setViewTemplate($bvt->getTemplate());
				}
				break;
			case 'add':
				$this->setBlockViewHeaderFile(DIR_FILES_ELEMENTS_CORE . '/block_header_add.php');
				$this->setBlockViewFooterFile(DIR_FILES_ELEMENTS_CORE . '/block_footer_add.php');
				if ($this->controller->blockViewRenderOverride) {
					$template = DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . $this->controller->blockViewRenderOverride . '.php';
				} else {
					$template = DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . FILENAME_BLOCK_ADD;
				}
				$this->setViewTemplate($env->getPath($template, $this->blockTypePkgHandle));
				break;
			case 'scrapbook':
				$this->setViewTemplate($env->getPath(DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . FILENAME_BLOCK_VIEW_SCRAPBOOK, $this->blockTypePkgHandle));
				break;
			case 'edit':
				if ($this->controller->blockViewRenderOverride) {
					$template = DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . $this->controller->blockViewRenderOverride . '.php';
				} else {
					$template = DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . FILENAME_BLOCK_EDIT;
				}
				$this->setBlockViewHeaderFile(DIR_FILES_ELEMENTS_CORE . '/block_header_edit.php');
				$this->setBlockViewFooterFile(DIR_FILES_ELEMENTS_CORE . '/block_footer_edit.php');
				$this->setViewTemplate($env->getPath($template, $this->blockTypePkgHandle));
				break;
		}

		$this->viewPerformed = $view;
	}

	protected function onBeforeGetContents() {
		if (in_array($this->viewPerformed, array('scrapbook', 'view'))) {
			$v = View::getInstance();
			$this->controller->runTask('on_page_view', array($this));
			$this->controller->outputAutoHeaderItems();
		}
	}

	public function renderViewContents($scopeItems) {
		if ($this->outputContent) {
			print $this->outputContent;			
		} else {
			extract($scopeItems);
			if ($this->blockViewHeaderFile) {
				include($this->blockViewHeaderFile);
			}

			$this->onBeforeGetContents();
			ob_start();
			include($this->template);
			$this->outputContent = ob_get_contents();
			ob_end_clean();
			print $this->outputContent;
			$this->onAfterGetContents();

			if ($this->blockViewFooterFile) {
				include($this->blockViewFooterFile);
			}
		}
	}

	protected function setBlockViewHeaderFile($file) {
		$this->blockViewHeaderFile = $file;
	}

	protected function setBlockViewFooterFile($file) {
		$this->blockViewFooterFile = $file;
	}

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

		/** 
		 * Returns a relative path to the current block's directory. If a filename is specified it will be appended and searched for as well.
		 * @return string
		 */
		public function getBlockURL($filename = null) {
			$obj = $this->block;
			if ($obj->getPackageID() > 0) {
				if (is_dir(DIR_PACKAGES_CORE . '/' . $obj->getPackageHandle())) {
					$base = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $obj->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
				} else {
					$base = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $obj->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
				}
			} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $filename)) {
				$base = DIR_REL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
			} else {
				$base = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
			}
			
			return $base;
		}
	
	public function inc($file, $args = array()) {
		extract($args);
		extract($this->getScopeItems());
		$env = Environment::get();
		include($env->getPath(DIRNAME_BLOCKS . '/' . $this->blockType->getBlockTypeHandle() . '/' . $file, $this->blockTypePkgHandle));
	}

	public function getScopeItems() {
		$items = parent::getScopeItems();
		$items['b'] = $this->block;
		$items['bt'] = $this->blockType;
		return $items;
	}

	protected function useBlockCache() {
		$u = new User();
		if ($this->viewToRender == 'view' && ENABLE_BLOCK_CACHE && $this->controller->cacheBlockOutput() && ($this->block instanceof Block)) {
			if ((!$u->isRegistered() || ($this->controller->cacheBlockOutputForRegisteredUsers())) &&
				(($_SERVER['REQUEST_METHOD'] != 'POST' || ($this->controller->cacheBlockOutputOnPost() == true)))) {
				return true;
			}
		}
		return false;
	}

	public function finishRender($contents) {
		if ($this->useBlockCache()) {
			$this->block->setBlockCachedOutput($this->outputContent, $this->controller->getBlockTypeCacheOutputLifetime(), $this->area);
		}
		return $contents;
	}

	public function runControllerTask() {
		if ($this->useBlockCache()) {
			$this->outputContent = $this->block->getBlockCachedOutput($this->area);
		}

		if (!$this->outputContent) {
			if (in_array($this->viewToRender, array('view', 'add', 'edit', 'composer'))) {
				$method = $this->viewToRender;
			} else {
				$method = 'view';
			}
			$this->controller->setupAndRun($method);
		}

	}

	public function setupController() {
		if (!isset($this->controller)) {
			if (isset($this->block)) {
				$this->controller = $this->block->getInstance();
				$this->controller->setBlockObject($this->block);
			} else {
				$this->controller = Loader::controller($this->blockType);
			}

			if (is_object($this->area)) {
				$this->controller->setAreaObject($this->area);
			}
		}
	}

	/** 
	 * Legacy
	 * @access private
	 */
	public function getThemePath() {
		$v = View::getInstance();
		return $v->getThemePath();
	}

}
	