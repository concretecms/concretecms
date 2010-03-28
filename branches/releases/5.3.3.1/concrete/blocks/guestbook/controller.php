<?php 
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
	class GuestbookBlockController extends BlockController {		
		protected $btTable = 'btGuestBook';
		protected $btInterfaceWidth = "300";
		protected $btInterfaceHeight = "260";	
		
		protected $btIncludeAll = 1;
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Adds blog-style comments (a guestbook) to your page.");
		}
		
		public function getBlockTypeName() {
			return t("Guestbook");
		}			
			
		function delete() {
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}
			$E = new GuestBookBlockEntry($this->bID);
			$bo = $this->getBlockObject();
			$c = Page::getCurrentPage();
			$E->removeAllEntries( $c->getCollectionID() );
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
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}

			// get the cID from the block Object
			$bo = $this->getBlockObject();
			$c = Page::getCurrentPage();
			$cID = $c->getCollectionID();
		
			$v = Loader::helper('validation/strings');
			$errors = array();
			
			$u = new User();
			$uID = intval( $u->getUserID() );
			if($this->authenticationRequired && !$u->isLoggedIn()){
					$errors['notLogged'] = '- '.t("Your session has expired.  Please log back in."); 
			}elseif(!$this->authenticationRequired){		
				if(!$v->email($_POST['email'])) {
					$errors['email'] = '- '.t("Invalid Email Address");
				}
				if(!$v->notempty($_POST['name'])) {
					$errors['name'] = '- '.t("Name is required");
				}
			}
			
			// check captcha if activated
			if ($this->displayCaptcha) {
			   $captcha = Loader::helper('validation/captcha');
			   if (!$captcha->check()) {
			      $errors['captcha'] = '- '.t("Incorrect captcha code");
			   }
			}

			if(!$v->notempty($_POST['commentText'])) {
				$errors['commentText'] = '- '.t("a comment is required");
			}
			
			if(count($errors)){
				$E = new GuestBookBlockEntry($this->bID);
				$E->user_name = $_POST['name'].'';
				$E->user_email = $_POST['email'].'';
				$E->commentText = $_POST['commentText'];
				$E->uID			= $uID;
				
				$E->entryID = ($_POST['entryID']?$_POST['entryID']:NULL);
				
				$this->set('response', t('Please correct the following errors:') );
				$this->set('errors',$errors);
				$this->set('Entry',$E);	
			} else {
				$E = new GuestBookBlockEntry($this->bID);
				if($_POST['entryID']) { // update
					$bp = $this->getPermissionsObject(); 
					if($bp->canWrite()) {
						$E->updateEntry($_POST['entryID'], $_POST['commentText'], $_POST['name'], $_POST['email'], $uID );
						$this->set('response', t('The comment has been saved') );
					} else {
						$this->set('response', t('An Error occured while saving the comment') );
						return true;
					}
				} else { // add			
					$E->addEntry($_POST['commentText'], $_POST['name'], $_POST['email'], (!$this->requireApproval), $cID, $uID );	
					$this->set('response', t('Thanks! Your comment has been posted.') );
				}
				 
				$stringsHelper = Loader::helper('validation/strings');
				if( $stringsHelper->email($this->notifyEmail) ){
					$c = Page::getCurrentPage(); 
					if(intval($uID)>0){
						Loader::model('userinfo');
						$ui = UserInfo::getByID($uID);
						$fromEmail=$ui->getUserEmail();
						$fromName=$ui->getUserName();
					}else{
						$fromEmail=$_POST['email'];
						$fromName=$_POST['name'];
					} 
					$mh = Loader::helper('mail');
					$mh->to( $this->notifyEmail ); 
					$mh->addParameter('guestbookURL', BASE_URL.DIR_REL.$c->getCollectionPath() ); 
					$mh->addParameter('comment',  $_POST['commentText'] );  
					$mh->from($fromEmail,$fromName);
					$mh->load('block_guestbook_notification');
					$mh->setSubject( t('Guestbook Comment Notification') ); 
					//echo $mh->body.'<br>';
					@$mh->sendMail(); 
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
			$bo = $this->getBlockObject();
			$c = Page::getCurrentPage();
			return GuestBookBlockEntry::getAll($this->bID, $c->getCollectionID(), $order);
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
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}
			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->removeEntry($_GET['entryID']);
				$this->set('response', t('The comment has been removed.') );
			}
		}
	
	
	
		/** 
		 * deltes a given Entry, sets the response message for use in the view
		 *
		*/
		function action_approveEntry() {
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}

			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->approveEntry($_GET['entryID']);
				$this->set('response', t('The comment has been approved.') );
			}
		}
		
		/** 
		 * deltes a given Entry, sets the response message for use in the view
		 *
		*/
		function action_unApproveEntry() {
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}

			$bp = $this->getPermissionsObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->unApproveEntry($_GET['entryID']);
				$this->set('response', t('The comment has been unapproved.') );
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
		 * blocks uID user id
		 * @var integer
		*/
		var $uID;		
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
			$this->uID 			= $data['uID'];
		}
		
		/** 
		 * Adds an entry to the guestbook for the current block
		 * @param string $comment
		 * @param string $name
		 * @param string $email
		*/
 		function addEntry($comment, $name, $email, $approved, $cID, $uID=0 ) {
			$txt = Loader::helper('text');
 		
			$db = Loader::db();
			$query = "INSERT INTO btGuestBookEntries (bID, cID, uID, user_name, user_email, commentText, approved) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$res = $db->query($query, array($this->bID, $cID, intval($uID), $txt->sanitize($name), $txt->sanitize($email), $txt->sanitize($comment), $approved) );

			$this->adjustCountCache(1);
		}

		/**
		* Adjusts cache of count bynumber specified, 
		*
		* Refreshes from db if cache is invalidated or
		* false is called in
		*/		
		private function adjustCountCache($number=false){
			$ca 	= new Cache();
			$db 	= Loader::db();			
			$count = $ca->get('GuestBookCount',$this->bID);
			if($count && $number){
				$count += $number;				
			}
			else{
				$q = 'SELECT count(bID) as count
				FROM btGuestBookEntries
				WHERE bID = ?
				AND approved=1';				
				$v = Array($this->bID);
				$rs = $db->query($q,$v);
				$row = $rs->FetchRow();
				$count = $row['count'];
			}			
			$ca->set('GuestBookCount',$this->bID,$count);
		}
		
		/** 
		 * Updates the given guestbook entry for the current block
		 * @param integer $entryID
		 * @param string $comment
		 * @param string $name
		 * @param string $email
		 * @param string $uID
		*/
	 	function updateEntry($entryID, $comment, $name, $email, $uID=0 ) {
			$db = Loader::db();
			$txt = Loader::helper('text');
			$query = "UPDATE btGuestBookEntries SET user_name=?, uID=?, user_email=?, commentText=? WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($txt->sanitize($name), intval($uID), $txt->sanitize($email),$txt->sanitize($comment),$entryID,$this->bID));
		}
 		
		/** 
		 * Deletes the given guestbook entry for the current block
		 * @param integer $entryID
		*/
		function removeEntry($entryID) {
			$db = Loader::db();
			$query = "DELETE FROM btGuestBookEntries WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
			$this->adjustCountCache(-1);
		}
		
		function approveEntry($entryID) {
			$db = Loader::db();
			$query = "UPDATE btGuestBookEntries SET approved = 1 WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
			$this->adjustCountCache(1);
		}
	
		function unApproveEntry($entryID) {
			$db = Loader::db();
			$query = "UPDATE btGuestBookEntries SET approved = 0 WHERE entryID=? AND bID=?";
			$res = $db->query($query, array($entryID,$this->bID));
			$this->adjustCountCache(-1);
		}
		
		/** 
		 * Deletes all the entries for the current block
		*/
		function removeAllEntries($cID) {
			$db = Loader::db();
			$query = "DELETE FROM btGuestBookEntries WHERE bID=? AND cID = ?";
			$res = $db->query($query, array($this->bID, $cID));	
			$this->adjustCountCache(false);
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