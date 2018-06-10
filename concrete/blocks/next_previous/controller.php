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
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        for ($page = Page::getCurrentPage();;) {
            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder > ?) or cvDatePublic > ?) and cParentID = ?  order by cvDatePublic asc, cDisplayOrder asc', [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]);
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder < ?) or cvDatePublic < ?) and cParentID = ?  order by cvDatePublic desc, cDisplayOrder desc', [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]);
                    break;
                case 'display_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and cDisplayOrder < ? and cParentID = ? order by cDisplayOrder desc', [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]);
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and  cDisplayOrder > ? and cParentID = ? order by cDisplayOrder asc', [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]);
                    break;
            }
            if ($cID <= 0) {
                if ($this->loopSequence) {
                    $c = Page::getCurrentPage();
                    $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
                    switch ($this->orderBy) {
                        case 'chrono_desc':
                            return $parent->getFirstChild('cvDatePublic asc, cDisplayOrder asc');
                            break;
                        case 'chrono_asc':
                            return $parent->getFirstChild('cvDatePublic desc, cDisplayOrder desc');
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
				return false;
            }
            $page = Page::getByID($cID, 'ACTIVE');
            $cp = new Permissions($page);
            if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                return $page;
            }
        }
    }

    /**
     * @return bool|Page
     */
    public function getPreviousCollection()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        for ($page = Page::getCurrentPage();;) {
            switch ($this->orderBy) {
                case 'chrono_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder < ?) or cvDatePublic < ?) and cParentID = ?  order by cvDatePublic desc, cDisplayOrder desc', [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]);
                    break;
                case 'chrono_asc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder > ?) or cvDatePublic > ?) and cParentID = ?  order by cvDatePublic asc, cDisplayOrder asc', [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]);
                    break;
                case 'display_desc':
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and cDisplayOrder > ? and cParentID = ? order by cDisplayOrder asc', [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]);
                    break;
                case 'display_asc':
                default:
                    $cID = $db->GetOne('select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and  cDisplayOrder < ? and cParentID = ? order by cDisplayOrder desc', [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]);
                    break;
            }
            if ($cID <= 0) {
                if ($this->loopSequence) {
                    $c = Page::getCurrentPage();
                    $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
                    switch ($this->orderBy) {
                        case 'chrono_desc':
                            return $parent->getFirstChild('cvDatePublic desc, cDisplayOrder desc');
                            break;
                        case 'chrono_asc':
                            return $parent->getFirstChild('cvDatePublic asc, cDisplayOrder asc');
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
				return false;
            }
            $page = Page::getByID($cID, 'ACTIVE');
            $cp = new Permissions($page);
            if ($cp->canRead() && $page->getAttribute('exclude_nav') != 1) {
                return $page;
           }
        }
    }
}
