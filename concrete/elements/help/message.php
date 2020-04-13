<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Core\Application\Service\UserInterface\Help\Message $message
* @var Concrete\Core\Application\Service\UserInterface\Help\MessageFormatterInterface $messageFormatter
*/

echo (string) $messageFormatter->format($message);
