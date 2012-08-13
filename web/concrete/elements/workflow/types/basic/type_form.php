<div class="control-group">
<fieldset>
<legend><?=t("Workflow Access")?></legend>

<?=Loader::element("permission/lists/basic_workflow", array('enablePermissions' => false, 'workflow' => $workflow));?>
</fieldset></div>