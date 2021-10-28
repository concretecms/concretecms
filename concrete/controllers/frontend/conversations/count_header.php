<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class CountHeader extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/count_header';

    public function view(): ?Response
    {
        $conversation = null;
        $cnvID = $this->request->request->get('cnvID');
        if ($this->app->make(Numbers::class)->integer($cnvID, 1)) {
            $cnvID = (int) $cnvID;
            $conversation = Conversation::getByID($cnvID);
        }
        if ($conversation === null) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }
        $this->set('conversation', $conversation);

        return null;
    }
}
