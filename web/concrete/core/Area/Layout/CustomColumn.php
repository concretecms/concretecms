<?php
namespace Concrete\Core\Area\Layout;
use Loader;
class CustomColumn extends Column {

	public static function getByID($arLayoutColumnID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from AreaLayoutCustomColumns where arLayoutColumnID = ?', array($arLayoutColumnID));
		if (is_array($row) && $row['arLayoutColumnID']) {
			$al = new static();
			$al->loadBasicInformation($arLayoutColumnID);
			$al->setPropertiesFromArray($row);
			return $al;
		}
	}	

	public function duplicate($newAreaLayout) {
		$areaLayoutColumnID = parent::duplicate($newAreaLayout);
		$db = Loader::db();
		$v = array($areaLayoutColumnID, $this->arLayoutColumnWidth);
		$db->Execute('insert into AreaLayoutCustomColumns (arLayoutColumnID, arLayoutColumnWidth) values (?, ?)', $v);
		$newAreaLayoutColumn = CustomColumn::getByID($areaLayoutColumnID);
		return $newAreaLayoutColumn;
	}

    public function exportDetails($node)
    {
        $node->addAttribute('width', $this->arLayoutColumnWidth);
    }

	public function getAreaLayoutColumnClass() {
		return 'ccm-layout-column';
	}

	public function getAreaLayoutColumnWidth() {
		return $this->arLayoutColumnWidth;
	}

	public function setAreaLayoutColumnWidth($width) {
		$this->arLayoutColumnWidth = $width;
		$db = Loader::db();
		$db->Execute('update AreaLayoutCustomColumns set arLayoutColumnWidth = ? where arLayoutColumnID = ?', array($width, $this->arLayoutColumnID));
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute("delete from AreaLayoutCustomColumns where arLayoutColumnID = ?", array($this->arLayoutColumnID));
		parent::delete();
	}




}