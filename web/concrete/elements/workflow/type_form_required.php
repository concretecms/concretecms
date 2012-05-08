<? 
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');

$wfName = $workflow->getWorkflowName();
$type = $workflow->getWorkflowTypeObject();

?>

<div class="ccm-pane-body">

<? if (is_object($workflow)) { ?>

	<?
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this workflow?');
	?>
	<script type="text/javascript">
	deleteAttribute = function() {
		if (confirm('<?=$delConfirmJS?>')) { 
			location.href = "<?=$this->action('delete', $workflow->getWorkflowID(), $valt->generate('delete_workflow'))?>";				
		}
	}
	</script>
	
	<? print $ih->button_js(t('Delete Workflow'), "deleteWorkflow()", 'right', 'error');?>
<? } ?>


<div class="clearfix">
<label><?=t('Type')?></label>
<div class="input" style="padding-top: 8px">
	<?=$type->getWorkflowTypeName()?>
</div>
</div>


<div class="clearfix">
<?=$form->label('wfName', t('Workflow Name'))?>
<div class="input">
	<?=$form->text('wfName', $wfName)?>
	<span class="help-inline"><?=t('Required')?></span>
</div>
</div>

<?=$form->hidden('wftID', $type->getWorkflowTypeID())?>
<?=$valt->output('update_workflow')?>

<? 
if ($type->getPackageID() > 0) { 
	Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} else {
	Loader::element('workflow/types/' . $type->getWorkflowTypeHandle(), array('type' => $type, 'workflow' => $workflow));
}
?>

</div>
<div class="ccm-pane-footer">
	<?=$ih->submit(t('Save'), 'ccm-workflow-form', 'right', 'primary')?>
</div>
