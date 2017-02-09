<?php

namespace Concrete\Core\Conversation;

use Concrete\Core\Conversation\Message\MessageEvent;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        $obj = $this;
        $handler = function(MessageEvent $message) use ($obj) {
            $obj->app->make(ConversationService::class)->trackReview($message->getMessage());
        };

        $director = $this->app->make('director');
        $director->addListener('on_conversations_message_add', $handler);
        $director->addListener('on_conversations_message_update', $handler);
    }

}
