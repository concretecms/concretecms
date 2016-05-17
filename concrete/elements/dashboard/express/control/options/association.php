<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('label_mask', t('Association Entity Display Format'))?>
    <?=$form->text('label_mask', $control->getAssociationEntityLabelMask())?>
</div>

<div class="alert alert-info"><?=t('Example: %first_name% %last_name%')?></div>