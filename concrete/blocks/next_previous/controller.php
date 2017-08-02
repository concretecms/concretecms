<?php
namespace Concrete\Block\NextPrevious;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Support\Facade\Facade;

class Controller extends BlockController
{
    protected $btTable = 'btNextPrevious';
    protected $btInterfaceWidth = "430";
    protected $btInterfaceHeight = "400";
    protected $btCacheBlockRecord = true;
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeDescription()
    {
        return t("Navigate through sibling pages.");
    }

    public function getBlockTypeName()
    {
        return t("Next & Previous Nav");
    }

    public function view()
    {
        // Next
        $nextLinkURL = '';
        $nextLinkText = '';
        $nextCollection = $this->getNextCollection();
        if (is_object($nextCollection) && !$nextCollection->isError()) {
            $nextLinkURL = $nextCollection->getCollectionLink();
            $nextLinkText = $nextCollection->getCollectionName();
        }

        $this->set('nextCollection', $nextCollection);
        $this->set('nextLinkURL', $nextLinkURL);
        $this->set('nextLinkText', $nextLinkText);
        $this->set('nextLabel', $this->nextLabel);

        // Previous
        $previousLinkURL = '';
        $previousLinkText = '';
        $previousCollection = $this->getPreviousCollection();

        if (is_object($previousCollection) && !$previousCollection->isError()) {
            $previousLinkURL = $previousCollection->getCollectionLink();
            $previousLinkText = $previousCollection->getCollectionName();
        }

        $this->set('previousCollection', $previousCollection);
        $this->set('previousLinkURL', $previousLinkURL);
        $this->set('previousLinkText', $previousLinkText);
        $this->set('previousLabel', $this->previousLabel);

        // Parent / Up
        $parentLinkURL = '';
        $parentCollection = Page::getByID(Page::getCurrentPage()->getCollectionParentID());
        if (is_object($parentCollection) && !$parentCollection->isError()) {
            $parentLinkURL = $parentCollection->getCollectionLink();
        }

        $this->set('parentCollection', $parentCollection);
        $this->set('parentLinkURL', $parentLinkURL);
        $this->set('parentLabel', $this->parentLabel);
    }

    public function add()
    {
        $this->set('nextLabel', t('Next'));
        $this->set('previousLabel', t('Previous'));
        $this->set('parentLabel', t('Up'));
        $this->set('loopSequence', 1);
        $this->set('orderBy', 'display_asc');
    }

    public function save($args)
    {
        $args += [
            'loopSequence' => 0,
        ];

        $args['loopSequence'] = intval($args['loopSequence']);

        parent::save($args);
    }

    /**
     * @return bool|Page
     */
    public function getNextCollection()
    {
        $page = false;

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $siteTreeID = \Core::make('site')->getSite()->getSiteTreeID();
        $cID = 1;
        $currentPage = Page::getCurrentPage();

        while ($cID > 0) {

            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID != ? and cvIsApproved = 1 and cvDatePublic > ? and cParentID = ? order by cvDatePublic asc',
                        [$cID, $currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()]);
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID != ? and cvIsApproved = 1 and cvDatePublic < ? and cParentID = ?  order by cvDatePublic desc',
                        [$cID, $currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()]);
                    break;
                case 'display_desc':
                    $cID = $db->GetOne(
                        'select cID from Pages where cID != ? and cDisplayOrder < ? and cParentID = ? order by cDisplayOrder desc',
                        [$cID, $currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()]);
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne(
                        'select cID from Pages where cID != ? and  cDisplayOrder > ? and cParentID = ? order by cDisplayOrder asc',
                        [$cID, $currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()]);
                    break;
            }

            if ($cID > 0) {
                $page = Page::getByID($cID, 'RECENT');
                $currentPage = $page;
                $cp = new Permissions($page);
                if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                    break;
                } else {
                    $page = false; //avoid accidentally returning this $page if we're on last loop iteration
                }
            }
        }

        if (!is_object($page) && $this->loopSequence) {
            $c = Page::getCurrentPage();
            $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
            switch ($this->orderBy) {
                case 'chrono_desc':
                    return $parent->getFirstChild('cvDatePublic asc');
                    break;
                case 'chrono_asc':
                    return $parent->getFirstChild('cvDatePublic desc');
                    break;
                case 'display_desc':
                    return $parent->getFirstChild('cDisplayOrder desc');
                    break;
                case 'display_asc':
                default:
                    return $parent->getFirstChild('cDisplayOrder asc');
                    break;
            }
        }

        return $page;
    }

    /**
     * @return bool|Page
     */
    public function getPreviousCollection()
    {
        $page = false;

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cID = 1;
        $currentPage = Page::getCurrentPage();

        while ($cID > 0) {
            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic < ? and cParentID = ?  order by cvDatePublic desc', [$currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()]);
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and cvDatePublic > ? and cParentID = ?  order by cvDatePublic asc', [$currentPage->getCollectionDatePublic(), $currentPage->getCollectionParentID()]);
                    break;
                case 'display_desc':
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder > ? and cParentID = ? order by cDisplayOrder asc', [$currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()]);
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne('select cID from Pages where cDisplayOrder < ? and cParentID = ? order by cDisplayOrder desc', [$currentPage->getCollectionDisplayOrder(), $currentPage->getCollectionParentID()]);
                    break;
            }

            if ($cID > 0) {
                $page = Page::getByID($cID, 'RECENT');
                $currentPage = $page;
                $cp = new Permissions($page);
                if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                    break;
                } else {
                    $page = false; //avoid accidentally returning this $page if we're on last loop iteration
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
                    return $parent->getFirstChild('cvDatePublic asc');
                    break;
                case 'display_desc':
                    return $parent->getFirstChild('cDisplayOrder asc');
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
