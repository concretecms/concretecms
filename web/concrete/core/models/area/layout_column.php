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

	public function getAreaLayoutColumnIndex() {
		return $this->arLayoutColumnIndex;
	}
		
	public function getAreaLayoutID() {
		return $this->arLayoutID;
	}

	public function getAreaLayoutColumnID() {
		return $this->arLayoutColumnID;
	}

	public function getAreaLayoutColumnWidth() {
		return $this->arLayoutColumnWidth;
	}

}