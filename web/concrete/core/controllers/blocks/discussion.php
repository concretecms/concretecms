<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the discussion block. This block is used to add a discussion (which is many conversations) to a website.
 *
 * @package Blocks
 * @subpackage Conversation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_Discussion extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btDiscussion';

		public function getBlockTypeDescription() {
			return t("Places a discussion a page.");
		}
		
		public function getBlockTypeName() {
			return t("Discussion");
		}

		public function getConversationDiscussionObject() {
			if (!isset($this->discussion)) {
				$db = Loader::db();
				$cnvDiscussionID = $db->GetOne('select cnvDiscussionID from btDiscussion where bID = ?', array($this->bID));
				$this->discussion = ConversationDiscussion::getByID($cnvDiscussionID);
			}
			return $this->discussion;
		}

		public function view() {
			$discussion = $this->getConversationDiscussionObject();
			if (is_object($discussion)) {
				$this->set('discussion', $discussion);
				if ($this->enableNewTopics && $this->cmpID) {
					$this->set('composer', Composer::getByID($this->cmpID));
				}

				$c = Page::getCurrentPage();
				$dl = new ConversationDiscussionList($c);
				$orderBy = $this->orderBy;
				if (in_array($_REQUEST['orderBy'], array('replies', 'date', 'date_last_message')) && $this->enableOrdering) {
					$orderBy = $_REQUEST['orderBy'];
				}
				switch($orderBy) {
					case 'replies':
						$dl->sortByTotalReplies();
						break;
					case 'date':
						$dl->sortByPublicDateDescending();
						break;
					default: //date_last_message
						$dl->sortByConversationDateLastMessage();
						break;
				}
				if ($this->itemsPerPage > 0) {
					$dl->setItemsPerPage($this->itemsPerPage);
					$this->requireAsset('core/frontend/pagination');
				}
				$pages = $dl->getPage();
				$this->set('reqOrderBy', $orderBy);
				$this->set('topics', $pages);
				$this->set('list', $dl);

			}
		}

		public function action_post() {
			// happens through ajax
			$composer = Composer::getByID($this->cmpID);
			if (is_object($composer) && $this->enableNewTopics) {
				$ccp = new Permissions($composer);
				if ($ccp->canAccessComposer()) {
					$pagetypes = $composer->getComposerPageTypeObjects();
					$ctTopic = $pagetypes[0];
					$c = Page::getCurrentPage();
					$e = $composer->validatePublishRequest($ctTopic, $c);
					$r = new ComposerPublishResponse($e);
					if (!$e->has()) {
						$d = $composer->createDraft($ctTopic);
						$d->setComposerDraftTargetParentPageID($c->getCollectionID());
						$d->saveForm();
						$d->publish();
						$nc = Page::getByID($d->getComposerDraftCollectionID(), 'RECENT');
						$link = Loader::helper('navigation')->getLinkToCollection($nc, true);
						$r->setRedirectURL($link);
					}
					print Loader::helper('ajax')->sendResult($r);
				}
			}
			exit;
		}

		public function on_page_view() {
			if ($this->enableNewTopics && $this->cmpID) {
				$cmp = Composer::getByID($this->cmpID);
				if (is_object($cmp)) {
					Loader::helper('composer')->addAssetsToRequest($cmp, $this);
				}
			}
			$req = Request::get();
			$req->requireAsset('core/conversation');
		}

		public function save($post) {
			$db = Loader::db();
			$cnvID = $db->GetOne('select cnvDiscussionID from btDiscussion where bID = ?', array($this->bID));
			if (!$cnvID) {
				$c = Page::getCurrentPage();
				$discussion = ConversationDiscussion::add($c);
			} else {
				$discussion = ConversationDiscussion::getByID($cnvID);
			}
			$values = $post;
			$cmpID = 0;
			if ($post['cmpID']) {
				$cmpID = $post['cmpID'];
			}
			$values['cmpID'] = $cmpID;
			$values['cnvDiscussionID'] = $discussion->getConversationDiscussionID();
			parent::save($values);
		}

	}