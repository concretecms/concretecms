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
     * Drag operation: move page(s).
     *
     * @var string
     */
    const OPERATION_MOVE = 'MOVE';

    /**
     * Drag operation: alias page(s).
     *
     * @var string
     */
    const OPERATION_ALIAS = 'ALIAS';

    /**
     * Drag operation: copy page(s).
     *
     * @var string
     */
    const OPERATION_COPY = 'COPY';

    /**
     * Drag operation: copy page(s) and their sub-pages.
     *
     * @var string
     */
    const OPERATION_COPYALL = 'COPY_ALL';

    /**
     * Drag operation: copy most recent version of a page to another page.
     *
     * @var string
     */
    const OPERATION_COPYVERSION = 'COPY_VERSION';

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
    protected $isSomeOriginalPageWithChildren;

    /**
     * @var bool|null
     */
    protected $isSomeOriginalPageAnAlias;

    /**
     * @var bool|null
     */
    protected $isSomeOriginalPageAnExternalLink;

    /**
     * @var bool|null
     */
    protected $isSaveOldPagePath;

    /**
     * @var bool|null
     */
    protected $isCopyChildrenOnly;

    /**
     * Array keys are the OPERATION_... constant values, array values are the reasons why the operation can't be performed (or empty string if they can be performed).
     *
     * @var array
     */
    protected $operationErrors = [];

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
     * Get a Page instance if and only if there's just one original page.
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getSingleOriginalPage()
    {
        $originalPages = $this->getOriginalPages();

        return count($originalPages) === 1 ? $originalPages[0] : null;
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

    /**
     * Check if an operation can be performed.
     *
     * @param string $operation The value of one of the OPERATION_... constants
     *
     * @return bool
     */
    public function canDo($operation)
    {
        return $this->whyCantDo($operation) === '';
    }

    /**
     * Check if at least one operation can be performed.
     *
     * @param string[] $operations The values of one of the OPERATION_... constants
     *
     * @return bool
     */
    public function canDoAnyOf(array $operations)
    {
        foreach ($operations as $operation) {
            if ($this->canDo($operation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the reason why an operation can't be performed.
     *
     * @param string $operation The value of one of the OPERATION_... constants.
     *
     * @return string empty string if the operation CAN be performed
     */
    public function whyCantDo($operation)
    {
        if (!is_string($operation)) {
            return 'Invalid $operation';
        }
        if (!isset($this->operationErrors[$operation])) {
            switch ($operation) {
                case static::OPERATION_MOVE:
                    $error = $this->whyCantMove();
                    break;
                case static::OPERATION_ALIAS:
                    $error = $this->whyCantAlias();
                    break;
                case static::OPERATION_COPY:
                    $error = $this->whyCantCopy();
                    break;
                case static::OPERATION_COPYALL:
                    $error = $this->whyCantDo(static::OPERATION_COPY);
                    if ($error === '') {
                        $error = $this->whyCantCopyAll();
                    }
                    break;
                case static::OPERATION_COPYVERSION:
                    $error = $this->whyCantCopyVersion();
                    break;
                default:
                    return 'Invalid $operation';
            }
            $this->operationErrors[$operation] = $error;
        }

        return $this->operationErrors[$operation];
    }

    /**
     * @return bool
     */
    protected function isSomeOriginalPageWithChildren()
    {
        if ($this->isSomeOriginalPageWithChildren === null) {
            $isSomeOriginalPageWithChildren = false;
            foreach ($this->getOriginalPages() as $originalPage) {
                if (!empty($originalPage->getCollectionChildrenArray(true))) {
                    $isSomeOriginalPageWithChildren = true;
                }
            }
            $this->isSomeOriginalPageWithChildren = $isSomeOriginalPageWithChildren;
        }

        return $this->isSomeOriginalPageWithChildren;
    }

    /**
     * @return bool
     */
    protected function isSomeOriginalPageAnAlias()
    {
        if ($this->isSomeOriginalPageAnAlias === null) {
            $isSomeOriginalPageAnAlias = false;
            foreach ($this->getOriginalPages() as $originalPage) {
                if ($originalPage->getCollectionPointerID()) {
                    $isSomeOriginalPageAnAlias = true;
                    break;
                }
            }
            $this->isSomeOriginalPageAnAlias = $isSomeOriginalPageAnAlias;
        }

        return $this->isSomeOriginalPageAnAlias;
    }

    /**
     * @return bool
     */
    protected function isSomeOriginalPageAnExternalLink()
    {
        if ($this->isSomeOriginalPageAnExternalLink === null) {
            $isSomeOriginalPageAnExternalLink = false;
            foreach ($this->getOriginalPages() as $originalPage) {
                if ($originalPage->isExternalLink()) {
                    $isSomeOriginalPageAnExternalLink = true;
                    break;
                }
            }
            $this->isSomeOriginalPageAnExternalLink = $isSomeOriginalPageAnExternalLink;
        }

        return $this->isSomeOriginalPageAnExternalLink;
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
        if ($this->getDragMode() === 'after' || $this->getDragMode() === 'before') {
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
        if ($this->destinationPage->isExternalLink()) {
            throw new UserMessageException(t('The destination is an external link.'));
        }
        if ($this->destinationPage->isAlias()) {
            throw new UserMessageException(t('The destination is an alias page.'));
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
            $this->originalPages[] = $c;
        }
        if (empty($this->originalPages)) {
            throw new UserMessageException(t('No original page specified.'));
        }
    }

    /**
     * Get the reason why the move operation can't be performed.
     *
     * @return string empty string if the operation CAN be performed
     */
    protected function whyCantMove()
    {
        $destinationPageChecker = new Checker($this->getDestinationPage());
        $destinationPageID = $this->getDestinationPage()->getCollectionID();
        foreach ($this->getOriginalPages() as $originalPage) {
            if ($originalPage->getCollectionParentID() == $destinationPageID) {
                return t('"%1$s" is already the parent page of "%2$s".', $this->getDestinationPage()->getCollectionName(), $originalPage->getCollectionName());
            }
            $originalPageChecker = new Checker($originalPage);
            if (!$originalPageChecker->canMoveOrCopyPage()) {
                return t('You don\'t have the permission move the page "%s".', $originalPage->getCollectionName());
            }
            if ($originalPage->getCollectionID() == $destinationPageID) {
                return t('It\'s not possible to move the page "%s" under itself.', $originalPage->getCollectionName());
            }
            if (in_array($destinationPageID, $originalPage->getCollectionChildrenArray())) {
                return t('It\'s not possible to move the page "%s" under one of its child pages.', $originalPage->getCollectionName());
            }
            $originalPageType = $originalPage->getPageTypeObject();
            if (!$destinationPageChecker->canAddSubpage($originalPageType)) {
                return t('You do not have sufficient privileges to move the page "%1$s" under "%2$s".', $originalPage->getCollectionName(), $this->getDestinationPage()->getCollectionName());
            }
        }

        return '';
    }

    /**
     * Get the reason why the alias operation can't be performed.
     *
     * @return string empty string if the operation CAN be performed
     */
    protected function whyCantAlias()
    {
        if ($this->isSomeOriginalPageAnAlias()) {
            return t('It\'s not possible to create aliases of aliases.');
        }
        if ($this->isSomeOriginalPageAnExternalLink()) {
            return t('It\'s not possible to create aliases of external links. Just create a new external link in the new location.');
        }
        $destinationPageChecker = new Checker($this->getDestinationPage());
        foreach ($this->getOriginalPages() as $originalPage) {
            $originalPageChecker = new Checker($originalPage);
            if (!$originalPageChecker->canMoveOrCopyPage()) {
                return t('You don\'t have the permission to create an alias of the page "%s".', $originalPage->getCollectionName());
            }
            $originalPageType = $originalPage->getPageTypeObject();
            if (!$destinationPageChecker->canAddSubpage($originalPageType)) {
                return t('You do not have sufficient privileges to alias the page "%1$s" under "%2$s".', $originalPage->getCollectionName(), $this->getDestinationPage()->getCollectionName());
            }
        }

        return '';
    }

    /**
     * Get the reason why the copy operation can't be performed.
     *
     * @return string empty string if the operation CAN be performed
     */
    protected function whyCantCopy()
    {
        $destinationPageChecker = new Checker($this->getDestinationPage());
        foreach ($this->getOriginalPages() as $originalPage) {
            $originalPageChecker = new Checker($originalPage);
            if (!$originalPageChecker->canMoveOrCopyPage()) {
                return t('You don\'t have the permission copy the page "%s".', $originalPage->getCollectionName());
            }
            $originalPageType = $originalPage->getPageTypeObject();
            if (!$destinationPageChecker->canAddSubpage($originalPageType)) {
                return t('You do not have sufficient privileges to copy the page "%1$s" under "%2$s".', $originalPage->getCollectionName(), $this->getDestinationPage()->getCollectionName());
            }
        }

        return '';
    }

    /**
     * Get the reason why the copy-all operation can't be performed (NOTE: this DOES NOT include the checks performed in the whyCantCopy() method).
     *
     * @return string empty string if the operation CAN be performed
     */
    protected function whyCantCopyAll()
    {
        $u = $this->app->make(User::class);
        if (!$u->isSuperUser() && $this->isSomeOriginalPageAnAlias()) {
            return t('Only the administrator can copy aliased pages.');
        }
        $destinationPageID = $this->getDestinationPage()->getCollectionID();
        $somePageWithChildren = false;
        foreach ($this->getOriginalPages() as $originalPage) {
            if ($originalPage->getCollectionID() == $destinationPageID) {
                return t('It\'s not possible to copy the page "%s" and its child pages under the page itself.', $originalPage->getCollectionName());
            }
            if (in_array($destinationPageID, $originalPage->getCollectionChildrenArray())) {
                return t('It\'s not possible to copy the page "%s" and its child pages under one of its child pages.', $originalPage->getCollectionName());
            }
            if ($somePageWithChildren === false && !$originalPage->isAliasPageOrExternalLink() && $originalPage->getNumChildrenDirect() > 0) {
                $somePageWithChildren = true;
            }
        }
        if ($somePageWithChildren === false) {
            return count($this->getOriginalPages()) === 1 ? t("The page doesn't have any child pages.") : t("The pages don't have any child pages.");
        }

        return '';
    }

    /**
     * Get the reason why the copy-version operation can't be performed.
     *
     * @return string empty string if the operation CAN be performed
     */
    protected function whyCantCopyVersion()
    {
        $originalPage = $this->getSingleOriginalPage();
        if ($originalPage === null) {
            return t("It's possible to copy just one page version at a time.");
        }
        if ($originalPage->isExternalLink()) {
            return t("It's not possible to copy the page version of an external URL.");
        }
        if ($originalPage->isAliasPage()) {
            return t("It's not possible to copy the page version of aliases.");
        }
        $destinationPage = $this->getDestinationPage();
        if ($destinationPage->getCollectionID() == $originalPage->getCollectionID()) {
            return t("It's not possible to copy the page version of a page to the page itself.");
        }
        $pc = new Checker($destinationPage);
        if (!$pc->canWrite()) {
            return t('You don\'t have the permission to edit the contents of "%s".', $destinationPage->getCollectionName());
        }

        return '';
    }
}
