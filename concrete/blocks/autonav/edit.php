<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Block\Autonav\Controller $controller */
/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Core\Block\Block|null $b */
/** @var \Concrete\Core\Page\Page|null $c */
/** @var array<string,mixed> $info */
$info = $controller->getContent();
$view->inc('form_setup_html.php', ['info' => $info, 'c' => $c, 'b' => $b]);
