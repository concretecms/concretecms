<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Frontend\Conversations\CountHeader $controller
 * @var Concrete\Core\Conversation\Conversation $conversation
 * @var Concrete\Core\View\View $view
 */

View::element('conversation/count_header', ['conversation' => $conversation]);
