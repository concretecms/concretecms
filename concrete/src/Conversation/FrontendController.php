<?php

namespace Concrete\Core\Conversation;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Block\CoreConversation\Controller as CoreConversationBlockController;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;

/**
 * Base class for all the conversations frontend controllers.
 */
abstract class FrontendController extends Controller
{
    /**
     * The name of the request field that contains the page ID.
     *
     * @var string
     */
    protected const FIELDNAME_PAGEID = 'cID';

    /**
     * The name of the request field that contains the area handle.
     *
     * @var string
     */
    protected const FIELDNAME_AREAHANDLE = 'blockAreaHandle';

    /**
     * The name of the request field that contains the block ID.
     *
     * @var string
     */
    protected const FIELDNAME_BLOCKID = 'bID';
    
    /**
     * The page ID as specified by the request.
     *
     * @var int|false|null FALSE if and only if not yet initialized
     */
    private $pageID = false;

    /**
     * The page as specified by the request.
     *
     * @var \Concrete\Core\Page\Page|null NULL if and only if not yet initialized
     */
    private $page;

    /**
     * The handle of the area as specified by the request.
     *
     * @var string|null NULL if and only if not yet initialized
     */
    private $areaHandle;

    /**
     * The area as specified by the request.
     *
     * @var \Concrete\Core\Area\Area|null NULL if and only if not yet initialized
     */
    private $area;

    /**
     * The block ID as specified by the request.
     *
     * @var int|false|null FALSE if and only if not yet initialized
     */
    private $blockID = false;

    /**
     * The block instance as specified by the request.
     *
     * @var \Concrete\Core\Block\Block|null NULL if and only if not yet initialized
     */
    private $block;

    /**
     * The block controller as specified by the request.
     *
     * @var \Concrete\Block\CoreConversation\Controller|null NULL if and only if not yet initialized
     */
    private $blockController;

    /**
     * The Conversation instance for in tbe block specified by the request.
     *
     * @var Conversation
     */
    private $blockConversation;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::setRequest()
     */
    public function setRequest($request)
    {
        $this->pageID = false;
        $this->page = null;
        $this->areaHandle = null;
        $this->area = null;
        $this->blockID = false;
        $this->block = null;
        $this->blockController = null;
        $this->blockConversation = null;
        parent::setRequest($request);
    }

    /**
     * Get the page ID as specified by the request.
     *
     * @return int|null Returns NULL if not received, or not a positive integer
     */
    protected function getPageID(): ?int
    {
        if ($this->pageID === false) {
            $pageID = $this->request->request->get(static::FIELDNAME_PAGEID);
            $this->pageID = $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : null;
        }

        return $this->pageID;
    }

    /**
     * Get the page as specified by the request.
     *
     * @throws \Concrete\Core\Error\UserMessageException if the page ID has not been received, if the page couldn't be found, or if it's not visible by the current user
     */
    protected function getPage(): Page
    {
        if ($this->page === null) {
            $pageID = $this->getPageID();
            $page = $pageID === null ? null : Page::getByID($pageID);
            if (!$page || $page->isError()) {
                throw new UserMessageException(t('Unable to find the specified page.'));
            }
            $pp = new Checker($page);
            if (!$pp->canViewPage()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $this->page = $page;
        }

        return $this->page;
    }

    /**
     * Get the handle of the area as specified by the request.
     *
     * @return string it may be empty
     */
    protected function getAreaHandle(): string
    {
        if ($this->areaHandle === null) {
            $areaHandle = $this->request->request->get(static::FIELDNAME_AREAHANDLE);
            $this->areaHandle = is_string($areaHandle) ? $areaHandle : '';
        }

        return $this->areaHandle;
    }

    /**
     * Get the area as specified by the details of the request.
     *
     * @throws \Concrete\Core\Error\UserMessageException if the page ID has not been received, if the page couldn't be found, if it's not visible by the current user, or if the area couldn't be found
     *
     * @return \Concrete\Core\Area\Area
     */
    protected function getArea(): Area
    {
        if ($this->area === null) {
            $page = $this->getPage();
            $areaHandle = $this->getAreaHandle();
            $area = $areaHandle === '' ? null : Area::get($page, $areaHandle);
            if ($area === null) {
                throw new UserMessageException(t('Unable to find the specified area.'));
            }
            $this->area = $area;
        }

        return $this->area;
    }

    /**
     * Get the block ID as specified by the request.
     *
     * @return int|null Returns NULL if not received, or not a positive integer
     */
    protected function getBlockID(): ?int
    {
        if ($this->blockID === false) {
            $blockID = $this->request->request->get(static::FIELDNAME_BLOCKID);
            $this->blockID = $this->app->make(Numbers::class)->integer($blockID, 1) ? (int) $blockID : null;
        }

        return $this->blockID;
    }

    /**
     * Get the block instance as specified by the details of the request.
     *
     * @throws \Concrete\Core\Error\UserMessageException in case of missing/invalid data, or access denied problems
     */
    protected function getBlock(): Block
    {
        if ($this->block === null) {
            $page = $this->getPage();
            $area = $this->getArea();
            $blockID = $this->getBlockID();
            $block = $blockID === null ? null : Block::getByID($blockID, $page, $area);
            if (!$block || $block->isError()) {
                throw new UserMessageException(t('Unable to find the specified block'));
            }
            if ($block->getBlockTypeHandle() !== BLOCK_HANDLE_CONVERSATION) {
                throw new UserMessageException(t('Invalid block'));
            }
            if ((int) $block->getBlockActionCollectionID() !== $this->getPageID()) {
                throw new UserMessageException(t('Invalid block'));
            }
            $p = new Checker($block);
            if (!$p->canViewBlock()) {
                throw new UserMessageException(t('You do not have permission to view this conversation'));
            }
            $this->block = $block;
        }

        return $this->block;
    }

    /**
     * Get the block controller as specified by the request.
     *
     * @throws \Concrete\Core\Error\UserMessageException in case of missing/invalid data, or access denied problems
     */
    protected function getBlockController(): CoreConversationBlockController
    {
        if ($this->blockController === null) {
            $this->blockController = $this->getBlock()->getController();
        }

        return $this->blockController;
    }

    /**
     * Get the block controller as specified by the request.
     *
     * @throws \Concrete\Core\Error\UserMessageException in case of missing/invalid data, or access denied problems
     */
    protected function getBlockConversation(): Conversation
    {
        if ($this->blockConversation === null) {
            $blockConversation = $this->getBlockController()->getConversationObject();
            if (!($blockConversation instanceof Conversation)) {
                throw new UserMessageException(t('Invalid Conversation.'));
            }
            $this->blockConversation = $blockConversation;
        }

        return $this->blockConversation;
    }
}
