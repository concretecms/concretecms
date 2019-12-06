<?php
defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
if ($fileToRender) {
    include($fileToRender);
} else {
    if ($c->isEditMode()) { ?>
        <div class="ccm-edit-mode-disabled-item">
            <?php if ($board) { ?>
                <?=t('Board Block: %s –  No Template Found.', $board->getBoardName())?>
            <?php } else { ?>
                <?=t('Board Block  – No Board Found.')?>
            <?php } ?>
        </div>
    <?php }
}
