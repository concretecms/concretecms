<fieldset>
<legend><?=t('Checkbox Options')?></legend>

<div class="clearfix">
<label><?=t("Default Value")?></label>
<div class="input">
<ul class="inputs-list">
<li><label><?=$form->checkbox('akCheckedByDefault', 1, $akCheckedByDefault)?> <span><?=t('The checkbox will be checked by default.')?></span></label></li>
</ul>
</div>
</div>

</fieldset>