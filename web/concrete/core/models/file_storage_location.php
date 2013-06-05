<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileStorageLocation extends Object {

	const ALTERNATE_ID = 1;
	
	public function add($name, $directory, $forceID = false) {
		$db = Loader::db();
		if ($forceID) {
			$v = array($name, $directory, $forceID);
			$db->Execute('insert into FileStorageLocations (fslName, fslDirectory, fslID) values (?, ?, ?)', $v);
			$fsl = FileStorageLocation::getByID($forceID);
		} else {
			$v = array($name, $directory);
			$db->Execute('insert into FileStorageLocations (fslName, fslDirectory, fslID) values (?, ?)', $v);
			
			$id = $db->Insert_ID();
			$fsl = FileStorageLocation::getByID($id);
		}
		
		return $fsl;	
	}
	
	public function update($name, $directory) {
		$db = Loader::db();
		$db->Execute("update FileStorageLocations set fslName = ?, fslDirectory = ? where fslID = ?", array($name, $directory, $this->fslID));
	}
	
	public function getByID($id) {
		$db = Loader::db();
		$r = $db->GetRow("select * from FileStorageLocations where fslID = ?", array($id));
		if (is_array($r) && $r['fslID'] == $id) {
			$obj = new FileStorageLocation();
			$obj->setPropertiesFromArray($r);
			return $obj;
		}
	}
	
	public function getID() {return $this->fslID;}
	public function getName() {return $this->fslName;}
	public function getDirectory() {return $this->fslDirectory;}
}
