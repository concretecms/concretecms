<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Board\Instance\Slot\Content\ContentRenderer $renderer
 * @var \Concrete\Core\Entity\Board\SlotTemplate|null $template
 * @var \Concrete\Core\Board\Instance\Slot\Content\ObjectCollection $dataCollection
 */

if ($template) {
    echo $renderer->render($dataCollection, $template);
}
