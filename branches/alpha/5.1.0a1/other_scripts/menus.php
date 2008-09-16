<?

class CCMMenuNode {

	function getID() {return $this->mID;}
	function getDisplayName() {return $this->mDisplayName;}
	function getLink() {return $this->mLink;}
	function getParentID() {return $this->mParentID;}
	function getDisplayOrder() {return $this->mDisplayOrder;}
	function getNumChildren() {return $this->mNumChildren;}
	function getHandle() {return $this->mHandle;}
	
	function get($mID) {
		global $db;
		$v = array($mID);
		$r = $db->query("select Menus.* from Menus where mID = ?", $v);
		$row = $r->fetchRow();
		if (is_array($row)) {
			$cm = new CCMMenuNode;
			foreach ($row as $key => $value) {
				$cm->{$key} = $value;	
			}
			$total = $db->getOne("select count(mID) as total from Menus where mParentID = ?", $v);
			$cm->mNumChildren = 0;
			if (!PEAR::isError($total) && $total > 0) {
				$cm->mNumChildren = $total;
			}
			return $cm;
		}
	}
	
	function setParent($obj) {
		global $db;
		$v = array($obj->getID(), $this->mID);
		$db->query("update Menus set mParentID = ?, mDisplayOrder = 99999 where mID = ?", $v);
		
		$this->rescanDisplayOrder();
		$obj->rescanDisplayOrder();
	}
	
	function copy($obj) {
		global $db;
		$v = array($this->getDisplayName(), $this->getLink(), $this->getHandle(), $obj->getID(), 99999);
		$db->query("insert into Menus (mDisplayName, mLink, mHandle, mParentID, mDisplayOrder) values (?, ?, ?, ?, ?)", $v);
		
		$this->rescanDisplayOrder();
		$obj->rescanDisplayOrder();
	}
	
	function rescanDisplayOrder() {
		$do = 0;
		global $db;
		$q = "select mID from Menus where mParentID = {$this->mID} order by mDisplayOrder asc";
		$r = $db->query($q);
		while ($row = $r->fetchRow()) {
			$db->query("update Menus set mDisplayOrder = {$do} where mID = {$row['mID']}");
			$do++;
		}
	}
	
	function getSubNodeIDs($mParentID) {
		global $db;
		$v = array($mParentID);
		$r = $db->query("select mID from Menus where mParentID = ? order by mDisplayOrder asc", $v);
		$mids = array();
		while ($row = $r->fetchRow()) {
			$mids[] = $row['mID'];
		}
		return $mids;
	}
	
	function add($mDisplayName, $mLink, $mHandle, $mParentID) {
		global $db;
		$v = array($mParentID);
		$do = $db->getOne("select max(mDisplayOrder) as md from Menus where mParentID = ?", $v);
		$displayOrder = 0;
		if (!PEAR::isError($do) && is_numeric($do)) {
			if ($do > 0) {
				$displayOrder = $do + 1;
			}
		}
		$v = array($mDisplayName, $mLink, $mHandle, $mParentID, $displayOrder);
		$r = $db->query("insert into Menus (mDisplayName, mLink, mHandle, mParentID, mDisplayOrder) values (?, ?, ?, ?, ?)", $v);
	}
	
	function update($mDisplayName, $mLink, $mHandle) {
		global $db; 
		$v = array($mDisplayName, $mLink, $mHandle, $this->mID);
		$db->query("update Menus set mDisplayName = ?, mLink = ?, mHandle = ? where mID = ?", $v);
	}
	
	function delete() {
		global $db;
		$v = array($this->mID);
		$db->query("delete from Menus where mID = ?", $v);
	}
}

?>