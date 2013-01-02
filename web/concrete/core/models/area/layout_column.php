<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AreaLayoutColumn extends Object {

	public static function getByID($arLayoutColumnID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from AreaLayoutColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
		if (is_array($row) && $row['arLayoutColumnID']) {
			$al = new AreaLayoutColumn();
			$al->setPropertiesFromArray($row);
			return $al;
		}
	}	

	public function setAreaLayoutObject($arLayout) {
		$this->arLayout = $arLayout;
	}

	public function getAreaLayoutObject() {
		return $this->arLayout;
	}

	public function getAreaLayoutColumnIndex() {
		return $this->arLayoutColumnIndex;
	}
		
	public function getAreaLayoutID() {
		return $this->arLayoutID;
	}

	public function getAreaID() {
		return $this->arID;
	}

	public function getAreaLayoutColumnID() {
		return $this->arLayoutColumnID;
	}

	public function getAreaLayoutColumnWidth() {
		return $this->arLayoutColumnWidth;
	}


	public function display($c) {
		$layout = $this->getAreaLayoutObject();
		$a = $layout->getAreaObject();
		$as = new SubArea($this->getAreaLayoutColumnIndex(), $a);
		$as->display($c);
		if (!$this->getAreaID()) {
			$db = Loader::db();
			$db->Execute('update AreaLayoutColumns set arID = ? where arLayoutColumnID = ?', array($as->getAreaID(), $this->arLayoutColumnID));
		}
	}


}