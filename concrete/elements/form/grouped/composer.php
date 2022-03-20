<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<fieldset>
    <?php if ($view->supportsLabel()) { ?>
        <legend class="control-label form-label"><?=$view->getLabel()?></legend>
    <?php } ?>

    <?php if ($context->getTooltip()): ?>
        <i class="fas fa-question-circle launch-tooltip" data-bs-toggle="tooltip" title="<?= h($context->getTooltip()); ?>"></i>
    <?php endif; ?>

    <?php $view->renderControl()?>
</fieldset>
