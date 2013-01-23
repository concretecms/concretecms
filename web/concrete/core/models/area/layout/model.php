<?

defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_AreaLayout extends Object {

	public static function getByID($arLayoutID) {
		$db = Loader::db();
		$row = $db->GetRow('select arLayoutID, arLayoutUsesThemeGridFramework from AreaLayouts where arLayoutID = ?', array($arLayoutID));
		if (is_array($row) && $row['arLayoutID']) {
			if ($row['arLayoutUsesThemeGridFramework']) {
				$al = new ThemeGridAreaLayout();
			} else {
				$al = new CustomAreaLayout();
			}
			$al->setPropertiesFromArray($row);
			$al->loadDetails();
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
		

	public function isAreaLayoutUsingThemeGridFramework() {
		return $this->arLayoutUsesThemeGridFramework;
	}

	public function getAreaLayoutColumns() {
		$db = Loader::db();
		$r = $db->Execute('select arLayoutColumnID from AreaLayoutColumns where arLayoutID = ? order by arLayoutColumnIndex asc', array($this->arLayoutID));
		$columns = array();
		$class = Loader::helper('text')->camelcase($this->arLayoutType) . 'AreaLayoutColumn';
		while ($row = $r->FetchRow()) {
			$column = call_user_func_array(array($class, 'getByID'), array($row['arLayoutColumnID']));
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

	protected function addLayoutColumn() {
		$db = Loader::db();
		$index = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arLayoutID = ?', array($this->arLayoutID));
		$db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex) values (?, ?)', array($this->arLayoutID, $index));
		$arLayoutColumnID = $db->Insert_ID();
		return $arLayoutColumnID;
	}

	abstract static public function add();

	public function delete() {
		$columns = $this->getAreaLayoutColumns();
		foreach($columns as $col) {
			$col->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
	}

}