<?php

namespace Concrete\Core\Page;
use Database;
/**
 * @Entity
 * @Table(name="PageFeeds")
 */
class Feed
{

    protected $itemsPerFeed = 20;
    protected $checkPagePermissions = true;

    /**
     * @param mixed $cParentID
     */
    public function setParentID($cParentID)
    {
        $this->cParentID = $cParentID;
    }

    /**
     * @return mixed
     */
    public function getParentID()
    {
        return $this->cParentID;
    }

    /**
     * @param mixed $pfDescription
     */
    public function setDescription($pfDescription)
    {
        $this->pfDescription = $pfDescription;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->pfDescription;
    }

    /**
     * @param mixed $pfDisplayAliases
     */
    public function setDisplayAliases($pfDisplayAliases)
    {
        $this->pfDisplayAliases = $pfDisplayAliases;
    }

    /**
     * @return mixed
     */
    public function getDisplayAliases()
    {
        return $this->pfDisplayAliases;
    }

    /**
     * @param mixed $pfDisplayFeaturedOnly
     */
    public function setDisplayFeaturedOnly($pfDisplayFeaturedOnly)
    {
        $this->pfDisplayFeaturedOnly = $pfDisplayFeaturedOnly;
    }

    /**
     * @return mixed
     */
    public function getDisplayFeaturedOnly()
    {
        return $this->pfDisplayFeaturedOnly;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->pfID;
    }

    /**
     * @param mixed $pfIncludeAllDescendents
     */
    public function setIncludeAllDescendents($pfIncludeAllDescendents)
    {
        $this->pfIncludeAllDescendents = $pfIncludeAllDescendents;
    }

    /**
     * @return mixed
     */
    public function getIncludeAllDescendents()
    {
        return $this->pfIncludeAllDescendents;
    }

    /**
     * @param mixed $pfTitle
     */
    public function setTitle($pfTitle)
    {
        $this->pfTitle = $pfTitle;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->pfTitle;
    }

    /**
     * @param mixed $ptID
     */
    public function setPageTypeID($ptID)
    {
        $this->ptID = $ptID;
    }

    /**
     * @return mixed
     */
    public function getPageTypeID()
    {
        return $this->ptID;
    }

    /**
     * @Column(type="string")
     */
    protected $pfDescription;

    /**
     * @Column(type="string")
     */
    protected $pfHandle;

    /**
     * @param mixed $pfHandle
     */
    public function setHandle($pfHandle)
    {
        $this->pfHandle = $pfHandle;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->pfHandle;
    }

    /**
     * @Column(type="string")
     */
    protected $pfTitle;

    /**
     * @Id @Column(columnDefinition="integer unsigned")
     * @GeneratedValue
     */
    protected $pfID;

    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $cParentID;

    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $ptID;

    /**
     * @Column(type="boolean")
     */
    protected $pfIncludeAllDescendents = false;

    /**
     * @Column(type="boolean")
     */
    protected $pfDisplayAliases = false;

    /**
     * @Column(type="string")
     */
    protected $pfContentToDisplay = 'S'; // short description

    /**
     * @Column(type="string")
     */
    protected $pfAreaHandleToDisplay;

    public function displayShortDescriptionContent()
    {
        $this->pfContentToDisplay = 'S';
        $this->pfAreaHandleToDisplay = null;
    }

    public function displayAreaContent($arHandle)
    {
        $this->pfContentToDisplay = 'A';
        $this->pfAreaHandleToDisplay = $arHandle;
    }

    public function getTypeOfContentToDisplay()
    {
        return $this->pfContentToDisplay;
    }

    public function getAreaHandleToDisplay()
    {
        return $this->pfAreaHandleToDisplay;
    }

    /**
     * @Column(type="boolean")
     */
    protected $pfDisplayFeaturedOnly = false;

    public static function getList()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\Page\Feed')->findBy(array(), array('pfTitle' => 'asc'));
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function exportList($node)
    {
        $child = $node->addChild('pagefeeds');
        $list = static::getList();
        foreach($list as $link) {
            $linkNode = $child->addChild('feed');
        }
    }

    public function delete()
    {
        $em = Database::get()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\Page\Feed', $id);
        return $r;
    }

    public static function getByHandle($pfHandle)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\Page\Feed')->findOneBy(
            array('pfHandle' => $pfHandle)
        );
    }

    public function ignorePermissions()
    {
        $this->checkPagePermissions = false;
    }

    /**
     * @return \Concrete\Core\Page\PageList
     */
    public function getPageListObject()
    {
        $pl = new PageList();
        $pl->setItemsPerPage($this->itemsPerFeed);
        if (!$this->checkPagePermissions) {
            $pl->ignorePermissions();
        }
        if ($this->cParentID) {
            if ($this->pfIncludeAllDescendents) {
                $parent = \Page::getByID($this->cParentID);
                if (is_object($parent) && !$parent->isError()) {
                    $pl->filterByPath($parent->getCollectionPath());
                }
            } else {
                $pl->filterByParentID($this->cParentID);
            }
        }
        if ($this->pfDisplayAliases) {
            $pl->includeAliases();
        }
        if ($this->ptID) {
            $pl->filterByPageTypeID($this->ptID);
        }
        if ($this->pfDisplayFeaturedOnly) {
            $pl->filterByAttribute('is_featured', true);
        }
        return $pl;
    }

    public function getPageFeedContent(Page $p)
    {

    }
}