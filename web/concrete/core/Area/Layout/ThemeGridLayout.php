<?php
namespace Concrete\Core\Area\Layout;
class ThemeGridLayout extends Layout {

	protected $arLayoutType = 'theme_grid';
	protected $arLayoutMaxColumns;
	protected $gf;

	protected function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select arLayoutMaxColumns from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
		$this->setPropertiesFromArray($row);

		$c = Page::getCurrentPage();
		if (is_object($c)) {
			$pt = $c->getCollectionThemeObject();
			if (is_object($pt) && $pt->supportsGridFramework()) {
				$gf = $pt->getThemeGridFrameworkObject();
				$this->setThemeGridFrameworkObject($gf);
			}
		}
	}

	public function setThemeGridFrameworkObject($gf) {
		$this->gf = $gf;
	}

	public function getThemeGridFrameworkObject() {
		return $this->gf;
	}

	public function setAreaObject(Area $a) {
		$this->area = $a;
	}

	public function getAreaObject() {
		return $this->area;
	}

	public function getAreaLayoutID() {
		return $this->arLayoutID;
	}
		
	public function getAreaLayoutSpacing() {
		return $this->arLayoutSpacing;
	}

	public function hasAreaLayoutCustomColumnWidths() {
		return $this->arLayoutIsCustom;
	}

	public function isAreaLayoutUsingThemeGridFramework() {
		return $this->arLayoutUsesThemeGridFramework;
	}

	public function duplicate() {
		$db = Loader::db();
		$v = array($this->arLayoutMaxColumns, 1);
		$db->Execute('insert into AreaLayouts (arLayoutMaxColumns, arLayoutUsesThemeGridFramework) values (?, ?)', $v);
		$newAreaLayoutID = $db->Insert_ID();
		if ($newAreaLayoutID) {
			$newAreaLayout = AreaLayout::getByID($newAreaLayoutID);
			$columns = $this->getAreaLayoutColumns();
			foreach($columns as $col) {
				$col->duplicate($newAreaLayout);
			}
			return $newAreaLayout;
		}
	}

	public function setAreaLayoutMaxColumns($max) {
		if (!$max) {
			$max = 0;
		}
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutMaxColumns = ? where arLayoutID = ?', array($max, $this->arLayoutID));
		$this->arLayoutMaxColumns = $max;
	}

	public function getAreaLayoutMaxColumns() {
		return $this->arLayoutMaxColumns;
	}

	public function addLayoutColumn() {
		$columnID = parent::addLayoutColumn();
		$db = Loader::db();
		$db->Execute('insert into AreaLayoutThemeGridColumns (arLayoutColumnID) values (?)', array($columnID));
		return ThemeGridAreaLayoutColumn::getByID($columnID);
	}

	public static function add() {
		$db = Loader::db();
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom, arLayoutUsesThemeGridFramework) values (?, ?, ?)', array(0, 0, 1));
		$arLayoutID = $db->Insert_ID();
		if ($arLayoutID) {
			$ar = ThemeGridAreaLayout::getByID($arLayoutID);
			return $ar;
		}
	}


}