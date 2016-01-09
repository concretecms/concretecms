<?php
namespace Concrete\Block\Autonav;
use Loader;
/**
 * An object used by the Autonav Block to display navigation items in a tree
 *
 * @package Blocks
 * @subpackage Auto-Nav
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class NavItem {

		protected $level;
		protected $isActive = false;
		protected $_c;
		public $hasChildren = false;

		/**
		 * Instantiates an Autonav Block Item.
		 * @param array $itemInfo
		 * @param int $level
		 */
		function __construct($itemInfo, $level = 1) {

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
		 * Gets a URL that will take the user to this particular page. Checks against concrete.seo.url_rewriting, the page's path, etc..
		 * @return string $url
		 */
		function getURL() {
			if ($this->cPointerExternalLink != '') {
				$link = $this->cPointerExternalLink;
			} else if ($this->cPath) {
			    $link = $this->cPath;
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
