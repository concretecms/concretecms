<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('headline', t('Headline'))?>
    <?=$form->text('headline', $control->getHeadline())?>
</div>
<div class="form-group">
    <?=$form->label('body', t('Body'))?>
    <?php
    $editor = Core::make('editor');
    print $editor->outputStandardEditor('body', $control->getBody());
    ?>
</div>
