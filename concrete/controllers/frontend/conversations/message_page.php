<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\MessageList;
use Concrete\Core\Conversation\Message\ThreadedList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class MessagePage extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/message_page';

    public function view(): ?Response
    {
        $conversation = $this->getConversation();
        $messageList = $this->buildMessageList($conversation);
        $this->configureMessageList($conversation, $messageList);
        $this->set('conversation', $conversation);
        $this->set('messageList', $messageList);
        $this->set('pageIndex', $this->getPageIndex());
        $this->set('displayMode', $this->getDisplayMode());
        $this->set('enablePosting', $this->isPostingEnabled());
        $this->set('enableCommentRating', $this->isCommentRatingEnabled());
        $this->set('displaySocialLinks', $this->shouldDisplaySocialLinks());

        return null;
    }

    protected function shouldDisplaySocialLinks():bool
    {
        return (bool) $this->request->request->get('displaySocialLinks');
    }

    protected function getConversationID(): ?int
    {
        $conversationID = $this->request->request->get('cnvID');

        return $this->app->make(Numbers::class)->integer($conversationID, 1) ? (int) $conversationID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getConversation(): Conversation
    {
        $conversationID = $this->getConversationID();
        $conversation = $conversationID === null ? null : Conversation::getByID($conversationID);
        if ($conversation === null) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }

        return $conversation;
    }

    protected function getDisplayMode(): string
    {
        $modes = ['flat', 'threaded'];
        $mode = $this->request->request->get('displayMode');

        return in_array($mode, $modes, true) ? $mode : 'threaded';
    }

    protected function isPostingEnabled(): bool
    {
        return (bool) $this->request->request->get('enablePosting');
    }

    protected function isOrderingEnabled(): bool
    {
        return (bool) $this->request->request->get('enableOrdering');
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

    protected function isCommentRatingEnabled(): bool
    {
        return (bool) $this->request->request->get('enableCommentRating');
    }

    protected function getPageIndex(): int
    {
        $pageID = $this->request->request->get('page');

        return $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : 1;
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
        $messageList->setItemsPerPage($this->getItemsPerPage());
    }
}
