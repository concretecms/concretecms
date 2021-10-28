<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\MessageList;
use Concrete\Core\Conversation\Message\ThreadedList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\SanitizeService;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class ViewAjax extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Conversation\Conversation::FIELDNAME_BLOCKID
     */
    protected const FIELDNAME_BLOCKID = 'blockID';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/view_ajax';

    public function view(): ?Response
    {
        $this->request->setCurrentPage($this->getPage());
        $conversation = $this->getConversation();
        $messageList = $this->buildMessageList($conversation);
        $this->configureMessageList($conversation, $messageList);
        $this->setElementSets($conversation, $messageList);

        return null;
    }

    protected function getConversationID(): ?int
    {
        $conversationID = $this->request->request->get('cnvID');

        return $this->app->make(Numbers::class)->integer($conversationID, 1) ? (int) $conversationID : null;
    }

    protected function getConversation(): Conversation
    {
        $conversation = $this->getBlockConversation();
        if ((int) $conversation->getConversationID() !== $this->getConversationID()) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }

        return $conversation;
    }

    protected function getEnabledPosting(Conversation $conversation): int
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
        $this->set('enablePosting', $this->getEnabledPosting($conversation));
        $this->set('addMessageLabel', $this->getAddMessageLabel());
        $this->set('currentPage', 1);
        $this->set('totalPages', $summary->pages);
        $this->set('orderBy', $this->getOrderBy());
        $this->set('enableOrdering', (bool) $this->request->request->get('enableOrdering'));
        $this->set('enableTopCommentReviews', (bool) $this->request->request->get('enableTopCommentReviews'));
        $this->set('displaySocialLinks', (bool) $this->request->request->get('displaySocialLinks'));
        $this->set('displayPostingForm', (string) $this->request->request->get('displayPostingForm'));
        $this->set('enableCommentRating', (bool) $this->request->request->get('enableCommentRating'));
        $this->set('dateFormat', (string) $this->request->request->get('dateFormat'));
        $this->set('customDateFormat', (string) $this->request->request->get('customDateFormat'));
        $this->set('blockAreaHandle', $this->getAreaHandle());
        $this->set('attachmentsEnabled', (bool) $this->request->request->get('attachmentsEnabled'));
        $this->set('attachmentOverridesEnabled', (bool) $this->request->request->get('attachmentOverridesEnabled'));
    }
}
