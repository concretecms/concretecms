<?php

use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $environment \Twig\Environment
 */
$content = $environment->render('block', [
    'c' => Page::getCurrentPage(),
    'view' => $view,
]);
echo $content;
