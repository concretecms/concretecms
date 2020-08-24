<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Block\CoreConversation\Controller as CoreConversationBlockController;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\Message\MessageList;
use Concrete\Core\Conversation\Message\ThreadedList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\SanitizeService;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class ViewAjax extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/view_ajax';

    /**
     * @var \Concrete\Core\Page\Page|null
     */
    private $pageObject;

    /**
     * @var \Concrete\Core\Block\Block|null
     */
    private $blockObject;

    /**
     * @var \Concrete\Block\CoreConversation\Controller|null
     */
    private $blockController;

    public function view(): ?Response
    {
        $this->request->setCurrentPage($this->getPageObject());
        $conversation = $this->getConversation();
        $messageList = $this->buildMessageList($conversation);
        $this->configureMessageList($conversation, $messageList);
        $this->setElementSets($conversation, $messageList);

        return null;
    }

    protected function getPageID(): ?int
    {
        $pageID = $this->request->request->get('cID');

        return $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : null;
    }

    protected function getPageObject(): Page
    {
        if ($this->pageObject === null) {
            $pageID = $this->getPageID();
            $pageObject = $pageID === null ? null : Page::getByID($pageID);
            if (!$pageObject || $pageObject->isError()) {
                throw new UserMessageException(t('Unable to find the specified page.'));
            }
            $pp = new Checker($pageObject);
            if (!$pp->canViewPage()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $this->pageObject = $pageObject;
        }

        return $this->pageObject;
    }

    protected function getBlockID(): ?int
    {
        $blockID = $this->request->request->get('blockID');

        return $this->app->make(Numbers::class)->integer($blockID, 1) ? (int) $blockID : null;
    }

    protected function getAreaHandle(): string
    {
        $areaHandle = $this->request->request->get('blockAreaHandle');

        return is_string($areaHandle) ? $areaHandle : '';
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getBlockObject(): Block
    {
        if ($this->blockObject === null) {
            $pageObj = $this->getPageObject();
            $areaObj = Area::get($pageObj, $this->getAreaHandle());
            $blockID = $this->getBlockID();
            $blockObj = $blockID === null ? null : Block::getByID($blockID, $pageObj, $areaObj);
            if (!$blockObj || $blockObj->isError()) {
                throw new UserMessageException(t('Unable to find the specified block'));
            }
            if ($blockObj->getBlockTypeHandle() !== BLOCK_HANDLE_CONVERSATION) {
                throw new UserMessageException(t('Invalid block'));
            }
            if ($blockObj->getBlockActionCollectionID() != $this->getPageID()) {
                throw new UserMessageException(t('Invalid block'));
            }
            $p = new Checker($blockObj);
            if (!$p->canViewBlock()) {
                // block read permissions check
                throw new UserMessageException(t('You do not have permission to view this conversation'));
            }
            $this->blockObject = $blockObj;
        }

        return $this->blockObject;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getBlockController(): CoreConversationBlockController
    {
        if ($this->blockController === null) {
            $this->blockController = $this->getBlockObject()->getController();
        }

        return $this->blockController;
    }

    protected function getConversationID(): ?int
    {
        $conversationID = $this->request->request->get('cnvID');

        return $this->app->make(Numbers::class)->integer($conversationID, 1) ? (int) $conversationID : null;
    }

    protected function getConversation(): Conversation
    {
        $conversationID = $this->getConversationID();
        $conversation = $conversationID === null ? null : Conversation::getByID($conversationID);
        if ($conversation === null) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }
        if ($this->getBlockController()->getConversationObject()->getConversationID() != $conversationID) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }

        return $conversation;
    }

    protected function isOrderingEnabled(): bool
    {
        return (bool) $this->request->request->get('enableOrdering');
    }

    protected function isEnableTopCommentReviews(): bool
    {
        return (bool) $this->request->request->get('enableTopCommentReviews');
    }

    protected function isDisplaySocialLinks(): bool
    {
        return (bool) $this->request->request->get('displaySocialLinks');
    }

    protected function getDisplayPostingForm(): string
    {
        $displayPostingForm = $this->request->request->get('displayPostingForm');

        return is_string($displayPostingForm) ? $displayPostingForm : '';
    }

    protected function getPostingEnabled(Conversation $conversation): int
    {
        $cp = new Checker($conversation);
        if (!$cp->canAddConversationMessage()) {
            return Conversation::POSTING_DISABLED_PERMISSIONS;
        }

        return $this->request->request->get('enablePosting') ? Conversation::POSTING_ENABLED : Conversation::POSTING_DISABLED_MANUALLY;
    }

    protected function isPaginate(): bool
    {
        return (bool) $this->request->request->get('paginate');
    }

    protected function isCommentRatingEnabled(): bool
    {
        return (bool) $this->request->request->get('enableCommentRating');
    }

    protected function isAttachmentsEnabled(): bool
    {
        return (bool) $this->request->request->get('attachmentsEnabled');
    }

    protected function isAttachmentOverridesEnabled(): bool
    {
        return (bool) $this->request->request->get('attachmentOverridesEnabled');
    }

    protected function getDateFormat(): string
    {
        $dateFormat = $this->request->request->get('dateFormat');

        return is_string($dateFormat) ? $dateFormat : '';
    }

    protected function getCustomDateFormat(): string
    {
        $dateFormat = $this->request->request->get('customDateFormat');

        return is_string($dateFormat) ? $dateFormat : '';
    }

    protected function getDisplayMode(): string
    {
        $modes = ['flat', 'threaded'];
        $mode = $this->request->request->get('displayMode');

        return in_array($mode, $modes, true) ? $mode : 'threaded';
    }

    protected function getAddMessageLabel(): string
    {
        $addMessageLabel = $this->request->request->get('addMessageLabel');
        if (is_string($addMessageLabel) && $addMessageLabel !== '') {
            return $this->app->make(SanitizeService::class)->sanitizeString($addMessageLabel);
        }

        return t('Add Message');
    }

    protected function isDisplayForm(): bool
    {
        $task = $this->request->request->get('task');

        return $task === 'get_messages' ? false : true;
    }

    protected function getOrderBy(): string
    {
        $orderBys = ['date_desc', 'date_asc', 'rating'];
        $orderBy = $this->request->request->get('orderBy');

        return in_array($orderBy, $orderBys, true) ? $orderBy : '';
    }

    protected function getItemsPerPage(): ?int
    {
        $itemsPerPage = $this->request->request->get('itemsPerPage');

        return $this->app->make(Numbers::class)->integer($itemsPerPage, 1) ? (int) $itemsPerPage : null;
    }

    /**
     * @return \Concrete\Core\Conversation\Message\MessageList|\Concrete\Core\Conversation\Message\ThreadedList
     */
    protected function buildMessageList(Conversation $conversation): object
    {
        switch ($this->getDisplayMode()) {
            case 'flat':
                $messageList = new MessageList();
                $messageList->filterByConversation($conversation);
                break;
            case 'threaded':
            default:
                $messageList = new ThreadedList($conversation);
                break;
        }

        return $messageList;
    }

    /**
     * @param \Concrete\Core\Conversation\Message\MessageList|\Concrete\Core\Conversation\Message\ThreadedList $messageList
     */
    protected function configureMessageList(Conversation $conversation, object $messageList): void
    {
        switch ($this->getOrderBy()) {
            case 'date_asc':
                $messageList->sortByDateAscending();
                break;
            case 'date_desc':
                $messageList->sortByDateDescending();
                break;
            case 'rating':
                $messageList->sortByRating();
                break;
        }
        $itemsPerPage = $this->isPaginate() ? $this->getItemsPerPage() : null;
        if ($itemsPerPage === null) {
            $messageList->setItemsPerPage(-1);
        } else {
            $messageList->setItemsPerPage($itemsPerPage);
        }
    }

    /**
     * @param \Concrete\Core\Conversation\Message\MessageList|\Concrete\Core\Conversation\Message\ThreadedList $messageList
     */
    protected function setElementSets(Conversation $conversation, object $messageList): void
    {
        $summary = $messageList->getSummary();
        $this->set('conversation', $conversation);
        $this->set('cID', $this->getPageID());
        $this->set('bID', $this->getBlockID());
        $this->set('messages', $messageList->getPage());
        $this->set('displayMode', $this->getDisplayMode());
        $this->set('displayForm', $this->isDisplayForm());
        $this->set('enablePosting', $this->getPostingEnabled($conversation));
        $this->set('addMessageLabel', $this->getAddMessageLabel());
        $this->set('currentPage', 1);
        $this->set('totalPages', $summary->pages);
        $this->set('orderBy', $this->getOrderBy());
        $this->set('enableOrdering', $this->isOrderingEnabled());
        $this->set('enableTopCommentReviews', $this->isEnableTopCommentReviews());
        $this->set('displaySocialLinks', $this->isDisplaySocialLinks());
        $this->set('displayPostingForm', $this->getDisplayPostingForm());
        $this->set('enableCommentRating', $this->isCommentRatingEnabled());
        $this->set('dateFormat', $this->getDateFormat());
        $this->set('customDateFormat', $this->getCustomDateFormat());
        $this->set('blockAreaHandle', $this->getAreaHandle());
        $this->set('attachmentsEnabled', $this->isAttachmentsEnabled());
        $this->set('attachmentOverridesEnabled', $this->isAttachmentOverridesEnabled());
    }
}
