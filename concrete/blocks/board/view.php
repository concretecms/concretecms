<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Entity\Board\Instance|null $instance */
/** @var \Concrete\Core\Board\Instance\Renderer|null $renderer */
$renderer = $renderer ?? null;
$instance = $instance ?? null;
if ($renderer) {
    $renderer->render($instance);
}
