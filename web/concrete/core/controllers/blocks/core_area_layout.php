<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreAreaLayout extends BlockController {

		protected $btCacheBlockRecord = false;
		protected $btSupportsInlineEditing = true;		
		protected $btTable = 'btCoreAreaLayout';
		/* protected $btIsInternal = true; */

		public function getBlockTypeDescription() {
			return t("Proxy block for area layouts.");
		}
		
		public function getBlockTypeName() {
			return t("Area Layout (Core)");
		}

		public function on_start() {
			$this->arLayout = AreaLayout::getByID($this->arLayoutID);
		}

		public function getAreaLayoutObject() {
			if (!is_object($this->arLayout)) {
				$this->on_start();
			}
			return $this->arLayout;
		}

		public function save($post) {
			if (!$post['arLayoutID']) {
				// we are adding a new layout 

			
				if (!$post['isautomated']) {
					$iscustom = 1;
				} else {
					$iscustom = 0;
				}
				$ar = AreaLayout::add($post['spacing'], $iscustom);
				for ($i = 0; $i < $post['columns']; $i++) {
					$width = ($post['width'][$i]) ? $post['width'][$i] : 0;
					$ar->addLayoutColumn($width);
				}
				$values = array('arLayoutID' => $ar->getAreaLayoutID());
				parent::save($values);
			} else {
				$arLayout = AreaLayout::getByID($post['arLayoutID']);
				// save spacing
				$arLayout->setAreaLayoutColumnSpacing($post['spacing']);
				if ($post['isautomated']) {
					$arLayout->disableAreaLayoutCustomColumnWidths();
				} else {
					$arLayout->enableAreaLayoutCustomColumnWidths();
					$columns = $arLayout->getAreaLayoutColumns();
					for ($i = 0; $i < count($columns); $i++) {
						$col = $columns[$i];
						$width = ($post['width'][$i]) ? $post['width'][$i] : 0;
						$col->setAreaLayoutColumnWidth($width);
					}
				}
			}

		}

		public function view() {
			$b = $this->getBlockObject();
			$a = $b->getBlockAreaObject();
			$this->arLayout->setAreaObject($a);
			$this->set('columns', $this->arLayout->getAreaLayoutColumns());
		}

		public function edit() {
			$this->view();
			$this->set('spacing', $this->arLayout->getAreaLayoutSpacing());
			$this->set('iscustom', $this->arLayout->hasAreaLayoutCustomColumnWidths());
		}

		public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->css(REL_DIR_FILES_TOOLS_REQUIRED . '/area/layout.css?bID=' . $this->bID));
		}


	}