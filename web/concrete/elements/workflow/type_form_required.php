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


<h3><?=t('Type')?></h3>
<p><?=$type->getWorkflowTypeName()?></p>

<h3><?=t('Name')?></h3>
<p><?=$wfName?></p>



<? 
if ($type->getPackageID() > 0) { 
	Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} else {
	Loader::element('workflow/types/' . $type->getWorkflowTypeHandle(), array('type' => $type, 'workflow' => $workflow));
}
?>

</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/workflow/list')?>" class="btn"><?=t('Back to List')?></a>
</div>
