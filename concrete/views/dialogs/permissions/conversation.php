<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Conversation\Conversation|null $conversation
 */

View::element('permission/details/conversation', ['conversation' => $conversation]);
