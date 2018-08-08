<?php

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;

class DragRequestData
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $dragMode;

    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $destinationPage;

    /**
     * @var \Concrete\Core\Page\Page|null
     */
    protected $destinationSibling;

    /**
     * @var \Concrete\Core\Page\Page[]
     */
    protected $originalPages;

    /**
     * @var bool|null
     */
    protected $canCopyChildren;

    /**
     * @var bool|null
     */
    protected $isSomeOriginalPageALink;

    /**
     * @var bool|null
     */
    protected $isSaveOldPagePath;

    /**
     * @var bool|null
     */
    protected $isCopyChildrenOnly;

    /**
     * @param Request $request
     * @param Application $app
     *
     * @throws \Concrete\Core\Error\UserMessageException
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->initializeDragMode();
        $this->initializeDestinationPages();
        $this->initializeOriginalPages();
    }

    /**
     * @return string
     */
    public function getDragMode()
    {
        return $this->dragMode;
    }

    /**
     * @return \Concrete\Core\Page\Page
     */
    public function getDestinationPage()
    {
        return $this->destinationPage;
    }

    /**
     * @return \Concrete\Core\Page\Page|null
     */
    public function getDestinationSibling()
    {
        return $this->destinationSibling;
    }

    /**
     * @return \Concrete\Core\Page\Page[]
     */
    public function getOriginalPages()
    {
        return $this->originalPages;
    }

    /**
     * @return \Concrete\Core\Page\Page
     */
    public function getFirstOriginalPage()
    {
        return $this->originalPages[0];
    }

    /**
     * @return bool
     */
    public function canCopyChildren()
    {
        if ($this->canCopyChildren === null) {
            $u = $this->app->make(User::class);
            if ($u->isSuperUser()) {
                $this->canCopyChildren = true;
            } else {
                $canCopyChildren = true;
                foreach ($this->originalPages as $originalPage) {
                    if ($originalPage->getCollectionPointerID() > 0) {
                        $canCopyChildren = false;
                        break;
                    }
                }
                $this->canCopyChildren = $canCopyChildren;
            }
        }

        return $this->canCopyChildren;
    }

    /**
     * @return bool
     */
    public function isSomeOriginalPageALink()
    {
        if ($this->isSomeOriginalPageALink === null) {
            $isSomeOriginalPageALink = false;
            foreach ($this->originalPages as $originalPage) {
                if ($originalPage->getCollectionPointerID()) {
                    $isSomeOriginalPageALink = true;
                    break;
                }
            }
            $this->isSomeOriginalPageALink = $isSomeOriginalPageALink;
        }

        return $this->isSomeOriginalPageALink;
    }

    /**
     * @return bool
     */
    public function isSaveOldPagePath()
    {
        if ($this->isSaveOldPagePath === null) {
            $this->isSaveOldPagePath = (bool) $this->request->request->get('saveOldPagePath', $this->request->query->get('saveOldPagePath', false));
        }

        return $this->isSaveOldPagePath;
    }

    /**
     * @return bool
     */
    public function isCopyChildrenOnly()
    {
        if ($this->isCopyChildrenOnly === null) {
            $this->isCopyChildrenOnly = (bool) $this->request->request->get('copyChildrenOnly', $this->request->query->get('copyChildrenOnly', false));
        }

        return $this->isCopyChildrenOnly;
    }

    protected function initializeDragMode()
    {
        $this->dragMode = $this->request->request->get('dragMode', $this->request->query->get('dragMode', ''));
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function initializeDestinationPages()
    {
        $cID = $this->request->request->get('destCID', $this->request->query->get('destCID'));
        $this->destinationPage = is_int($cID) || (is_string($cID) && is_numeric($cID)) ? Page::getByID($cID) : null;
        if (!$this->destinationPage || $this->destinationPage->isError()) {
            throw new UserMessageException(t('Error loading the destination page.'));
        }
        if ($this->dragMode === 'after' || $this->dragMode === 'before') {
            $siblingCID = $this->request->request->get('destSibling', $this->request->query->get('destSibling'));
            if ($siblingCID) {
                $this->destinationSibling = is_int($siblingCID) || (is_string($siblingCID) && is_numeric($siblingCID)) ? Page::getByID($siblingCID) : null;
                if (!$this->destinationSibling || $this->destinationSibling->isError()) {
                    throw new UserMessageException(t('Error loading the destination page.'));
                }
            } else {
                $this->destinationSibling = $this->destinationPage;
                $this->destinationPage = Page::getByID($this->destinationSibling->getCollectionParentID());
                if (!$this->destinationPage || $this->destinationPage->isError()) {
                    throw new UserMessageException(t('Error loading the destination page.'));
                }
            }
        } else {
            $this->destinationSibling = null;
        }
        if ($this->destinationPage->isAlias()) {
            throw new UserMessageException(t('You may not move/copy/alias the chosen page(s) to that location.'));
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function initializeOriginalPages()
    {
        $origCID = $this->request->request->get('origCID', $this->request->query->get('origCID'));
        if (is_int($origCID)) {
            $collectionIDs = [$origCID];
        } elseif (is_string($origCID)) {
            $collectionIDs = explode(',', $origCID);
        } elseif (is_array($origCID)) {
            $collectionIDs = $origCID;
        } else {
            $collectionIDs = [];
        }
        $collectionIDs = array_unique(array_map(
            function ($v) {
                return is_int($v) || (is_string($v) && is_numeric($v)) ? (int) $v : 0;
            },
            $collectionIDs
        ));
        $this->originalPages = [];
        $destinationPageChecker = new Checker($this->destinationPage);
        foreach ($collectionIDs as $cID) {
            $c = Page::getByID($cID);
            if (!$c || $c->isError()) {
                throw new UserMessageException(t('Error loading the original page.'));
            }
            $pc = new Checker($c);
            if (!$pc->canRead()) {
                throw new UserMessageException(t('You cannot view the source page(s).'));
            }
            if (!$pc->canMoveOrCopyPage()) {
                throw new UserMessageException(t('You cannot move or copy the source page(s).'));
            }
            if (!$c->canMoveCopyTo($this->destinationPage)) {
                throw new UserMessageException(t('You may not move/copy/alias the chosen page(s) to that location.'));
            }
            $ct = $c->getPageTypeObject();
            if (!$destinationPageChecker->canAddSubpage($ct)) {
                throw new UserMessageException(t('You do not have sufficient privileges to add this page or these pages to this destination.'));
            }
            $this->originalPages[] = $c;
        }
        if (empty($this->originalPages)) {
            throw new UserMessageException(t('No original page specified.'));
        }
    }
}
