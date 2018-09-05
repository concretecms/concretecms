<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?=$form->label('label_mask', t('Association Entity Display Format'))?>
    <?=$form->text('label_mask', $control->getAssociationEntityLabelMask())?>
</div>

<div class="form-group">
    <?=$form->label('mode', t('Input Format'))?>
    <?php
    $options = [
        \Concrete\Core\Entity\Express\Control\AssociationControl::TYPE_HTML_INPUT => t('HTML Input'),
        \Concrete\Core\Entity\Express\Control\AssociationControl::TYPE_ENTRY_SELECTOR => t('Entry Selector'),
    ];
    ?>
    <?= $form->select('mode', $options, $control->getEntrySelectorMode()); ?>
</div>

<div class="alert alert-info"><?=t('Example: %first_name% %last_name%')?></div>