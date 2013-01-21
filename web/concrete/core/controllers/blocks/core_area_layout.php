<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreAreaLayout extends BlockController {

		protected $btCacheBlockRecord = false;
		protected $btSupportsInlineEditing = true;		
		protected $btTable = 'btCoreAreaLayout';
		protected $btIsInternal = true;

		public function getBlockTypeDescription() {
			return t("Proxy block for area layouts.");
		}
		
		public function getBlockTypeName() {
			return t("Area Layout (Core)");
		}

		public function duplicate($newBID) {
			$ar = AreaLayout::getByID($this->arLayoutID);
			$nr = $ar->duplicate();
			$this->record->arLayoutID = $nr->getAreaLayoutID();
			parent::duplicate($newBID);
		}

		public function getAreaLayoutObject() {
			if ($this->arLayoutID) {
				$arLayout = AreaLayout::getByID($this->arLayoutID);
				return $arLayout;
			}
		}

		public function delete() {
			$arLayout = $this->getAreaLayoutObject();
			$arLayout->delete();
			parent::delete();
		}

		public function save($post) {
			$db = Loader::db();
			$arLayoutID = $db->GetOne('select arLayoutID from btCoreAreaLayout where bID = ?', array($this->bID));
			if (!$arLayoutID) {
				// we are adding a new layout 
				if ($post['useThemeGrid']) {
					$arLayout = AreaLayout::add(false, false, true);
					for ($i = 0; $i < $post['themeGridColumns']; $i++) {
						$span = ($post['span'][$i]) ? $post['span'][$i] : 0;
						$arLayout->addLayoutColumn($span);
					}
				} else {
					if (!$post['isautomated']) {
						$iscustom = 1;
					} else {
						$iscustom = 0;
					}
					$arLayout = AreaLayout::add($post['spacing'], $iscustom);
					for ($i = 0; $i < $post['columns']; $i++) {
						$width = ($post['width'][$i]) ? $post['width'][$i] : 0;
						$arLayout->addLayoutColumn($width);
					}
				}
			} else {
				$arLayout = AreaLayout::getByID($arLayoutID);
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
			$values = array('arLayoutID' => $arLayout->getAreaLayoutID());
			parent::save($values);
		}

		public function view() {
			$b = $this->getBlockObject();
			$a = $b->getBlockAreaObject();
			$this->arLayout = $this->getAreaLayoutObject();
			if (is_object($this->arLayout)) {
				$this->arLayout->setAreaObject($a);
				$this->set('columns', $this->arLayout->getAreaLayoutColumns());
				if ($this->arLayout->isAreaLayoutUsingThemeGridFramework()) {
					$this->render('view_grid');
				} else {
					$this->render('view');
				}
			} else {
				$this->set('columns', array());
			}
		}

		public function edit() {
			$this->view();
			$this->set('spacing', $this->arLayout->getAreaLayoutSpacing());
			$this->set('iscustom', $this->arLayout->hasAreaLayoutCustomColumnWidths());
		}

		public function add() {
			$maxColumns = 12; // normally
			// now we check our active theme and see if it has other plans
			$c = Page::getCurrentPage();
			$pt = $c->getCollectionThemeObject();
			if (is_object($pt) && $pt->supportsGridFramework() && is_object($this->area) && $this->area->getAreaGridColumnSpan()) {
				$gf = $pt->getThemeGridFrameworkObject();
				$this->set('enableThemeGrid', true);
				$this->set('themeGridName', $gf->getPageThemeGridFrameworkName());
				$this->Set('themeGridFramework', $gf);
				$this->set('themeGridMaxColumns', $this->area->getAreaGridColumnSpan());
			} else {
				$this->set('enableThemeGrid', false);
			}

			$this->set('maxColumns', $maxColumns);
		}

		public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->css(REL_DIR_FILES_TOOLS_REQUIRED . '/area/layout.css?bID=' . $this->bID));
		}


	}