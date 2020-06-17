<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
$ih = $app->make('helper/concrete/ui');
$valt = $app->make('helper/validation/token');

$wfName = $workflow->getWorkflowName();
$type = $workflow->getWorkflowTypeObject();

?>

<fieldset>
<legend><?=t("Workflow Basics")?></legend>

<div class="form-group">
	<?=$form->label('wfName', t('Name'))?>
	<div class="input-group">
		<?=$form->text('wfName', $wfName)?>
		<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
	</div>
</div>
</fieldset>

<?php
if ($type->getPackageID() > 0) {
    @View::element('workflow/types/' . $type->getWorkflowTypeHandle()  . '/edit_type_form', $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} else {
    @View::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/edit_type_form', array('type' => $type, 'workflow' => $workflow));
}
?>
