<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_RequestView extends View {

	protected $viewPath;
	protected $innerContentFile;
	protected $themeHandle;
	protected $themeObject;
	protected $themeRelativePath;
	protected $themeAbsolutePath;
	protected $themePkgHandle;
	protected $viewRootDirectoryName = DIRNAME_VIEWS;
	private $providedAssetGroupUnmatched = array();

	public function __construct($path) {
		$path = '/' . trim($path, '/');
		$this->viewPath = $path;
	}

	public function getThemeDirectory() {return $this->themeAbsolutePath;}
	/**
	 * gets the relative theme path for use in templates
	 * @access public
	 * @return string $themePath
	*/
	public function getThemePath() { return $this->themeRelativePath; }
	public function getThemeHandle() {return $this->themeHandle;}
	
	public function setInnerContentFile($innerContentFile) {
		$this->innerContentFile = $innerContentFile;
	}

	public function setRequestViewRootDirectoryName($directory) {
		$this->viewRootDirectoryName = $directory;
	}

	public function inc($file, $args = array()) {
		extract($args);
		extract($this->getScopeItems());
		$env = Environment::get();
		include($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $file, $this->themePkgHandle));
	}

	/**
	 * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of 
	 * @param string $action
	 * @param string $task
	 * @return string $url
	 */
	public function action($action) {
		$a = func_get_args();
		array_unshift($a, $this->viewPath);
		$ret = call_user_func_array(array($this, 'url'), $a);
		return $ret;
	}

	public function setRequestViewTheme($theme) {
		if (is_object($theme)) {
			$this->themeHandle = $theme->getPageThemeHandle();
		} else {
			$this->themeHandle = $theme;
		}
	}

	/** 
	 * Load all the theme-related variables for which theme to use for this request.
	 */
	protected function loadRequestViewThemeObject() {
		$env = Environment::get();
		//$rl = Router::get();
		if ($this->controller->theme != false) {
			$this->setRequestViewTheme($this->controller->theme);
		} else {
			/*
			$this->themeHandle = VIEW_CORE_THEME;
			$tmpTheme = $rl->getThemeFromPath($this->viewPath);
			if ($tmpTheme) {
				$this->setRequestViewTheme($tmpTheme[0]);
			} else if (!$this->themeHandle) {
				if ($this->controller->theme != false) {
					$this->setRequestViewTheme($this->controller->theme);
				} else {
					$this->setRequestViewTheme(FILENAME_COLLECTION_DEFAULT_THEME);
				}
			}
			*/
		}

		if ($this->themeHandle) {
			if ($this->themeHandle != VIEW_CORE_THEME && $this->themeHandle != 'dashboard') {
				$this->themeObject = PageTheme::getByHandle($this->themeHandle);
				$this->themePkgHandle = $this->themeObject->getPackageHandle();
			}
			$this->themeAbsolutePath = $env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle, $this->themePkgHandle);
			$this->themeRelativePath = $env->getURL(DIRNAME_THEMES . '/' . $this->themeHandle, $this->themePkgHandle);
		}
	}

	/** 
	 * Begin the render
	 */
	public function start($mixed) {
		$this->requiredAssetGroup = new AssetGroup();
		$this->providedAssetGroup = new AssetGroup();
	}

	public function setupRender() {
		// Set the theme object that we should use for this requested page.
		// Only run setup if the theme is unset. Usually it will be but if we set it
		// programmatically we already have a theme.
		$this->loadRequestViewThemeObject();
		$env = Environment::get();
		$this->setInnerContentFile($env->getPath($this->viewRootDirectoryName . '/' . trim($this->viewPath, '/') . '.php', $this->themePkgHandle));
		if ($this->themeHandle) {
			if (file_exists(DIR_FILES_THEMES_CORE . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php')) {
				$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php'));
			} else {
				$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
			}
		}
	}

	public function startRender() {
		// First the starting gun.
		Events::fire('on_start', $this);
		parent::startRender();
	}

	protected function onBeforeGetContents() {
		Events::fire('on_before_render', $this);
		if ($this->themeHandle == VIEW_CORE_THEME) {
			$_pt = new ConcretePageTheme();
			$_pt->registerAssets();
		} else if (is_object($this->themeObject)) {
			$this->themeObject->registerAssets();
		}
	}

	public function renderViewContents($scopeItems) {
		extract($scopeItems);
		if ($this->innerContentFile) {
			ob_start();
			include($this->innerContentFile);
			$innerContent = ob_get_contents();
			ob_end_clean();
		}

		if (file_exists($this->template)) {
			ob_start();
			$this->onBeforeGetContents();
			include($this->template);
			$contents = ob_get_contents();
			$this->onAfterGetContents();
			ob_end_clean();
			return $contents;
		} else {
			return $innerContent;
		}
	}

	public function finishRender($contents) {
		$ret = Events::fire('on_page_output', $contents);
		if($ret != '') {
			$contents = $ret;
		}
		Events::fire('on_render_complete', $this);
		return $contents;
	}


	/** 
	 * Assets
	 */
	public function addHeaderAsset($item) {
		$this->outputAssets[Asset::ASSET_POSITION_HEADER]['unweighted'][] = $item;
	}
	
	/** 
	 * Function responsible for adding footer items within the context of a view.
	 * @access private
	 */
	public function addFooterAsset($item) {
		$this->outputAssets[Asset::ASSET_POSITION_FOOTER]['unweighted'][] = $item;
	}

	public function addOutputAsset(Asset $asset) {
		if ($asset->getAssetWeight() > 0) {
			$this->outputAssets[$asset->getAssetPosition()]['weighted'][] = $asset;
		} else {
			$this->outputAssets[$asset->getAssetPosition()]['unweighted'][] = $asset;
		}
	}

	/** 
	 * Notes in the current request that a particular asset has already been provided.
	 */
	public function markAssetAsIncluded($assetType, $assetHandle = false) {
		$list = AssetList::getInstance();
		if ($assetType && $assetHandle) {
			$asset = $list->getAsset($assetType, $assetHandle);
		} else {
			$assetGroup = $list->getAssetGroup($assetType);
		}

		if ($assetGroup) {
			$this->providedAssetGroup->addGroup($assetGroup);
		} else if ($asset) {
			$ap = new AssetPointer($asset->getAssetType(), $asset->getAssetHandle());
			$this->providedAssetGroup->add($ap);
		} else {
			$ap = new AssetPointer($assetType, $assetHandle);
			$this->providedAssetGroupUnmatched[] = $ap;
		}
	}

	/** 
	 * Adds a required asset to this request. This asset will attempt to be output or included
	 * when a view is rendered
	 */
	public function requireAsset($assetType, $assetHandle = false) {
		$list = AssetList::getInstance();
		if ($assetType instanceof Asset) {
			$this->requiredAssetGroup->addAsset($assetType);
		} else if ($assetType && $assetHandle) {
			$ap = new AssetPointer($assetType, $assetHandle);
			$this->requiredAssetGroup->add($ap);
		} else {
			$r = $list->getAssetGroup($assetType);
			$this->requiredAssetGroup->addGroup($r);
		}
	}

	/** 
	 * Returns all required assets
	 */
	public function getRequiredAssets() {
		return $this->requiredAssetGroup;
	}

	protected function filterProvidedAssets($asset) {
		foreach($this->providedAssetGroup->getAssetPointers() as $pass) {
			if ($pass->getHandle() == $asset->getHandle() && $pass->getType() == $asset->getType()) {
				return false;
			}
		}

		// now is the unmatched assets something that matches this asset?
		// (ie, is it a path-style match, like bootstrap/* )
		foreach($this->providedAssetGroupUnmatched as $assetPointer) {
			if ($assetPointer->getType() == $asset->getType() && fnmatch($assetPointer->getHandle(), $asset->getHandle())) {
				return false;
			}
		}

		return true;

	}

	/** 
	 * Returns only assets that are required but that aren't also in the providedAssetGroup
	 */
	public function getRequiredAssetsToOutput() {
		$required = $this->requiredAssetGroup->getAssetPointers();
		$assetPointers = array_filter($required, array('RequestView', 'filterProvidedAssets'));
		$assets = array();
		$al = AssetList::getInstance();
		foreach($assetPointers as $ap) {
			$asset = $ap->getAsset();
			if ($asset instanceof Asset) {
				$assets[] = $asset;
			}
		}
		// also include any hard-passed $assets into the group. This is rare but it's used for handle-less
		// assets like layout css
		$assets = array_merge($this->requiredAssetGroup->getAssets(), $assets);
		return $assets;
	}




	/** 
	 * Function responsible for outputting header items
	 * @access private
	 */
	public function markHeaderAssetPosition() {
		print '<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->';
	}
	
	/** 
	 * Function responsible for outputting footer items
	 * @access private
	 */
	public function markFooterAssetPosition() {
		print '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->';
	}

	public function postProcessViewContents($contents) {
		$assets = $this->getRequiredAssetsToOutput();
		
		foreach($assets as $asset) {
			$this->addOutputAsset($asset);
		}
		
		$contents = $this->replaceAssetPlaceholders($contents);

		// replace any empty placeholders
		$contents = $this->replaceEmptyAssetPlaceholders($contents);

		return $contents;
	}


	protected function sortAssetsByWeightDescending($assetA, $assetB) {
		$weightA = $assetA->getAssetWeight();
		$weightB = $assetB->getAssetWeight();

		if ($weightA == $weightB) {
			return 0;
		}

		return $weightA < $weightB ? 1 : -1;
	}

	protected function sortAssetsByPostProcessDescending($assetA, $assetB) {
		$ppA = ($assetA instanceof Asset && $assetA->assetSupportsPostProcessing());
		$ppB = ($assetB instanceof Asset && $assetB->assetSupportsPostProcessing());
		if ($ppA && $ppB) {
			return 0;
		}
		if ($ppA && !$ppB) {
			return -1;
		}

		if (!$ppA && $ppB) {
			return 1;
		}
		if (!$ppA && !$ppB) {
			return 0;
		}
	}

	protected function postProcessAssets($assets) {
		$c = Page::getCurrentPage();
		if (!is_object($c) || !ENABLE_ASSET_CACHE) {
			return $assets;
		}
		// goes through all assets in this list, creating new URLs and post-processing them where possible.
		$segment = 0;
		$subassets[$segment] = array();
		for ($i = 0; $i < count($assets); $i++) {
			$asset = $assets[$i];
			$nextasset = $assets[$i+1];
			$subassets[$segment][] = $asset;
			if ($asset instanceof Asset && $nextasset instanceof Asset) {
				if ($asset->getAssetType() != $nextasset->getAssetType()) {
					$segment++;
				} else if (!$asset->assetSupportsPostProcessing() || !$nextasset->assetSupportsPostProcessing()) {
					$segment++;
				}
			} else {
				$segment++;
			}
		}

		// now we have a sub assets array with different segments split by post process and non-post-process
		$return = array();
		foreach($subassets as $segment => $assets) {
			if ($assets[0] instanceof Asset && $assets[0]->assetSupportsPostProcessing()) {
				// this entire segment can be post processed together
				$class = Loader::helper('text')->camelcase($assets[0]->getAssetType()) . 'Asset';
				$assets = call_user_func(array($class, 'postprocess'), $assets);
			}
			$return = array_merge($return, $assets);
		}
		return $return;
	}

	protected function replaceEmptyAssetPlaceholders($pageContent) {
		foreach(array('<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->', '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->') as $comment) {
			$pageContent = str_replace($comment, '', $pageContent);
		}
		return $pageContent;
	}

	protected function replaceAssetPlaceholders($pageContent) {
		$outputItems = array();
		foreach($this->outputAssets as $position => $assets) {
			$output = '';
			if (is_array($assets['weighted'])) {
				$weightedAssets = $assets['weighted'];
				usort($weightedAssets, array($this, 'sortAssetsByWeightDescending'));
				$transformed = $this->postProcessAssets($weightedAssets);
				foreach($transformed as $item) {
					$itemstring = (string) $item;
					if (!in_array($itemstring, $outputItems)) {
						$output .= $this->outputAssetIntoView($item);
						$outputItems[] = $itemstring;
					}
				}
			}
			if (is_array($assets['unweighted'])) {
				// now the unweighted
				$unweightedAssets = $assets['unweighted'];
				usort($unweightedAssets, array($this, 'sortAssetsByPostProcessDescending'));
				$transformed = $this->postProcessAssets($unweightedAssets);
				foreach($transformed as $item) {
					$itemstring = (string) $item;
					if (!in_array($itemstring, $outputItems)) {
						$output .= $this->outputAssetIntoView($item);
						$outputItems[] = $itemstring;
					}
				}
			}
			$pageContent = str_replace('<!--ccm:assets:' . $position . '//-->', $output, $pageContent);
		}
		return $pageContent;				
	}
	
	protected function outputAssetIntoView($item) {
		return $item . "\n";			
	}

}