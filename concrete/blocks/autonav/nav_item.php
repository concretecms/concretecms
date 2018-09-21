<?php
namespace Concrete\Block\Autonav;

use Concrete\Core\Page\Page;

/**
 * An object used by the Autonav Block to display navigation items in a tree.
 */
class NavItem
{
    protected $level;
    protected $isActive = false;
    protected $_c;
    public $hasChildren = false;
    public $cID;
    public $cPath;
    public $cPointerExternalLink;
    public $cPointerExternalLinkNewWindow;
    public $cvDescription;
    public $cvName;

    /**
     * Instantiates an Autonav Block Item.
     *
     * @param array $itemInfo
     * @param int $level
     */
    public function __construct($itemInfo, $level = 1)
    {
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
     * Returns the number of children below this current nav item.
     *
     * @return int
     */
    public function hasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Determines whether this nav item is the current page the user is on.
     *
     * @param \Concrete\Core\Page\Page $c The page object for the current page
     *
     * @return bool
     */
    public function isActive(&$c)
    {
        if ($c) {
            $cID = ($c->getCollectionPointerID() > 0) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();

            return $cID == $this->cID;
        }
    }

    /**
     * Returns the description of the current navigation item (typically grabbed from the page's short description field).
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->cvDescription;
    }

    /**
     * Returns a target for the nav item.
     */
    public function getTarget()
    {
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
     *
     * @return string $url
     */
    public function getURL()
    {
        if ($this->cPointerExternalLink != '') {
            $link = $this->cPointerExternalLink;
        } elseif ($this->cPath) {
            $link = $this->cPath;
        } elseif ($this->cID == Page::getHomePageID()) {
            $link = DIR_REL . '/';
        } else {
            $link = DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->cID;
        }

        return $link;
    }

    /**
     * Gets the name of the page or link.
     *
     * @return string
     */
    public function getName()
    {
        return $this->cvName;
    }

    /**
     * Gets the pageID for the navigation item.
     *
     * @return int
     */
    public function getCollectionID()
    {
        return $this->cID;
    }

    /**
     * Gets the current level at the nav tree that we're at.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the collection Object of the navigation item to the passed object.
     *
     * @param \Concrete\Core\Page\Page $obj
     */
    public function setCollectionObject(&$obj)
    {
        $this->_c = $obj;
    }

    /**
     * Gets the collection Object of the navigation item.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getCollectionObject()
    {
        return $this->_c;
    }
}
