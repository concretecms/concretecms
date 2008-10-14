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
 * Controller for the discussion block, which allows site owners to add threaded discussions and forums to their site.
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
	class DiscussionBlockController extends BlockController {
		
		protected $btTable = 'btDiscussion';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "300";	
		private $mode;

		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("Adds a forum to a particular area of your site.");
		}
		
		public function getBlockTypeName() {
			return t("Discussion");
		}
		
		public function getMode() {return $this->mode;}
		
		function save($args) {
			global $c;
			
			// If we've gotten to the process() function for this class, we assume that we're in
			// the clear, as far as permissions are concerned (since we check permissions at several
			// points within the dispatcher)
			$db = Loader::db();
	
			$bID = $this->bID;
			$cID = $c->getCollectionID();			

			$args['cThis'] = ($args['cParentID'] == $cID) ? '1' : '0';
			$args['cParentID'] = ($args['cParentID'] == 'OTHER') ? $args['cParentIDValue'] : $args['cParentID'];
			parent::save($args);	
		}
		
		public function getDiscussionCollectionID() {
			global $c;
			$cParentID = ($this->cThis) ? $c->getCollectionID() : $this->cParentID;
			return $cParentID;
		}
	
	
		public function view() {
			Loader::model('discussion');
			Loader::model('discussion_post');
			$nav = Loader::helper('navigation');
			$this->set('html', Loader::helper('html'));

			$db = Loader::db();
			$ctHandle = $db->GetOne("select ctHandle from PageTypes inner join Pages on Pages.ctID = PageTypes.ctID where Pages.cID = ?", $this->getDiscussionCollectionID());
			switch($ctHandle) {
				case DiscussionModel::CTHANDLE:
					$this->mode = "topics";
					break;
				default: // if it's not one of these, then we're in category mode
					$this->mode = 'category';
					break;
			}
			
			switch($this->mode) {
				case "category":
					$categories = DiscussionModel::getDiscussions($this->getDiscussionCollectionID());
					$this->set('categories', $categories);
					$this->render('view_categories');
					break;
				case "topics":
					$categories = array();
					$dm = DiscussionModel::getByID($this->getDiscussionCollectionID());
					$topics = $dm->getPosts();
					$this->set('topics', $topics);
					$this->render('view_topics');
					$this->set('av', Loader::helper('concrete/avatar'));
					break;
			}
			$this->set('nav', $nav);
			
		}

	}