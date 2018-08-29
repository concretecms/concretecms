<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var string $content */
/** @var \Concrete\Core\Page\Page|null $c */

if (!$content && is_object($c) && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Content Block.')?></div>
    <?php
} else {
    echo $content;
}
