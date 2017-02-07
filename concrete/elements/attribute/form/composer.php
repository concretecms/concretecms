<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$view->getLabel()?></label>
    <?php } ?>

    <?php if ($view->getTooltip()): ?>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$view->getTooltip()?>"></i>
    <?php endif; ?>

    <?php $view->renderControl()?>
</div>
