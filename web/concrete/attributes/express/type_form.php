<fieldset class="ccm-attribute ccm-attribute-date-time">
<legend><?=t('Express Options')?></legend>

<div class="form-group">
<?=$form->label('exEntityID', t('Entity'))?>
<?=$form->select('exEntityID', $entities, $entityID)?>
</div>

</fieldset>
