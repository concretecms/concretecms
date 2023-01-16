<?php

namespace Concrete\Core\Conversation\Command;

use Concrete\Core\Conversation\Message\AuthorFormatter;
use Concrete\Core\Mail\Service;
use Concrete\Core\Utility\Service\Text;

defined('C5_EXECUTE') or die('Access Denied.');

class SendEmailsToConversationMessageSubscribersCommandHandler
{

    /**
     * @var Text
     */
    protected $textService;

    /**
     * @var Service
     */
    protected $mailService;

    public function __construct(Text $textService, Service $mailService)
    {
        $this->textService = $textService;
        $this->mailService = $mailService;
    }

    public function __invoke(SendEmailsToConversationMessageSubscribersCommand $command)
    {
        $message = $command->getMessage();
        $cnv = $message->getConversationObject();
        $author = $message->getConversationMessageAuthorObject();
        $cnvMessageBody = $message->getConversationMessageBodyOutput();
        if ($cnv) {
            $users = $cnv->getConversationUsersToEmail();
            $c = $cnv->getConversationPageObject();
            if (is_object($c)) {
                $formatter = new AuthorFormatter($author);
                $cnvMessageBody = html_entity_decode($cnvMessageBody, ENT_QUOTES, APP_CHARSET);
                foreach ($users as $ui) {
                    $this->mailService->to($ui->getUserEmail());
                    $this->mailService->addParameter('title', $c->getCollectionName());
                    $this->mailService->addParameter('link', $c->getCollectionLink(true));
                    $this->mailService->addParameter('poster', $formatter->getDisplayName());
                    $this->mailService->addParameter('body', $this->textService->prettyStripTags($cnvMessageBody));
                    $this->mailService->load('new_conversation_message');
                    $this->mailService->sendMail();
                }
            }
        }
    }


}