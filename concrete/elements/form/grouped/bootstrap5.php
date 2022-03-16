<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<fieldset class="mb-3">
    <?php if ($view->supportsLabel()) { ?>
        <legend><?=$view->getLabel()?></legend>
    <?php } ?>

    <?php $view->renderControl()?>
</fieldset>
