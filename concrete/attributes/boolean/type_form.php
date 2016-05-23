<fieldset>
<legend><?=t('Checkbox Options')?></legend>

<div class="form-group">
    <label class="control-label"><?=t("Default Value")?></label>
    <div class="checkbox"><label><?=$form->checkbox('akCheckedByDefault', 1, $akCheckedByDefault)?> <?=t('The checkbox will be checked by default.')?></label>
    </div>
</div>

</fieldset>