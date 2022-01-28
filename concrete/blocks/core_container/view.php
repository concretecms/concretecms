<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Page|null $c
 * @var string|null $fileToRender The file containing the container template
 */
$container = $container ?? null;
$fileToRender = $fileToRender ?? null;
$c = $c ?? \Concrete\Core\Page\Page::getCurrentPage();
if ($container) {
    $container->startRender();

    if ($fileToRender) {
        include $fileToRender;
    } else {
        if (is_object($c) && $c->isEditMode()) { ?>
            <div class="ccm-edit-mode-disabled-item">
               <?php echo t(
            'Container: %s – no container template file found.',
            $container->getInstance()->getContainer()->getContainerDisplayName()
        ); ?>
            </div>
        <?php }
    }

    $container->endRender();
} elseif (is_object($c) && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Container Block.')?></div>
    <?php
}
