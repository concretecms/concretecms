<fieldset class="ccm-attribute ccm-attribute-date-time">
<legend><?=t('Express Options')?></legend>

<div class="form-group">
<?=$form->label('exEntityID', t('Entity'), ['class' => 'form-label'])?>
<?=$form->select('exEntityID', $entities, $entityID)?>
</div>

</fieldset>
