<fieldset class="ccm-attribute ccm-attribute-date-time">
<legend><?=t('Express Options'); ?></legend>

<div class="form-group" data-group="single-value">
<label><?=t("Required"); ?></label>
<div class="checkbox">
    <label>
        <?=$form->checkbox('akIsRequired', 1, $akIsRequired); ?> <span><?=t('Ensure a value is selected.'); ?></span>
    </label>
</div>
</div>

<div class="form-group">
<?=$form->label('exEntityID', t('Entity')); ?>
<?=$form->select('exEntityID', $entities, $entityID);?>
</div>

</fieldset>
