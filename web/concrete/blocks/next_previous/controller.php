<?php

namespace Concrete\Block\NextPrevious;

use Database;
use Permissions;
use Page;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = 'btNextPrevious';
    protected $btInterfaceWidth = "430";
    protected $btInterfaceHeight = "400";
    protected $btCacheBlockRecord = true;
    protected $btWrapperClass = 'ccm-ui';
    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Navigate through sibling pages.");
    }

    public function getBlockTypeName()
    {
        return t("Next & Previous Nav");
    }

    public function getJavaScriptStrings()
    {
        return array();
    }

    public function save($args)
    {
        $args += array(
            'showArrows' => 0,
            'loopSequence' => 0,
            'excludeSystemPages' => 0,
        );

        $args['showArrows'] = intval($args['showArrows']);
        $args['loopSequence'] = intval($args['loopSequence']);
        $args['excludeSystemPages'] = intval($args['excludeSystemPages']);

        parent::save($args);
    }

    public function view()
    {
        $nextPage = $this->getNextCollection();
        $previousPage = $this->getPreviousCollection();
        $parentPage = Page::getByID(Page::getCurrentPage()->getCollectionParentID());

        $nextLinkText = $this->nextLabel;
        $previousLinkText = $this->previousLabel;
        $parentLinkText = $this->parentLabel;

        $this->set('nextCollection', $nextPage);
        $this->set('previousCollection', $previousPage);
        $this->set('parentCollection', $parentPage);

        $this->set('nextLinkText', $nextLinkText);
        $this->set('previousLinkText', $previousLinkText);
        $this->set('parentLinkText', $parentLinkText);
    }

    public function getNextCollection()
    {
        $page = false;
        $db = Database::connection();
        $systemPages = '';
        if ($this->excludeSystemPages) {
            $systemPages = 'and cIsSystemPage = 0';
        }
        $cID = 1;
        $currentPage = Page::getCurrentPage();
        while ($cID > 0) {
            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic > ? and cParentID = ? ' . $systemPages . ' order by cvDatePublic asc', array($currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()));
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic < ? and cParentID = ? ' . $systemPages . ' order by cvDatePublic desc', array($currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()));
                    break;
                case 'display_desc':
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder < ? and cParentID = ? ' . $systemPages . ' order by cDisplayOrder desc', array($currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()));
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder > ? and cParentID = ? ' . $systemPages . ' order by cDisplayOrder asc', array($currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()));
                    break;
            }
            if ($cID > 0) {
                $page = Page::getByID($cID, 'RECENT');
                $currentPage = $page;
                $cp = new Permissions($page);
                if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                    break;
                } else {
                    $page = null; //avoid accidentally returning this $page if we're on last loop iteration
                }
            }
        }
        if (!is_object($page) && $this->loopSequence) {
            $c = Page::getCurrentPage();
            $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
            switch ($this->orderBy) {
                case 'chrono_desc':
                    return $parent->getFirstChild('cvDatePublic asc', $this->excludeSystemPages);
                    break;
                case 'chrono_asc':
                    return $parent->getFirstChild('cvDatePublic desc');
                    break;
                case 'display_desc':
                    return $parent->getFirstChild('cDisplayOrder desc');
                    break;
                case 'display_asc':
                default:
                    return $parent->getFirstChild('cDisplayOrder asc', $this->excludeSystemPages);
                    break;
            }
        }

        return $page;
    }

    public function getPreviousCollection()
    {
        $page = false;
        $db = Database::connection();
        $systemPages = '';
        if ($this->excludeSystemPages) {
            $systemPages = 'and cIsSystemPage = 0';
        }
        $cID = 1;
        $currentPage = Page::getCurrentPage();
        while ($cID > 0) {
            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic < ? and cParentID = ? ' . $systemPages . ' order by cvDatePublic desc', array($currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()));
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic > ? and cParentID = ? ' . $systemPages . ' order by cvDatePublic asc', array($currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()));
                    break;
                case 'display_desc':
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder > ? and cParentID = ? ' . $systemPages . ' order by cDisplayOrder asc', array($currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()));
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder < ? and cParentID = ? ' . $systemPages . ' order by cDisplayOrder desc', array($currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()));
                    break;
            }
            if ($cID > 0) {
                $page = Page::getByID($cID, 'RECENT');
                $currentPage = $page;
                $cp = new Permissions($page);
                if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                    break;
                } else {
                    $page = null; //avoid accidentally returning this $page if we're on last loop iteration
                }
            }
        }
        if (!is_object($page) && $this->loopSequence) {
            $c = Page::getCurrentPage();
            $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
            switch ($this->orderBy) {
                case 'chrono_desc':
                    return $parent->getFirstChild('cvDatePublic desc');
                    break;
                case 'chrono_asc':
                    return $parent->getFirstChild('cvDatePublic asc', $this->excludeSystemPages);
                    break;
                case 'display_desc':
                    return $parent->getFirstChild('cDisplayOrder asc', $this->excludeSystemPages);
                    break;
                case 'display_asc':
                default:
                    return $parent->getFirstChild('cDisplayOrder desc');
                    break;
            }
        }

        return $page;
    }
}
