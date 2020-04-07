<?php

namespace Concrete\Block\NextPrevious;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
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
        $pageList = new PageList();
        $currentPage = Page::getCurrentPage();

        if (is_object($currentPage) && !$currentPage->isError()) {
            $pageList->filterByParentID($currentPage->getCollectionParentID());
            switch ($orderBy) {
                case 'chrono_desc':
                    $pageList->sortBy('cvDatePublic', 'asc');
                    $pageList->sortBy('cDisplayOrder', 'asc');
                    break;
                case 'chrono_asc':
                    $pageList->sortBy('cvDatePublic', 'desc');
                    $pageList->sortBy('cDisplayOrder', 'desc');
                    break;
                case 'display_desc':
                    $pageList->sortByDisplayOrderDescending();
                    break;
                case 'display_asc':
                    $pageList->sortByDisplayOrder();
                    break;
            }

            $result = $pageList->getAfter($currentPage->getCollectionID());
        }

        if (!$result) {
            if ($this->loopSequence) {
                $pages = $pageList->getResults();
                $firstPage = $pages[0];
                if (is_object($firstPage) && !$firstPage->isError()) {
                    if (!$firstPage->getAttribute('exclude_nav')) {
                        $cp = new Permissions($firstPage);
                        if ($cp->canRead()) {
                            $result = $firstPage;
                        } else {
                            $result = $pageList->getAfter($firstPage->getCollectionID());
                        }
                    } else {
                        $result = $pageList->getAfter($firstPage->getCollectionID());
                    }
                }
            }
        }

        return $result;
    }
}
