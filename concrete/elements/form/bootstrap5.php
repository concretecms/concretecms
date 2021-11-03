<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="mb-3">
    <?php if ($view->supportsLabel()) { ?>
        <label class="form-label" for="<?=$view->getControlID()?>"><?=$view->getLabel()?></label>
    <?php } ?>

    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <?php $view->renderControl()?>
</div>
