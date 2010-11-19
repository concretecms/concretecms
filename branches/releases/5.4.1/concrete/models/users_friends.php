<?php 

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Users
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * User associations
 *
 * @package Users
 * @category Concrete
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class UsersFriends extends Object {  

	static function getUsersFriendsData($uID=0, $sortBy='uf.uDateAdded DESC'){ 
		if( !intval($uID) ){
			$u = new User();
			if(!$u || !intval($u->uID)) return false;
			$uID=$u->uID;
		}
		$db = Loader::db();	
		$vals = array( $uID);
		$sql = 'SELECT uf.* FROM UsersFriends AS uf, Users AS u WHERE u.uID=uf.uID AND uf.uID=? ORDER BY '.$sortBy; 
		return $db->getAll( $sql, $vals );  
	}
	
	static function isFriend($friendUID,$uID=0){
		if( !intval($friendUID) ) return false;
		if( !intval($uID) ){
			$u = new User();
			if(!$u || !intval($u->uID)) return false;
			$uID=$u->uID;
		}
		$db = Loader::db();	
		$vals = array( $friendUID, $uID);
		$sql = 'SELECT count(*) FROM UsersFriends WHERE friendUID=? AND uID=?'; 
		$count = $db->getOne( $sql, $vals );  
		if( intval($count) ) return true;
		return false;
	}	
	
	static function addFriend( $friendUID, $uID=0, $status=''){
		if( !intval($friendUID) ) return false;
		if( !intval($uID) ){
			$u = new User();
			if(!$u || !intval($u->uID)) return false;
			$uID=$u->uID;
		}
		$db = Loader::db();			
		if( UsersFriends::isFriend( $friendUID, $uID ) ){
			$vals = array( $status, $friendUID, $uID );
			$sql = 'UPDATE UsersFriends SET status=? WHERE friendUID=? AND uID=?'; 
		}else{ 
			$vals = array( $friendUID, $uID, $status, date("Y-m-d H:i:s")); 
			$sql = 'INSERT INTO UsersFriends ( friendUID, uID, status, uDateAdded ) values (?, ?, ?, ?)'; 
		}			
		$db->query($sql,$vals); 
		return true;
	}	
	
	static function removeFriend($friendUID,$uID=0){
		if( !intval($friendUID) ) return false;
		if( !intval($uID) ){
			$u = new User();
			if(!$u || !intval($u->uID)) return false;
			$uID=$u->uID;
		}
		$db = Loader::db();	 
		$vals = array( $friendUID, $uID);
		$sql = 'DELETE FROM UsersFriends WHERE friendUID=? AND uID=?'; 
		$db->query($sql,$vals); 
		return true;
	}		
}

?>