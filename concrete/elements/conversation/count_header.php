<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Conversation\Conversation $conversation
 * @var Concrete\Core\View\View $view
 */
?>

<div class="ccm-conversation-message-count"><?= t2('%d Message', '%d Messages', $conversation->getConversationMessagesTotal()) ?></div>
