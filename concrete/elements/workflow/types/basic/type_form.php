<div class="control-group">
<fieldset>
<legend><?=t("Workflow Access")?></legend>

<?=View::element("permission/lists/basic_workflow", array('enablePermissions' => false, 'workflow' => $workflow));?>
</fieldset></div>
