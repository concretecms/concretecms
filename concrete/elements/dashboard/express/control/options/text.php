<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('text', t('Text Field Value'))?>
    <?=$form->textarea('text', $control->getText())?>
</div>