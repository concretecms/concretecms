<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * User associations
 *
 * @package Users
 * @category Concrete
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Concrete5_Model_UsersFriends extends Object {  
	/**
	* Get data from a users friends
	* @param int $uID
	* @param string $sortBy
	* @return array
	*/
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
	/**
	* Check if a user is friends with another
	* $friendUID is the user id of the person you want to check
	* $uID is the user id of the person you are checking from
	* @param int $friendUID
	* @param int $uID
	* @return bool
	*/
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
	/**
	* Adds a user as a friend to another
	* $friendUID is the person you want to add as a friend
	* $uID is the person that is friending $friendUID
	* @param int $friendUID
	* @param int $uID
	* @return bool
	*/
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
		Events::fire('on_user_friend_add', $uID, $friendUID);
		return true;
	}	
	/**
	* removes a user as a friend to another
	* $friendUID is the person you want to remove as a friend
	* $uID is the person that is un-friending $friendUID
	* @param int $friendUID
	* @param int $uID
	* @return bool
	*/
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
		$ret = Events::fire('on_user_friend_remove', $uID, $friendUID);
		if($ret < 0) {
			return;
		}
		$db->query($sql,$vals); 
		return true;
	}		
}

?>