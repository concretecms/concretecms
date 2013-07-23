<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Controller for the guestbook block, which allows site owners to add comments onto any concrete page.
 *
 * @package Blocks
 * @subpackage Guestbook
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_Guestbook extends BlockController {		
		protected $btTable = 'btGuestBook';
		protected $btInterfaceWidth = "370";
		protected $btInterfaceHeight = "480";	
		protected $btWrapperClass = 'ccm-ui';
		protected $btExportPageColumns = array('cID');
		protected $btIncludeAll = true; // This has to be on otherwise duplicate() kills the entries.
		protected $btExportTables = array('btGuestBook', 'btGuestBookEntries');

		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Adds blog-style comments (a guestbook) to your page.");
		}
		
		public function getBlockTypeName() {
			return t("Guestbook / Comments");
		}			
			
		function delete() {
			$ip = Loader::helper('validation/ip');
			if (!$ip->check()) {
				$this->set('invalidIP', $ip->getErrorMessage());			
				return;
			}
			$c = Page::getCurrentPage();
			$E = new GuestBookBlockEntry($this->bID, $c->getCollectionID());
			$bo = $this->getBlockObject();
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
				$txt = Loader::helper('text');

				$E = new GuestBookBlockEntry($this->bID, $c->getCollectionID());
				$E->user_name = $txt->entities($_POST['name']).'';
				$E->user_email = $txt->entities($_POST['email']).'';
				$E->commentText = $txt->entities($_POST['commentText']);
				$E->uID			= $uID;
				
				$E->entryID = ($_POST['entryID']?$_POST['entryID']:NULL);
				
				$this->set('response', t('Please correct the following errors:') );
				$this->set('errors',$errors);
				$this->set('Entry',$E);	
			} else {
				$antispam = Loader::helper('validation/antispam');
				if (!$antispam->check($_POST['commentText'], 'guestbook_block', array('email' => $_POST['email']))) { 
					$this->requireApproval = true;
				}

				$E = new GuestBookBlockEntry($this->bID, $c->getCollectionID());
				if($_POST['entryID']) { // update
					$bp = $this->getPermissionObject(); 
					if($bp->canWrite()) {
						$E->updateEntry($_POST['entryID'], $_POST['commentText'], $_POST['name'], $_POST['email'], $uID );
						$this->set('response', t('The comment has been saved') );
					} else {
						$this->set('response', t('An Error occured while saving the comment') );
						return true;
					}
				} else { // add			
					$E->addEntry($_POST['commentText'], $_POST['name'], $_POST['email'], (!$this->requireApproval), $cID, $uID );	
					if ($this->requireApproval) { 
						$this->set('response', t('Thanks! Your comment has been received. It will require approval before it appears.'));
					} else { 
						$this->set('response', t('Thanks! Your comment has been posted.') );
					}
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
					$mh->addParameter('guestbookURL', Loader::helper('navigation')->getLinkToCollection($c, true)); 
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
			$bp = $this->getPermissionObject(); 
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

			$bp = $this->getPermissionObject(); 
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

			$bp = $this->getPermissionObject(); 
			if($bp->canWrite()) {
				$Entry = new GuestBookBlockEntry($this->bID);
				$Entry->unApproveEntry($_GET['entryID']);
				$this->set('response', t('The comment has been unapproved.') );
			}
		}
		
		
		
		public function getEntryCount($cID = NULL) {
			$ca = new Cache();
			$cID = (isset($cID)?$cID:$this->cID);
			$count = $ca->get('GuestBookCount',$cID."-".$this->bID);
			if(!isset($count) || $count === false) {
				$db = Loader::db();
				$q = 'SELECT count(bID) as count
				FROM btGuestBookEntries
				WHERE bID = ?
				AND cID = ?
				AND approved=1';				
				$v = array($this->bID, $cID);
				$count = $db->getOne($q,$v);
			}
			return $count;
		}
		
		
	} // end class def
	
	
