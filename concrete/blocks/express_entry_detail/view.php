<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Entity\Express\Entry|null $entry */
/** @var \Concrete\Core\Express\Form\Renderer|null $renderer */
if (isset($renderer, $entry) && is_object($entry)) {
$renderer->render($entry);
}
