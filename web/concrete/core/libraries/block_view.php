<?
defined('C5_EXECUTE') or die("Access Denied.");
	
/**
 * An object corresponding to a particular view of a block. These are those of the "add" state, the block's "edit" state, or the block's "view" state.
 *
 * @package Blocks
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Library_BlockView extends View {
	
		protected $block;
		protected $area;
		protected $blockObj;
		
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
		 * @param strign $extraParams Adds items onto the end of the query string. Useful for anchor links, etc...
		 * @return string $url
		 */
		public function action($task, $extraParams = null) {
			try {
				if (is_object($this->block)) {
					if (is_object($this->block->getProxyBlock())) {
						$b = $this->block->getProxyBlock();
					} else {
						$b = $this->block;
					}
					
					if (is_object($b)) {
						return $b->getBlockPassThruAction() . '&amp;method=' . $task . $extraParams;
					}
				}
			} catch(Exception $e) {}
		}
		
		/**
		 * includes file from the current block directory. Similar to php's include()
		 * @access public
		 * @param string $file
		 * @param array $args
		 * @return void
		*/
		public function inc($file, $args = array()) {
			extract($args);
			$base = $this->getBlockPath($file);
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());

			include($base . '/' . $file);
		}
		
		
		/**
		 * Returns the path to the current block's directory
		 * @access public
		 * @return string
		*/
		public function getBlockPath($filename = null) {
			$obj = $this->blockObj;			
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

			$obj = $this->blockObj;
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
		
		/** 
		 * @access private
		 */
		public function setAreaObject($a) {
			$this->area = $a;
		}
		
		public function getThemePath() {
			$v = View::getInstance();
			return $v->getThemePath();
		}
		
		/** 
		 * Returns the template used in the block view
		 */
		public function getTemplate() {
			return $this->template;
		}
		
		public function getTemplateURL() {
			$bvt = new BlockViewTemplate($this->blockObj);
			return $bvt->getBaseURL();
		}
		
		public function setBlockObject($obj) {
			$this->blockObj = $obj;
		}
		
		public function getBlockObject() {
			return $this->blockObj;
		}
		
		/** 
		 * Renders a particular view for a block or a block type
		 * @param Block | BlockType $obj
		 * @param string $view
		 * @param array $args
		 */
		public function render($obj, $view = 'view', $args = array()) {
			if ($this->hasRendered) {
				return false;
			}
			$this->blockObj = $obj;
			$customAreaTemplates = array();
			
			if ($obj instanceof BlockType) {
				$bt = $obj;
				$base = $obj->getBlockTypePath();
			} else {
				$bFilename = $obj->getBlockFilename();
				$b = $obj;
				$base = $b->getBlockPath();
				$this->block = $b;
				$this->c = $b->getBlockCollectionObject();
				if ($bFilename == '' && is_object($this->area)) {
					$customAreaTemplates = $this->area->getCustomTemplates();
					$btHandle = $b->getBlockTypeHandle();
					if (isset($customAreaTemplates[$btHandle])) {
						$bFilename = $customAreaTemplates[$btHandle];
					}
				}

			}				
			
			$btHandle = $obj->getBlockTypeHandle();
			
			if (!isset($this->controller)) {
				if ($obj instanceof Block) {
					$this->controller = $obj->getInstance();
					$this->controller->setBlockObject($obj);
				} else {
					$this->controller = Loader::controller($obj);
				}
			}
			if (in_array($view, array('view', 'add', 'edit', 'composer'))) {
				$_action = $view;
			} else {
				$_action = 'view';
			}
			
			$u = new User();
			
			$outputContent = false;
			$useCache = false;
			$page = Page::getCurrentPage();
			
			if ($view == 'view') {
				if ($this->controller->cacheBlockOutput() && ($obj instanceof Block) && (!$obj->isBlockInStack())) {
					if ((!$u->isRegistered() || ($this->controller->cacheBlockOutputForRegisteredUsers())) &&
						(($_SERVER['REQUEST_METHOD'] != 'POST' || ($this->controller->cacheBlockOutputOnPost() == true)))) {
							$useCache = true;
					}
					if ($useCache) {
						$cID = 0;
						if (is_object($this->area)) {
							$cID = $this->area->getCollectionID();
						} else if (is_object($page)) {
							$cID = $page->getCollectionID();
						}
						$outputContent = Cache::get('block_view_output', $cID . ':' . $obj->getBlockID() . ':' . $obj->getAreaHandle());
					}
				}
			}
			if ($outputContent == false) {
				$this->controller->setupAndRun($_action);
			}
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());
			$headerItems = $this->controller->headerItems;
			extract($args);
			
			if ($this->controller->getRenderOverride() != '') { 
				$_filename = $this->controller->getRenderOverride() . '.php';
			} 
			
			if ($view == 'scrapbook') {
				$template = $this->getBlockPath(FILENAME_BLOCK_VIEW_SCRAPBOOK) . '/' . FILENAME_BLOCK_VIEW_SCRAPBOOK;
				if (!file_exists($template)) {
					$view = 'view';
				}
			}
			
			if (!in_array($view, array('composer','view', 'add', 'edit', 'scrapbook'))) {
				// then we're trying to render a custom view file, which we'll pass to the bottom functions as $_filename
				$_filename = $view . '.php';
				$view = 'view';
			}
			
			switch($view) {
				case 'scrapbook':
					$header = DIR_FILES_ELEMENTS_CORE . '/block_header_view.php';
					$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_view.php';										
					break;
				case 'composer':
				case 'view':				
					if (!$outputContent) {
						if (!isset($_filename)) {
							$_filename = FILENAME_BLOCK_VIEW;
						}					
						$bvt = new BlockViewTemplate($obj);
						if ($bFilename) {
							$bvt->setBlockCustomTemplate($bFilename); // this is PROBABLY already set by the method above, but in the case that it's passed by area we have to set it here
						} else if ($_filename != FILENAME_BLOCK_VIEW) {
							$bvt->setBlockCustomRender($_filename); 
						}
						$template = $bvt->getTemplate();
					}
					
					if ($view == 'composer') {
						$displayEditLink = true;
						$header = DIR_FILES_ELEMENTS_CORE . '/block_header_composer.php';
						$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_composer.php';
						$cpFilename = $obj->getBlockComposerFilename();
						if ($cpFilename) {
							$cmpbase = $this->getBlockPath(DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename);
							if (file_exists($cmpbase . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename)) {
								$template = $base . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $cpFilename;
								$displayEditLink = false;
							}
						}
						
						if ($displayEditLink) {
							$cmpbase = $this->getBlockPath(FILENAME_BLOCK_COMPOSER);
							if (file_exists($cmpbase . '/' . FILENAME_BLOCK_COMPOSER)) {
								$template = $cmpbase . '/' . FILENAME_BLOCK_COMPOSER;
								$displayEditLink = false;
							}
						}
						
					} else {
						$header = DIR_FILES_ELEMENTS_CORE . '/block_header_view.php';
						$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_view.php';										
					}
					break;
				case 'add':
					if (!isset($_filename)) {
						$_filename = FILENAME_BLOCK_ADD;
					}
					$header = DIR_FILES_ELEMENTS_CORE . '/block_header_add.php';
					$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_add.php';
					break;
				case 'edit':
					if (!isset($_filename)) {
						$_filename = FILENAME_BLOCK_EDIT;
					}
					$header = DIR_FILES_ELEMENTS_CORE . '/block_header_edit.php';
					$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_edit.php';
					break;
			} 		
			
			if (!isset($template)) {
				$base = $this->getBlockPath($_filename);
				$template = $base . '/' . $_filename;
			}
						
			if (isset($header)) {
				include($header);
			}
			if ($outputContent) {
				print $outputContent;			
			} else if ($template) {
				
				ob_start();
				include($template);
				$outputContent = ob_get_contents();
				ob_end_clean();					
				print $outputContent;
				
				if ($useCache) {
					$cID = 0;
					if (is_object($this->area)) {
						$cID = $this->area->getCollectionID();
					} else if (is_object($page)) {
						$cID = $page->getCollectionID();
					}
					Cache::set('block_view_output', $cID . ':' . $obj->getBlockID() . ':' . $obj->getAreaHandle(), $outputContent, $this->controller->getBlockTypeCacheOutputLifetime());
				}
			}
			if (isset($footer)) {
				include($footer);
			}

			$this->template = $template;
			$this->header = $header;
			$this->footer = $footer;
			
			
		}
	}
	