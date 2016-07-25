<fieldset class="ccm-attribute ccm-attribute-date-time">
<legend><?=t('Date/Time Options')?></legend>

<div class="form-group">
<?=$form->label('akDateDisplayMode', t('Ask User For'))?>
<?php
    $akDateDisplayModeOptions = array(
        'date_time' => t('Both Date and Time'),
        'date' => t('Date Only'),
        'text' => t('Text Input Field'),

    );
    ?>
<?=$form->select('akDateDisplayMode', $akDateDisplayModeOptions, $akDateDisplayMode)?>
</div>

</fieldset>
