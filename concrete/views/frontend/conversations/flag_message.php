<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Frontend\Conversations\FlagMessage $controller
 * @var Concrete\Core\Conversation\Message\Message $message
 * @var Concrete\Core\View\View $view
 */

View::element('conversation/message', ['message' => $message]);
