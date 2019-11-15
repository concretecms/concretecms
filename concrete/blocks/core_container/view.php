<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $c Concrete\Core\Page\Page
 * @var $fileToRender string The file containing the container template.
 */

$container->startRender();

$c = Page::getCurrentPage();
if ($fileToRender) {
    include($fileToRender);
} else {
    if ($c->isEditMode()) { ?>
        <div class="ccm-edit-mode-disabled-item">
           <?php echo t('Container: %s – no container template file found.', 
               $container->getInstance()->getContainer()->getContainerName()); ?>
        </div>
    <?php }
}

$container->endRender();
