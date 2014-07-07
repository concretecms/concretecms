<?
namespace Concrete\Block\CoreAreaLayout;
use Loader;
use \Concrete\Core\Block\BlockController;
use \Concrete\Core\Area\Layout\Layout as AreaLayout;
use \Concrete\Core\Area\Layout\Preset as AreaLayoutPreset;
use \Concrete\Core\Area\Layout\CustomLayout as CustomAreaLayout;
use \Concrete\Core\Area\Layout\ThemeGridLayout as ThemeGridAreaLayout;
use \Concrete\Core\Asset\CSSAsset;
use URL;
use Page;

class Controller extends BlockController {

		protected $btCacheBlockRecord = false;
		protected $btSupportsInlineAdd = true;		
		protected $btSupportsInlineEdit = true;		
		protected $btTable = 'btCoreAreaLayout';
		protected $btIsInternal = true;

		public function getBlockTypeDescription() {
			return t("Proxy block for area layouts.");
		}
		
		public function getBlockTypeName() {
			return t("Area Layout");
		}

		public function duplicate($newBID) {
			$db = Loader::db();
			parent::duplicate($newBID);
			$ar = AreaLayout::getByID($this->arLayoutID);
			$nr = $ar->duplicate();
			$db->Execute('update btCoreAreaLayout set arLayoutID = ? where bID = ?', array($nr->getAreaLayoutID(), $newBID));
		}

		public function getAreaLayoutObject() {
			if ($this->arLayoutID) {
				$arLayout = AreaLayout::getByID($this->arLayoutID);
				return $arLayout;
			}
		}

		public function delete() {
			$arLayout = $this->getAreaLayoutObject();
			if (is_object($arLayout)) {
				$arLayout->delete();
			}
			parent::delete();
		}

		public function save($post) {
			$db = Loader::db();
			$arLayoutID = $db->GetOne('select arLayoutID from btCoreAreaLayout where bID = ?', array($this->bID));
			if (!$arLayoutID) {
				$arLayout = $this->addFromPost($post);
			} else {

				$arLayout = AreaLayout::getByID($arLayoutID);
				// save spacing
				if ($arLayout->isAreaLayoutUsingThemeGridFramework()) {
					$columns = $arLayout->getAreaLayoutColumns();
					for ($i = 0; $i < count($columns); $i++) {
						$col = $columns[$i];
						$span = ($post['span'][$i]) ? $post['span'][$i] : 0;
						$offset = ($post['offset'][$i]) ? $post['offset'][$i] : 0;
						$col->setAreaLayoutColumnSpan($span);
						$col->setAreaLayoutColumnOffset($offset);
					}

				} else {
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
			$values = array('arLayoutID' => $arLayout->getAreaLayoutID());
			parent::save($values);
		}

		public function addFromPost($post) {
			// we are adding a new layout 
			switch($post['gridType']) {
				case 'TG':
					$arLayout = ThemeGridAreaLayout::add();
					$arLayout->setAreaLayoutMaxColumns($post['arLayoutMaxColumns']);
					for ($i = 0; $i < $post['themeGridColumns']; $i++) {
						$span = ($post['span'][$i]) ? $post['span'][$i] : 0;
						$offset = ($post['offset'][$i]) ? $post['offset'][$i] : 0;
						$column = $arLayout->addLayoutColumn();
						$column->setAreaLayoutColumnSpan($span);
						$column->setAreaLayoutColumnOffset($offset);
					}
					break;
				case 'FF':
					if ((!$post['isautomated']) && $post['columns'] > 1) {
						$iscustom = 1;
					} else {
						$iscustom = 0;
					}
					$arLayout = CustomAreaLayout::add($post['spacing'], $iscustom);
					for ($i = 0; $i < $post['columns']; $i++) {
						$width = ($post['width'][$i]) ? $post['width'][$i] : 0;
						$column = $arLayout->addLayoutColumn();
						$column->setAreaLayoutColumnWidth($width);
					}
					break;
				default: // a preset
					$arLayoutPreset = AreaLayoutPreset::getByID($post['gridType']);
					$arLayout = $arLayoutPreset->getAreaLayoutObject();
					$arLayout = $arLayout->duplicate();
					break;
			}
			return $arLayout;
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
					$asset = new CSSAsset();
					$asset->setAssetURL(URL::to('/ccm/system/css/layout', $this->bID));
					$asset->setAssetSupportsMinification(false);
					$this->requireAsset($asset);
					$this->render('view');
				}
			} else {
				$this->set('columns', array());
			}
		}

		public function edit() {
			$this->addHeaderItem(Loader::helper('html')->javascript('layouts.js'));
			$this->view();
			// since we set a render override in view() we have to explicitly declare edit
			$this->set('enableThemeGrid', $this->arLayout->isAreaLayoutUsingThemeGridFramework());
			if ($this->arLayout->isAreaLayoutUsingThemeGridFramework()) {
				$c = Page::getCurrentPage();
				$pt = $c->getCollectionThemeObject();
				$gf = $pt->getThemeGridFrameworkObject();
				$this->set('themeGridFramework', $gf);
				$this->set('themeGridMaxColumns', $this->arLayout->getAreaLayoutMaxColumns());
				$this->set('themeGridName', $gf->getPageThemeGridFrameworkName());
				$this->render("edit_grid");
			} else {
				$this->set('spacing', $this->arLayout->getAreaLayoutSpacing());
				$this->set('iscustom', $this->arLayout->hasAreaLayoutCustomColumnWidths());
				$this->set('maxColumns', 12);
				$this->render('edit');
			}
			$this->set('columnsNum', count($this->arLayout->getAreaLayoutColumns()));

		}

		public function add() {
			$this->addHeaderItem(Loader::helper('html')->javascript('layouts.js'));
			$maxColumns = 12; // normally
			// now we check our active theme and see if it has other plans
			$c = Page::getCurrentPage();
			$pt = $c->getCollectionThemeObject();
			if (is_object($pt) && $pt->supportsGridFramework() && is_object($this->area) && $this->area->getAreaGridMaximumColumns()) {
				$gf = $pt->getThemeGridFrameworkObject();
				$this->set('enableThemeGrid', true);
				$this->set('themeGridName', $gf->getPageThemeGridFrameworkName());
				$this->set('themeGridFramework', $gf);
				$this->set('themeGridMaxColumns', $this->area->getAreaGridMaximumColumns());
			} else {
				$this->set('enableThemeGrid', false);
			}
			$this->set('columnsNum', 1);
			$this->set('maxColumns', $maxColumns);
		}


	}