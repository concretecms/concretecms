<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Frontend\Conversations\GetRating $controller
 * @var Concrete\Core\Conversation\Message\Message $message
 * @var Concrete\Core\View\View $view
 */

echo $message->getConversationMessageTotalRatingScore();
