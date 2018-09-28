<?php

namespace Concrete\Block\NextPrevious;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

class Controller extends BlockController
{
    protected $btTable = 'btNextPrevious';

    protected $btInterfaceWidth = 430;

    protected $btInterfaceHeight = 400;

    protected $btCacheBlockRecord = true;

    protected $btWrapperClass = 'ccm-ui';

    /**
     * The label to go to the previous page.
     *
     * @var string
     */
    public $previousLabel;

    /**
     * The label to go to the next page.
     *
     * @var string
     */
    public $nextLabel;

    /**
     * The label of the parent page.
     *
     * @var string
     */
    public $parentLabel;

    /**
     * Whether the navigation should be looped.
     *
     * 0 = don't loop
     * 1 = loop (default)
     *
     * @var int
     */
    public $loopSequence;

    /**
     * How to order the sibling pages.
     *
     * @example E.g. <code>display_asc</code>
     *
     * @var string
     */
    public $orderBy;

    public function getBlockTypeDescription()
    {
        return t('Navigate through sibling pages.');
    }

    public function getBlockTypeName()
    {
        return t('Next & Previous Nav');
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

        $args['loopSequence'] = (int) $args['loopSequence'];

        parent::save($args);
    }

    /**
     * @return Page|false
     */
    public function getNextCollection()
    {
        return $this->getNextPreviousCollection(false);
    }

    /**
     * @return Page|false
     */
    public function getPreviousCollection()
    {
        return $this->getNextPreviousCollection(true);
    }

    /**
     * @param bool $previous
     *
     * @return Page|false
     */
    private function getNextPreviousCollection($previous)
    {
        $result = false;
        $reverseMap = [
            'chrono_asc' => 'chrono_desc',
            'chrono_desc' => 'chrono_asc',
            'display_desc' => 'display_asc',
            'display_asc' => 'display_desc',
        ];
        $orderBy = $this->orderBy && isset($reverseMap[$this->orderBy]) ? $this->orderBy : 'display_asc';
        if ($previous) {
            $orderBy = $reverseMap[$orderBy];
        }
        $db = $this->app->make(Connection::class);
        for ($page = Page::getCurrentPage(); $page && !$page->isError();) {
            switch ($orderBy) {
                case 'chrono_desc':
                    $cID = $db->fetchColumn(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder > ?) or cvDatePublic > ?) and cParentID = ?  order by cvDatePublic asc, cDisplayOrder asc',
                        [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]
                    );
                    break;
                case 'chrono_asc':
                    $cID = $db->fetchColumn(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where Pages.cID <> ? and cvIsApproved = 1 and ((cvDatePublic = ? and cDisplayOrder < ?) or cvDatePublic < ?) and cParentID = ?  order by cvDatePublic desc, cDisplayOrder desc',
                        [$page->getCollectionID(), $page->getCollectionDatePublic(), $page->getCollectionDisplayOrder(), $page->getCollectionDatePublic(), $page->getCollectionParentID()]
                    );
                    break;
                case 'display_desc':
                    $cID = $db->fetchColumn(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and cDisplayOrder < ? and cParentID = ? order by cDisplayOrder desc',
                        [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]
                    );
                    break;
                case 'display_asc':
                    $cID = $db->fetchColumn(
                        'select Pages.cID from Pages inner join CollectionVersions cv on Pages.cID = cv.cID where cvIsApproved = 1 and Pages.cID <> ? and  cDisplayOrder > ? and cParentID = ? order by cDisplayOrder asc',
                        [$page->getCollectionID(), $page->getCollectionDisplayOrder(), $page->getCollectionParentID()]
                    );
                    break;
            }
            if ($cID !== false) {
                $page = Page::getByID($cID, 'ACTIVE');
                if (!$page->getAttribute('exclude_nav')) {
                    $cp = new Permissions($page);
                    if ($cp->canRead()) {
                        $result = $page;
                        break;
                    }
                }
            } else {
                if ($this->loopSequence) {
                    $c = Page::getCurrentPage();
                    $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
                    switch ($orderBy) {
                        case 'chrono_desc':
                            $sibling = $parent->getFirstChild('cvDatePublic asc, cDisplayOrder asc');
                            break;
                        case 'chrono_asc':
                            $sibling = $parent->getFirstChild('cvDatePublic desc, cDisplayOrder desc');
                            break;
                        case 'display_desc':
                            $sibling = $parent->getFirstChild('cDisplayOrder desc');
                            break;
                        case 'display_asc':
                            $sibling = $parent->getFirstChild('cDisplayOrder asc');
                            break;
                    }
                    if ($sibling && !$sibling->isError()) {
                        if (!$sibling->getAttribute('exclude_nav')) {
                            $cp = new Permissions($sibling);
                            if ($cp->canRead()) {
                                $result = $sibling;
                            }
                        }
                    }
                }
                break;
            }
        }

        return $result;
    }
}
