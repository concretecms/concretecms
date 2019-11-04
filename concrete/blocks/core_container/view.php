<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $c Concrete\Core\Page\Page
 * @var $fileToRender string The file containing the container template.
 */

$container->startRender();

$c = Page::getCurrentPage();
include($fileToRender);

$container->endRender();
