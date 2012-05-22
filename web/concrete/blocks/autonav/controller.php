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
 * An object used by the Autonav Block to display navigation items in a tree
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	defined('C5_EXECUTE') or die("Access Denied.");
	class AutonavBlockItem {

		protected $level;
		protected $isActive = false;
		protected $_c;
		public $hasChildren = false;
		
		/**
		 * Instantiates an Autonav Block Item. 
		 * @param array $itemInfo 
		 * @param int $level
		 */
		function AutonavBlockItem($itemInfo, $level = 1) {

			$this->level = $level;
			if (is_array($itemInfo)) {
				// this is an array pulled from a separate SQL query
				foreach ($itemInfo as $key => $value) {
					$this->{$key} = $value;
				}
			}

			return $this;
		}
		
		/**
		 * Returns the number of children below this current nav item
		 * @return int
		 */
		function hasChildren() {
			return $this->hasChildren;
		}
		
		/**
		 * Determines whether this nav item is the current page the user is on. 
		 * @param Page $page The page object for the current page
		 * @return bool
		 */
		function isActive(&$c) {
			if ($c) {
				$cID = ($c->getCollectionPointerID() > 0) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();
				return ($cID == $this->cID);
			}
		}

		/**
		 * Returns the description of the current navigation item (typically grabbed from the page's short description field)
		 * @return string
		 */
		function getDescription() {
			return $this->cvDescription;
		}

		/** 
		 * Returns a target for the nav item
		 */
		public function getTarget() {
			if ($this->cPointerExternalLink != '') {
				if ($this->cPointerExternalLinkNewWindow) {
					return '_blank';
				}
			}
			
			$_c = $this->getCollectionObject();
			if (is_object($_c)) {
				return $_c->getAttribute('nav_target');
			}
			
			return '';
		}
		
		/** 
		 * Gets a URL that will take the user to this particular page. Checks against URL_REWRITING, the page's path, etc..
		 * @return string $url
		 */
		function getURL() {
			$dispatcher = '';
			if (!URL_REWRITING) {
				$dispatcher = '/' . DISPATCHER_FILENAME;
			}
			if ($this->cPointerExternalLink != '') {
				$link = $this->cPointerExternalLink;
			} else if ($this->cPath) {
				$link = DIR_REL . $dispatcher . $this->cPath . '/';
			} else if ($this->cID == HOME_CID) {
				$link = DIR_REL . '/';
			} else {
				$link = DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->cID;
			}
			return $link;
		}
		
		/**
		 * Gets the name of the page or link.
		 * @return string
		 */
		function getName() {
			return $this->cvName;
		}
		
		/**
		 * Gets the pageID for the navigation item.
		 * @return int
		 */
		function getCollectionID() {
			return $this->cID;
		}
		
		
		/**
		 * Gets the current level at the nav tree that we're at.
		 * @return int
		 */
		function getLevel() {
			return $this->level;
		}
		
		/** 
		 * Sets the collection Object of the navigation item to the passed object
		 * @param Page $obj
		 * @return void
		 */
		function setCollectionObject(&$obj) {
			$this->_c = $obj;
		}
		
		/**
		 * Gets the collection Object of the navigation item
		 * @return Page
		 */
		function getCollectionObject() {
			return $this->_c;
		}
	}
	
	/**
	 * The controller for the autonav block, which makes navigation lists and menus from C5 pages.
	 *
	 * @package Blocks
	 * @subpackage BlockTypes
	 * @author Andrew Embler <andrew@concrete5.org>
	 * @category Concrete
	 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
	 * @license    http://www.concrete5.org/license/     MIT License
	 *
	 */
	 
	class AutonavBlockController extends BlockController {

		protected $btTable = 'btNavigation';
		protected $btInterfaceWidth = "500";
		protected $btInterfaceHeight = "350";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		protected $btCacheBlockOutputLifetime = 300;
		protected $btWrapperClass = 'ccm-ui';
		
		protected $btExportPageColumns = array('displayPagesCID');
		
		public function getBlockTypeDescription() {
			return t("Creates navigation trees and sitemaps.");
		}
		
		public function getBlockTypeName() {
			return t("Auto-Nav");
		}

		public $navArray = array();
		public $cParentIDArray = array();

		public $sorted_array = array();
		public $navSort = array();
		public $navObjectNames = array();

		public $displayPages, $displayPagesCID, $displayPagesIncludeSelf, $displaySubPages, $displaySubPageLevels, $displaySubPageLevelsNum, $orderBy, $displayUnavailablePages;
		public $haveRetrievedSelf = false;
		public $haveRetrievedSelfPlus1 = false;
		public $displaySystemPages = false;

		// private variable $displayUnapproved, used by the dashboard
		public $displayUnapproved = false;

		// haveRetrievedSelf is a variable that stores whether or not a particular tree walking operation has retrieved the current page. We use this
		// with subpage modes like enough and enough_plus1

		// displayUnavailablePages allows us to decide whether this autonav block filters out unavailable pages (pages we can't see, are restricted, etc...)
		// or whether they display them, but then restrict them when the page is actually visited
		// TODO - Implement displayUnavailablePages in the btNavigation table, and in the frontend of the autonav block

		function __construct($obj = null) {
			if (is_object($obj)) {
				switch(strtolower(get_class($obj))) {
				   case "blocktype":
					  // instantiating autonav on a particular collection page, instead of adding
					  // it through the block interface
					  $this->bID = null;
					  break;
				   case "block": // block
					  // standard block object
					  $this->bID = $obj->bID;
					  break;
				}
			}

			$c = Page::getCurrentPage();
			if (is_object($c)) {
			   $this->cID = $c->getCollectionID();
			   $this->cParentID = $c->getCollectionParentID();
			}
			
			parent::__construct($obj);
		}
		
		function save($args) {
			$args['displayPagesIncludeSelf'] = $args['displayPagesIncludeSelf'] ? 1 : 0;
			$args['displayPagesCID'] = $args['displayPagesCID'] ? $args['displayPagesCID'] : 0;
			$args['displaySubPageLevelsNum'] = $args['displaySubPageLevelsNum'] > 0 ? $args['displaySubPageLevelsNum'] : 0;
			$args['displayUnavailablePages'] = $args['displayUnavailablePages'] ? 1 : 0;
			parent::save($args);
		}
		
		function getContent() {
			/* our templates expect a variable not an object */
			$con = array();
			foreach($this as $key => $value) {
				$con[$key] = $value;
			}
			return $con;
		}
		
		public function getChildPages($c) {
		
			// a quickie
			$db = Loader::db();
			$r = $db->query("select cID from Pages where cParentID = ? order by cDisplayOrder asc", array($c->getCollectionID()));
			$pages = array();
			while ($row = $r->fetchRow()) {
				$pages[] = Page::getByID($row['cID']);
			}
			return $pages;
		}
		
		function generateNav() {
			$db = Loader::db();
			// now we proceed, with information obtained either from the database, or passed manually from
			$orderBy = "";
			/*switch($this->orderBy) {
			switch($this->orderBy) {
				case 'display_asc':
					$orderBy = "order by Collections.cDisplayOrder asc";
					break;
				case 'display_desc':
					$orderBy = "order by Collections.cDisplayOrder desc";
					break;
				case 'chrono_asc':
					$orderBy = "order by cvDatePublic asc";
					break;
				case 'chrono_desc':
					$orderBy = "order by cvDatePublic desc";
					break;
				case 'alpha_desc':
					$orderBy = "order by cvName desc";
					break;
				default:
					$orderBy = "order by cvName asc";
					break;
			}*/
			switch($this->orderBy) {
				case 'display_asc':
					$orderBy = "order by Pages.cDisplayOrder asc";
					break;
				case 'display_desc':
					$orderBy = "order by Pages.cDisplayOrder desc";
					break;
				default:
					$orderBy = '';
					break;
			}
			$level = 0;
			$cParentID = 0;
			switch($this->displayPages) {
				case 'current':
					$cParentID = $this->cParentID;
					if ($cParentID < 1) {
						$cParentID = 1;
					}
					break;
				case 'top':
					// top level actually has ID 1 as its parent, since the home page is effectively alone at the top
					$cParentID = 1;
					break;
				case 'above':
					$cParentID = $this->getParentParentID();
					break;
				case 'below':
					$cParentID = $this->cID;
					break;
				case 'second_level':
					$cParentID = $this->getParentAtLevel(2);
					break;
				case 'third_level':
					$cParentID = $this->getParentAtLevel(3);
					break;
				case 'custom':
					$cParentID = $this->displayPagesCID;
					break;
				default:
					$cParentID = 1;
					break;
			}
			
			if ($cParentID != null) {
				
				/*
				
				$displayHeadPage = false;

				if ($this->displayPagesIncludeSelf) {
					$q = "select Pages.cID from Pages where Pages.cID = '{$cParentID}' and cIsTemplate = 0";
					$r = $db->query($q);
					if ($r) {
						$row = $r->fetchRow();
						$displayHeadPage = true;
						if ($this->displayUnapproved) {
							$tc1 = Page::getByID($row['cID'], "RECENT");
						} else {
							$tc1 = Page::getByID($row['cID'], "ACTIVE");
						}
						$tc1v = $tc1->getVersionObject();
						if (!$tc1v->isApproved() && !$this->displayUnapproved) {
							$displayHeadPage = false;
						}
					}
				}
				
				if ($displayHeadPage) {
					$level++;
				}
				*/
				
				if ($this->displaySubPages == 'relevant' || $this->displaySubPages == 'relevant_breadcrumb') {
					$this->populateParentIDArray($this->cID);
				}
				
				$this->getNavigationArray($cParentID, $orderBy, $level);
				
				// if we're at the top level we add home to the beginning
				if ($cParentID == 1) {
					if ($this->displayUnapproved) {
						$tc1 = Page::getByID(HOME_CID, "RECENT");
					} else {
						$tc1 = Page::getByID(HOME_CID, "ACTIVE");
					}
					$niRow = array();
					$niRow['cvName'] = $tc1->getCollectionName();
					$niRow['cID'] = HOME_CID;
					$niRow['cvDescription'] = $tc1->getCollectionDescription();
					$niRow['cPath'] = $tc1->getCollectionPath();
					
					$ni = new AutonavBlockItem($niRow, 0);
					$ni->setCollectionObject($tc1);
					
					array_unshift($this->navArray, $ni);
				}
				
				/*
				
				if ($displayHeadPage) {				
					$niRow = array();
					$niRow['cvName'] = $tc1->getCollectionName();
					$niRow['cID'] = $row['cID'];
					$niRow['cvDescription'] = $tc1->getCollectionDescription();
					$niRow['cPath'] = $tc1->getCollectionPath();
					
					$ni = new AutonavBlockItem($niRow, 0);
					$level++;
					$ni->setCollectionObject($tc1);
					
					array_unshift($this->navArray, $ni);
				}
				*/
				
			}
			
			return $this->navArray;
		}

		function getParentAtLevel($level) {
			// this function works in the following way
			// we go from the current collection up to the top level. Then we find the parent Id at the particular level specified, and begin our
			// autonav from that point

			$this->populateParentIDArray($this->cID);

			$idArray = array_reverse($this->cParentIDArray);
			$this->cParentIDArray = array();
			if ($level - count($idArray) == 0) {
				// This means that the parent ID array is one less than the item
				// we're trying to grab - so we return our CURRENT page as the item to get
				// things under
				return $this->cID;
			}
			
			if (isset($idArray[$level])) {
				return $idArray[$level];
			} else {
				return null;
			}
		}
		
		protected function displayPage($tc) {
		
			if ($tc->isSystemPage() && (!$this->displaySystemPages)) {
				if ($tc->getCollectionPath() == '/members' && Config::get('ENABLE_USER_PROFILES')) {
					return true;
				}
				
				return false;
			}
			
			$tcv = $tc->getVersionObject();
			if ((!is_object($tcv)) || (!$tcv->isApproved() && !$this->displayUnapproved)) { 
				return false;
			}
			
			if ($this->displayUnavailablePages == false) {
				$tcp = new Permissions($tc);
				if (!$tcp->canRead() && ($tc->getCollectionPointerExternalLink() == null)) {
					return false;
				}
			}

			return true;
		}

		function getNavigationArray($cParentID, $orderBy, $currentLevel) {
			// increment all items in the nav array with a greater $currentLevel
			
			foreach($this->navArray as $ni) {
				if ($ni->getLevel() + 1 < $currentLevel) {
					$ni->hasChildren = true;
				}
			}
			
			$db = Loader::db();
			$navSort = $this->navSort;
			$sorted_array = $this->sorted_array;
			$navObjectNames = $this->navObjectNames;

			$allowedParentIDs = ($allowedParentIDs) ? $allowedParentIDs : array();
			$q = "select Pages.cID from Pages where cIsTemplate = 0 and cIsActive = 1 and cParentID = '{$cParentID}' {$orderBy}";
			$r = $db->query($q);
			if ($r) {
				while ($row = $r->fetchRow()) {
					if ($this->displaySubPages != 'relevant_breadcrumb' || (in_array($row['cID'], $this->cParentIDArray) || $row['cID'] == $this->cID)) {
						/*
						if ($this->haveRetrievedSelf) {
							// since we've already retrieved self, and we're going through again, we set plus 1
							$this->haveRetrievedSelfPlus1 = true;
						} else 
						*/
						
						if ($this->haveRetrievedSelf && $cParentID == $this->cID) {
							$this->haveRetrievedSelfPlus1 = true;
						} else if ($row['cID'] == $this->cID) {
							$this->haveRetrievedSelf = true;
						}
						
						$displayPage = true;
						if ($this->displayUnapproved) {
							$tc = Page::getByID($row['cID'], "RECENT");
						} else {
							$tc = Page::getByID($row['cID'], "ACTIVE");
						}
						
						$displayPage = $this->displayPage($tc);
						
						if ($displayPage) {
							$niRow = array();
							$niRow['cvName'] = $tc->getCollectionName();
							$niRow['cID'] = $row['cID'];
							$niRow['cvDescription'] = $tc->getCollectionDescription();
							$niRow['cPath'] = $tc->getCollectionPath();
							$niRow['cPointerExternalLink'] = $tc->getCollectionPointerExternalLink();
							$niRow['cPointerExternalLinkNewWindow'] = $tc->openCollectionPointerExternalLinkInNewWindow();
							$dateKey = strtotime($tc->getCollectionDatePublic());

							$ni = new AutonavBlockItem($niRow, $currentLevel);
							$ni->setCollectionObject($tc);
							// $this->navArray[] = $ni;
							$navSort[$niRow['cID']] = $dateKey;
							$sorted_array[$niRow['cID']] = $ni;

							$_c = $ni->getCollectionObject();
							$object_name = $_c->getCollectionName();
							$navObjectNames[$niRow['cID']] = $object_name;

						}

					}
				}
				// end while -- sort navSort

				// Joshua's Huge Sorting Crap
				if($navSort) {
					$sortit=0;
					if($this->orderBy == "chrono_asc") { asort($navSort); $sortit=1; }
					if($this->orderBy == "chrono_desc") { arsort($navSort); $sortit=1; }

					if($sortit) {
						foreach($navSort as $sortCID => $sortdatewhocares) {
							// create sorted_array
							$this->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($this->displaySubPages == 'all') {
								if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($this->displaySubPages == "relevant" || $this->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $this->cParentIDArray) || $sortCID == $this->cID)) {
								if ($this->displaySubPageLevels == "enough" && $this->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == "enough_plus1" && $this->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								$this->getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}

					$sortit=0;
					if($this->orderBy == "alpha_desc") { 
						$navObjectNames = array_map('strtolower',$navObjectNames);
						arsort($navObjectNames);						
						$sortit=1; 						
					}
					
					if($this->orderBy == "alpha_asc") { 
						$navObjectNames = array_map('strtolower',$navObjectNames);
						asort($navObjectNames); 
						$sortit=1; 
					}

					if($sortit) {
						foreach($navObjectNames as $sortCID => $sortnameaction) {
							// create sorted_array
							$this->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($this->displaySubPages == 'all') {
								if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($this->displaySubPages == "relevant" || $this->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $this->cParentIDArray) || $sortCID == $this->cID)) {
								if ($this->displaySubPageLevels == "enough" && $this->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == "enough_plus1" && $this->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								$this->getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}

					$sortit=0;
					if($this->orderBy == "display_desc") { $sortit=1; }
					if($this->orderBy == "display_asc") { $sortit=1; }

					if($sortit) {
						// for display order? this stuff is already sorted...
						foreach($navObjectNames as $sortCID => $sortnameaction) {
							// create sorted_array
							$this->navArray[] = $sorted_array[$sortCID];

							#############start_recursive_crap
							$retrieveMore = false;
							if ($this->displaySubPages == 'all') {
								if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							} else if (($this->displaySubPages == "relevant" || $this->displaySubPages == "relevant_breadcrumb") && (in_array($sortCID, $this->cParentIDArray) || $sortCID == $this->cID)) {
								if ($this->displaySubPageLevels == "enough" && $this->haveRetrievedSelf == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == "enough_plus1" && $this->haveRetrievedSelfPlus1 == false) {
									$retrieveMore = true;
								} else if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
									$retrieveMore = true;
								}
							}
							if ($retrieveMore) {
								$this->getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
							}
							#############end_recursive_crap
						}
					}
				}
				// End Joshua's Huge Sorting Crap

			}
		}

		function populateParentIDArray($cID) {
			// returns an array of collection IDs going from the top level to the current item
			$db = Loader::db();
			$cParentID = Page::getCollectionParentIDFromChildID($cID);
			if ($cParentID > -1) {
				if ($cParentID != $stopAt) {
					if (!in_array($cParentID, $this->cParentIDArray)) {
						$this->cParentIDArray[] = $cParentID;
					}
					$this->populateParentIDArray($cParentID);
				}
			}

		}
		
		/** 
		 * heh. probably should've gone the simpler route and named this getGrandparentID()
		 */
		function getParentParentID() {
			// this has to be the stupidest name of a function I've ever created. sigh
			$cParentID = Page::getCollectionParentIDFromChildID($this->cParentID);
			return ($cParentID) ? $cParentID : 0;
		}
	}

?>