<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\View\View $view
 * @var Concrete\Controller\Frontend\Conversations\MessagePage $controller
 * @var Concrete\Core\Conversation\Conversation $conversation
 * @var Concrete\Core\Conversation\Message\MessageList|Concrete\Core\Conversation\Message\ThreadedList $messageList
 * @var int $pageIndex
 * @var string $displayMode
 * @var bool $enablePosting
 * @var bool $enableCommentRating
 * @var bool $displaySocialLinks
 */

// $totalPages = $messageList->getSummary()->pages;

foreach ($messageList->getPage($pageIndex) as $message) {
    View::element(
        'conversation/message',
        [
            'message' => $message,
            'displayMode' => $displayMode,
            'enablePosting' => $enablePosting,
            'enableCommentRating'=> $enableCommentRating,
            'displaySocialLinks'=> $displaySocialLinks,
        ]
    );
}
