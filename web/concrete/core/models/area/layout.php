<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AreaLayout extends Object {

	protected $gf;

	public static function getByID($arLayoutID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from AreaLayouts where arLayoutID = ?', array($arLayoutID));
		if (is_array($row) && $row['arLayoutID']) {
			$al = new AreaLayout();
			$al->setPropertiesFromArray($row);


			$c = Page::getCurrentPage();
			if (is_object($c)) {
				$pt = $c->getCollectionThemeObject();
				if (is_object($pt) && $pt->supportsGridFramework()) {
					$gf = $pt->getThemeGridFrameworkObject();
					$al->setThemeGridFrameworkObject($gf);
				}
			}

			return $al;
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

	public function getAreaLayoutColumns() {
		$db = Loader::db();
		$r = $db->Execute('select arLayoutColumnID from AreaLayoutColumns where arLayoutID = ? order by arLayoutColumnIndex asc', array($this->arLayoutID));
		$columns = array();
		while ($row = $r->FetchRow()) {
			if ($this->isAreaLayoutUsingThemeGridFramework) {
				$column = AreaLayoutThemeGridColumn::getByID($row['arLayoutColumnID']);
			} else {
				$column = AreaLayoutCustomColumn::getByID($row['arLayoutColumnID']);
			}

			if (is_object($column)) {
				$column->setAreaLayoutObject($this);
				$columns[] = $column;
			}
		}
		return $columns;
	}

	/*
	public function duplicate() {
		$db = Loader::db();
		$v = array($this->arLayoutSpacing, $this->arLayoutIsCustom);
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom) values (?, ?)', $v);
		$newAreaLayoutID = $db->Insert_ID();
		if ($newAreaLayoutID) {
			$columns = $this->getAreaLayoutColumns();
			foreach($columns as $col) {
				$v = array($newAreaLayoutID, $col->getAreaLayoutColumnIndex(), 0, $col->getAreaLayoutColumnWidth());
				$db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arID, arLayoutColumnWidth) values (?, ?, ?, ?)', $v);
			}
			return AreaLayout::getByID($newAreaLayoutID);
		}
	}
	*/

	public function addLayoutColumn($width = 0) {
		if (!$width) {
			$width = 0; // just in case
		}
		$db = Loader::db();
		$index = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arLayoutID = ?', array($this->arLayoutID));
		$db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnWidth) values (?, ?, ?)', array($this->arLayoutID, $index, $width));
		$arLayoutColumnID = $db->Insert_ID();
		if ($arLayoutID) {
			$arc = AreaLayoutColumn::getByID($arLayoutColumnID);
			return $arc;
		}
	}

	public function setAreaLayoutColumnSpacing($spacing) {
		if (!$spacing) {
			$spacing = 0;
		}
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutSpacing = ? where arLayoutID = ?', array($spacing, $this->arLayoutID));
		$this->arLayoutSpacing = $spacing;
	}

	public function enableAreaLayoutCustomColumnWidths() {
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(1, $this->arLayoutID));
		$this->arLayoutIsCustom = true;
	}

	public function disableAreaLayoutCustomColumnWidths() {
		$db = Loader::db();
		$db->Execute('update AreaLayouts set arLayoutIsCustom = ? where arLayoutID = ?', array(0, $this->arLayoutID));
		$this->arLayoutIsCustom = false;
	}


	public static function add($spacing = 0, $iscustom = false, $usegrid = false) {
		if (!$spacing) {
			$spacing = 0; // just in case
		}
		if (!$iscustom) {
			$iscustom = 0;
		} else {
			$iscustom = 1;
		}

		if (!$usegrid) {
			$arLayoutUsesThemeGridFramework = 0;
		} else {
			$arLayoutUsesThemeGridFramework = 1;
		}

		$db = Loader::db();
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom, arLayoutUsesThemeGridFramework) values (?, ?, ?)', array($spacing, $iscustom, $arLayoutUsesThemeGridFramework));
		$arLayoutID = $db->Insert_ID();
		if ($arLayoutID) {
			$ar = AreaLayout::getByID($arLayoutID);
			return $ar;
		}
	}

	public function delete() {
		$columns = $this->getAreaLayoutColumns();
		foreach($columns as $col) {
			$col->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
	}

}