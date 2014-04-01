<?php
namespace Concrete\Core\Area\Layout;
use \Concrete\Core\Foundation\Object;
abstract class Layout extends Object {

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
			$al->arLayoutNumColumns = $db->GetOne('select count(arLayoutColumnID) as totalColumns from AreaLayoutColumns where arLayoutID = ?', array($arLayoutID));
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

	public function getAreaLayoutNumColumns() {
		return $this->arLayoutNumColumns;
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

	protected function addLayoutColumn() {
		$db = Loader::db();
		$arLayoutColumnDisplayID = $db->GetOne('select max(arLayoutColumnDisplayID) as arLayoutColumnDisplayID from AreaLayoutColumns');
		if ($arLayoutColumnDisplayID) {
			$arLayoutColumnDisplayID++;
		} else {
			$arLayoutColumnDisplayID = 1;
		}
		$index = $db->GetOne('select count(arLayoutColumnID) from AreaLayoutColumns where arLayoutID = ?', array($this->arLayoutID));
		$db->Execute('insert into AreaLayoutColumns (arLayoutID, arLayoutColumnIndex, arLayoutColumnDisplayID) values (?, ?, ?)', array($this->arLayoutID, $index, $arLayoutColumnDisplayID));
		$arLayoutColumnID = $db->Insert_ID();
		return $arLayoutColumnID;
	}

	abstract public function duplicate();
	abstract static public function add();

	public function delete() {
		$columns = $this->getAreaLayoutColumns();
		foreach($columns as $col) {
			$col->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from AreaLayouts where arLayoutID = ?', array($this->arLayoutID));
		$db->Execute('delete from AreaLayoutPresets where arLayoutID = ?', array($this->arLayoutID));
	}

}