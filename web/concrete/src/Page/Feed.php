<?php

namespace Concrete\Core\Page;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Html\Object\HeadLink;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
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
     * @Column(type="string")
     */
    protected $customTopicAttributeKeyHandle = null;


    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $customTopicTreeNodeID = 0;

    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $iconFID = 0;

    /**
     * @return mixed
     */
    public function getIconFileID()
    {
        return $this->iconFID;
    }

    /**
     * @param mixed $iconFID
     */
    public function setIconFileID($iconFID)
    {
        $this->iconFID = $iconFID;
    }

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
     * @return mixed
     */
    public function getCustomTopicAttributeKeyHandle()
    {
        return $this->customTopicAttributeKeyHandle;
    }

    /**
     * @param mixed $customTopicAttributeKeyHandle
     */
    public function setCustomTopicAttributeKeyHandle($customTopicAttributeKeyHandle)
    {
        $this->customTopicAttributeKeyHandle = $customTopicAttributeKeyHandle;
    }

    /**
     * @return mixed
     */
    public function getCustomTopicTreeNodeID()
    {
        return $this->customTopicTreeNodeID;
    }

    /**
     * @param mixed $customTopicTreeNodeID
     */
    public function setCustomTopicTreeNodeID($customTopicTreeNodeID)
    {
        $this->customTopicTreeNodeID = $customTopicTreeNodeID;
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

    public function getFeedURL()
    {
        return \URL::to('/rss/' . $this->getHandle());
    }
    /**
     * @Column(type="boolean")
     */
    protected $pfDisplayFeaturedOnly = false;

    public static function getList()
    {
        $em = \ORM::entityManager('core');
        return $em->getRepository('\Concrete\Core\Page\Feed')->findBy(array(), array('pfTitle' => 'asc'));
    }

    public function save()
    {
        $em = \ORM::entityManager('core');
        $em->persist($this);
        $em->flush();
    }

    public static function exportList(\SimpleXMLElement $node)
    {
        $child = $node->addChild('pagefeeds');
        $list = static::getList();
        foreach($list as $feed) {
            $feedNode = $child->addChild('feed');
            if ($feed->getParentID()) {
                $feedNode->addChild('parent', ContentExporter::replacePageWithPlaceHolder($feed->getParentID()));
            }
            $feedNode->addChild('title', $feed->getTitle());
            $feedNode->addChild('description', $feed->getDescription());
            $feedNode->addChild('handle', $feed->getHandle());
            if ($feed->getIncludeAllDescendents()) {
                $feedNode->addChild('descendents', 1);
            }
            if ($feed->getDisplayAliases()) {
                $feedNode->addChild('aliases', 1);
            }
            if ($feed->getDisplayFeaturedOnly()) {
                $feedNode->addChild('featured', 1);
            }
            if ($feed->getPageTypeID()) {
                $feedNode->addChild('pagetype', ContentExporter::replacePageTypeWithPlaceHolder($feed->getPageTypeID()));
            }
            if ($feed->getTypeOfContentToDisplay() == 'S') {
                $type = $feedNode->addChild('contenttype');
                $type->addAttribute('type', 'description');
            } else {
                $area = $feedNode->addChild('contenttype');
                $area->addAttribute('type', 'area');
                $area->addAttribute('handle', $feed->getAreaHandleToDisplay());
            }
        }
    }

    public function delete()
    {
        $em = \ORM::entityManager('core');
        $em->remove($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $em = \ORM::entityManager('core');
        $r = $em->find('\Concrete\Core\Page\Feed', $id);
        return $r;
    }

    /**
     * Get a PageFeed by its handle
     * @param $pfHandle
     * @return self|null
     */
    public static function getByHandle($pfHandle)
    {
        $em = \ORM::entityManager('core');
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
        $pl->sortByPublicDateDescending();
        if (!$this->checkPagePermissions) {
            $pl->ignorePermissions();
        } else {
            $vp = \Concrete\Core\Permission\Key\Key::getByHandle('view_page');
            $guest = \Group::getByID(GUEST_GROUP_ID);
            $access = GroupEntity::getOrCreate($guest);
            // we set page permissions to be Guest group only, because
            // authentication won't work with RSS feeds
            $pl->setPermissionsChecker(function($page) use ($vp, $access) {
                $vp->setPermissionObject($page);
                $pa = $vp->getPermissionAccessObject($page);
                if (!is_object($pa)) {
                    return false;
                }
                return $pa->validateAccessEntities(array($access));
            });
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
        if ($this->getCustomTopicAttributeKeyHandle()) {
            $pl->filterByTopic(intval($this->getCustomTopicTreeNodeID()));
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

    protected function getPageFeedContent(Page $p)
    {
        $content = false;
        switch($this->pfContentToDisplay) {
            case 'S':
                $content = $p->getCollectionDescription();
                break;
            case 'A':
                $a = new \Area($this->getAreaHandleToDisplay());
                $blocks = $a->getAreaBlocksArray($p);
                $r = Request::getInstance();
                $r->setCurrentPage($p);
                ob_start();
                foreach($blocks as $b) {
                    $bv = new BlockView($b);
                    $bv->render('view');
                }
                $content = ob_get_contents();
                ob_end_clean();
                break;
        }

        $f = $p->getAttribute('thumbnail');
        if (is_object($f)) {
            $content = '<p><img src="' . $f->getURL() . '" /></p>' . $content;
        }
        return $content;
    }

    /**
     * Get the feed output in RSS form given a Request object
     * @param Request|null $request
     * @return string|null The full RSS output as a string
     */
    public function getOutput($request = null)
    {
        $pl = $this->getPageListObject();
        $link = false;
        if ($this->cParentID) {
            $parent = Page::getByID($this->cParentID);
            $link = $parent->getCollectionLink();
        } else {
            $link = \URL::to('/');
        }
        $pagination = $pl->getPagination();
        if ($pagination->getTotalResults() > 0) {
            $writer = new \Zend\Feed\Writer\Feed();
            $writer->setTitle($this->getTitle());
            $writer->setDescription($this->getDescription());
            if ($this->getIconFileID()) {
                $f = \File::getByID($this->getIconFileID());
                if (is_object($f)) {
                    $data = array(
                        'uri' => $f->getURL(),
                        'title' => $f->getTitle(),
                        'link' => (string) $link
                    );
                    $writer->setImage($data);
                }
            }
            $writer->setLink((string) $link);

            foreach($pagination->getCurrentPageResults() as $p) {
                $entry = $writer->createEntry();
                $entry->setTitle($p->getCollectionName());
                $entry->setDateCreated(strtotime($p->getCollectionDatePublic()));
                $content = $this->getPageFeedContent($p);
                if (!$content) {
                    $content = t('No Content.');
                }
                $entry->setDescription($content);
                $entry->setLink((string) $p->getCollectionLink(true));
                $writer->addEntry($entry);
            }

            $ev = new FeedEvent();
            if (isset($parent)) {
                $ev->setPageObject($parent);
            }
            $ev->setFeedObject($this);
            $ev->setWriterObject($writer);
            $ev->setRequest($request);

            $ev = \Events::dispatch('on_page_feed_output', $ev);

            $writer = $ev->getWriterObject();

            return $writer->export('rss');
        }
    }

    public function getHeadLinkElement()
    {
        $link = new HeadLink($this->getFeedURL(), 'alternate', 'application/rss+xml');
        return $link;
    }

}
