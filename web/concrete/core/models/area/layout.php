<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AreaLayout extends Object {

	public static function getByID($arLayoutID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from AreaLayouts where arLayoutID = ?', array($arLayoutID));
		if (is_array($row) && $row['arLayoutID']) {
			$al = new AreaLayout();
			$al->setPropertiesFromArray($row);
			return $al;
		}
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

	public function getAreaLayoutColumns() {
		$db = Loader::db();
		$r = $db->Execute('select arLayoutColumnID from AreaLayoutColumns where arLayoutID = ? order by arLayoutColumnIndex asc', array($this->arLayoutID));
		$columns = array();
		while ($row = $r->FetchRow()) {
			$column = AreaLayoutColumn::getByID($row['arLayoutColumnID']);
			if (is_object($column)) {
				$column->setAreaLayoutObject($this);
				$columns[] = $column;
			}
		}
		return $columns;
	}

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

	public static function add($spacing = 0, $iscustom = false) {
		if (!$spacing) {
			$spacing = 0; // just in case
		}
		if (!$iscustom) {
			$iscustom = 0;
		} else {
			$iscustom = 1;
		}

		$db = Loader::db();
		$db->Execute('insert into AreaLayouts (arLayoutSpacing, arLayoutIsCustom) values (?, ?)', array($spacing, $iscustom));
		$arLayoutID = $db->Insert_ID();
		if ($arLayoutID) {
			$ar = AreaLayout::getByID($arLayoutID);
			return $ar;
		}
	}

}