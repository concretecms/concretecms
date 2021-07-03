<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="form-check">
    <input class="form-check-input"
        type="checkbox"
        value="1"
        name="<?=$view->field('value')?>"
           id="<?=$view->field('value')?>"
        <?php if ($checked) { ?> checked <?php } ?>
    >
    <label class="form-check-label" for="<?=$view->field('value')?>">
        <?=t($controller->getCheckboxLabel())?>
    </label>
</div>