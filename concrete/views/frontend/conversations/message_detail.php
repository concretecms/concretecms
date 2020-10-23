<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\View\View $view
 * @var Concrete\Controller\Frontend\Conversations\MessageDetail $controller
 * @var Concrete\Core\Conversation\Message\Message $message
 * @var string $displayMode
 * @var bool $enablePosting
 * @var bool $enableCommentRating
 * @var bool $displaySocialLinks
 */

if ($message->isConversationMessageApproved()) {
    View::element(
        'conversation/message',
        [
            'message' => $message,
            'displayMode' => $displayMode,
            'enablePosting' => $enablePosting,
            'enableCommentRating' => $enableCommentRating,
            'displaySocialLinks' => $displaySocialLinks,
        ]
    );
} else {
    // it's a new message, but it's pending
    View::element(
        'conversation/message/pending',
        [
            'message' => $message,
        ]
    );
}
