<?php

namespace Concrete\Block\Autonav;

use Concrete\Core\Page\Page;

/**
 * An object used by the Autonav Block to display navigation items in a tree.
 */
class NavItem
{
    /**
     * @var bool|int
     */
    public $hasChildren = false;

    /**
     * @var int|null
     */
    public $cID;

    /**
     * @var string|null
     */
    public $cPath;

    /**
     * @var string|null
     */
    public $cPointerExternalLink;

    /**
     * @var string|null
     */
    public $cPointerExternalLinkNewWindow;

    /**
     * @var string|null
     */
    public $cvDescription;

    /**
     * @var string|null
     */
    public $cvName;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @var Page
     */
    protected $_c;

    /**
     * Instantiates an Autonav Block Item.
     *
     * @param array<string,mixed> $itemInfo
     * @param int $level
     *
     * @return void
     */
    public function __construct(array $itemInfo, int $level = 1)
    {
        $this->level = $level;
        if (is_array($itemInfo)) {
            // this is an array pulled from a separate SQL query
            foreach ($itemInfo as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Returns the number of children below this current nav item.
     *
     * @return int|bool
     */
    public function hasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Determines whether this nav item is the current page the user is on.
     *
     * @param \Concrete\Core\Page\Page|null $c The page object for the current page
     *
     * @return bool
     */
    public function isActive($c): bool
    {
        if ($c) {
            $cID = ($c->getCollectionPointerID() > 0) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();

            return $cID == $this->cID;
        }

        return false;
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
     *
     * @return string
     */
    public function getTarget(): string
    {
        if ($this->cPointerExternalLink != '') {
            if ($this->cPointerExternalLinkNewWindow) {
                return '_blank';
            }
        }

        /** @var Page|null $_c */
        $_c = $this->getCollectionObject();
        if (is_object($_c)) {
            return (string) $_c->getAttribute('nav_target');
        }

        return '';
    }

    /**
     * Gets a URL that will take the user to this particular page. Checks against concrete.seo.url_rewriting, the page's path, etc..
     *
     * @return string $url
     */
    public function getURL(): string
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
     *
     * @return void
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
