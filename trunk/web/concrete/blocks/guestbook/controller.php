<?
/**
 * @package Blocks
 * @subpackage BlockTypes
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * Controller for the guestbook block, which allows site owners to add comments onto any concrete page.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class GuestBookBlockController extends BlockController {
		
		/** 
		* @var object
		*/
		var $pobj;
		
		protected $btDescription = "Adds blog-style comments (a guestbook) to your page.";
		protected $btName = "Guestbook";
		protected $btTable = 'btGuestBook';
		protected $btInterfaceWidth = "300";
		protected $btInterfaceHeight = "260";	
		
		protected $btIncludeAll = 1;
			
		function delete() {
			$E = new GuestBookBlockEntry($this->bID);
			$E->removeAllEntries( $this->pobj->getBlockCollectionID() );
			parent::delete();
		}
		
		/**
		 * returns the title
		 * @return string $title
		*/
		function getTitle() {
			return $this->title;
		}


		/**
		 * returns wether or not to require approval
		 * @return bool
		*/
		function getRequireApproval() {
			return $this->requireApproval;
		}


		/**
		 * returns the bool to display the form
		 * @return bool
		*/
		function getDisplayGuestBookForm() {
			return $this->displayGuestBookForm;
		}
		
		/** 
		 * Handles the form post for adding a new guest book entry
		 *
		*/	
		function action_form_save_entry() {			
			// get the cID from the block Object
			$cID = $this->pobj->getBlockCollectionID();
		
			$v = Loader::helper('validation/strings');
			$errors = array();
			
			if(!$v->email($_POST['email'])) {
				$errors['email'] = "- invalid email address";
			}
			if(!$v->notempty($_POST['name'])) {
				$errors['name'] = "- name is required";
			}
			if(!$v->notempty($_POST['commentText'])) {
				$errors['commentText'] = "- a comment is required";
			}
			
			if(count($errors)) {
				$E = new GuestBookBlockEntry($this->bID);
				$E->user_name = $_POST['name'];
				$E->user_email = $_POST['email'];
				$E->commentText = $_POST['commentText'];
				
				$E->entryID = ($_POST['entryID']?$_POST['entryID']:NULL);
				
				$this->set('response', 'Please correct the following errors:');
				$this->set('errors',$errors);
				$this->set('Entry',$E);	
			} else {
				$E = new GuestBookBlockEntry($this->bID);
				if($_POST['entryID']) { // update
					$bp = $this->getPermissionsObject(); 
					if($bp->canWrite()) {
						$E->updateEntry($_POST['entryID'], $_POST['commentText'], $_POST['name'], $_POST['email']);
						$this->set('response', 'The comment has been saved');
					} else {
						$this->set('response', 'An Error occured while saving the comment');
						return true;
					}
				} else { // add			
					$E->addEntry($_POST['commentText'], $_POST['name'], $_POST['email'], (!$this->requireApproval), $cID);	
					$this->set('response', 'Thanks! Your comment has been posted.');
				}
			}
			return true;
		}
		
		
		/** 
		 * gets a list of all guestbook entries for the current block
		 *
		 * @param string $order ASC|DESC
		 * @return array
		*/
		function getEntries($order = "ASC") {
			return GuestBookBlockEntry::getAll($this->bID, $this->pobj->getBlockCollectionID(), $order);
		}
		
		
		/** 
		 * Loads a guestbook entry and sets the $Entry GuestBookBlockEntry object instance for use by the view
		 *
		 * @return bool
		*/
		function action_loadEntry() {
			$Entry = new GuestBookBlockEntry($this->bID);
			$Entry->loadData($_GET['entryID']);
			$this->set('Entry',$Entry);
			return true;
		}	
		
		/** 
		 * deltes a given Entry, sets the response message for use in the view
		 *
		*/
		function action_removeEntry() {
			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->removeEntry($_GET['entryID']);
				$this->set('response', 'The comment has been removed');
			}
		}
	
	
	
		/** 
		 * deltes a given Entry, sets the response message for use in the view
		 *
		*/
		function action_approveEntry() {
			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->approveEntry($_GET['entryID']);
				$this->set('response', 'The comment has been approved');
			}
		}
		
		/** 
		 * deltes a given Entry, sets the response message for use in the view
		 *
		*/
		function action_unApproveEntry() {
			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->unApproveEntry($_GET['entryID']);
				$this->set('response', 'The comment has been set to not approved');
			}
		}
	
	} // end class def
	
	
	/** 
	 * Manages indevidual guestbook entries
	 */ 
	class GuestBookBlockEntry {
		/**
		 * blocks bID
		 * @var integer
		*/
		var $bID;
		/**
		 * the entry id
		 * @var integer
		*/
		var $entryID;
		/**
		 * the user's name
		 * @var string
		*/
		var $user_name;
		/**
		 * the user's email address
		 * @var string
		*/var $user_email;
		
		/**
		 * the text for the comment
		 * @var string
		*/
		var $commentText;
		
		function __construct($bID) {
			$this->bID = $bID;
		}
		
		/** 
		 * Loads the object data from the db
		 * @param integer $entryID
		 * @return bool
		*/
		function loadData($entryID) {
			$db = Loader::db();
			$data = $db->getRow("SELECT * FROM btGuestBookEntries WHERE entryID=? AND bID=?",array($entryID,$this->bID));
		
			$this->entryID 		= $data['entryID'];
			$this->user_name 	= $data['user_name'];
			$this->user_email 	= $data['user_email'];
			$this->commentText 	= $data['commentText'];
		}
		
		/** 
		 * Adds an entry to the guestbook for the current block
		 * @param string $comment
		 * @param string $name
		 * @param string $email
		*/
 		function addEntry($comment, $name, $email, $approved, $cID) {
			$txt = Loader::helper('text');
 		
			$db = Loader::db();
			$query = "INSERT INTO btGuestBookEntries (bID, cID, user_name, user_email, commentText, approved) VALUES (?, ?, ?, ?, ?, ?)";
			$res = $db->query($query, array($this->bID, $cID, $txt->sanitize($name), $txt->sanitize($email), $txt->sanitize($comment),$approved) );
		}
		
		
		/** 
		 * Updates the given guestbook entry for the current block
		 * @param integer $entryID
		 * @param string $comment
		 * @param string $name
		 * @param string $email
		*/
	 	function updateEntry($entryID, $comment, $name, $email) {
			$db = Loader::db();
			$txt = Loader::helper('text');
			$query = "UPDATE btGuestBookEntries SET user_name=?, user_email=?, commentText=? WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($txt->sanitize($name),$txt->sanitize($email),$txt->sanitize($comment),$entryID,$this->bID));
		}
 		
		/** 
		 * Deletes the given guestbook entry for the current block
		 * @param integer $entryID
		*/
		function removeEntry($entryID) {
			$db = Loader::db();
			$query = "DELETE FROM btGuestBookEntries WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
		}
		
		function approveEntry($entryID) {
			$db = Loader::db();
			$query = "UPDATE btGuestBookEntries SET approved = 1 WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
		}
	
		function unApproveEntry($entryID) {
			$db = Loader::db();
			$query = "UPDATE btGuestBookEntries SET approved = 0 WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
		}
		
		/** 
		 * Deletes all the entries for the current block
		*/
		function removeAllEntries($cID) {
			$db = Loader::db();
			$query = "DELETE FROM btGuestBookEntries WHERE bID=? AND cID = ?";
			$res = $db->query($query, array($this->bID, $cID));	
		}
		
		/** 
		 * gets all entries for the current block
		 * @param integer $bID
		 * @param string $order ASC|DESC
		 * @return array $rows	
		*/
		public static function getAll($bID, $cID, $order="ASC") {
			$db = Loader::db();
			$query = "SELECT * FROM btGuestBookEntries WHERE bID = ? AND cID = ? ORDER BY entryDate {$order}"; 
			
			$rows = $db->getAll($query,array($bID,$cID));		
			
			return $rows;
		}
	
	} // end class def