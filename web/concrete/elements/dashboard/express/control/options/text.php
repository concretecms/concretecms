<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('text', t('Custom Label'))?>
    <?=$form->textarea('text', $control->getText())?>
</div>