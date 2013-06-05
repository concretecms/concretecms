<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($wf)) { ?>

<? if ($this->controller->getTask() == 'edit_details') { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Workflow'), false, 'span10 offset1', false)?>
<form method="post"  action="<?=$this->action('save_workflow_details')?>" method="post" class="form-horizontal">
<input type="hidden" name="wfID" value="<?=$wf->getWorkflowID()?>" />
<?=Loader::helper('validation/token')->output('save_workflow_details')?>

<div class="ccm-pane-body">
	<? Loader::element("workflow/edit_type_form_required", array('workflow' => $wf)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/workflow/list/view_detail', $wf->getWorkflowID())?>" class="btn"><?=t("Cancel")?></a>
	<input type="submit" name="submit" value="<?=t('Save')?>" class="ccm-button-right primary btn" />
</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } else { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($wf->getWorkflowName(), false, 'span10 offset1', false)?>

<? Loader::element("workflow/type_form_required", array('workflow' => $wf)); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } ?>



<? } else if ($this->controller->getTask() == 'add' || $this->controller->getTask() == 'submit_add') { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Workflow'), false, 'span10 offset1', false)?>

	<form method="post" class="form-horizontal" action="<?=$this->action('submit_add')?>" id="ccm-attribute-type-form">
	<?=Loader::helper('validation/token')->output('add_workflow')?>
	<div class="ccm-pane-body">

	<div class="control-group">
	<?=$form->label('wfName', t('Name'))?>
	<div class="controls">
		<?=$form->text('wfName', $wfName)?>
		<span class="help-inline"><?=t('Required')?></span>
	</div>
	</div>

	<div class="control-group">
	<?=$form->label('wftID', t('Type'))?>
	<div class="controls">
	
	<?=$form->select('wftID', $types)?>
	
	</div>
	</div>

	<? foreach($typeObjects as $type) { ?>
		
		<div style="display: none" class="ccm-workflow-type-form" id="ccm-workflow-type-<?=$type->getWorkflowTypeID()?>">
			<? 
			if ($type->getPackageID() > 0) { 
				@Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle()  . '/add_type_form', $type->getPackageHandle(), array('type' => $type));
			} else {
				@Loader::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/add_type_form', array('type' => $type));
			}
			?>
		</div>
	<? } ?>

	</div>
	<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/workflow/list')?>" class="btn"><?=t("Cancel")?></a>
	<input type="submit" name="submit" value="<?=t('Add')?>" class="ccm-button-right primary btn" />
	</div>	
	</form>
	
	<script type="text/javascript">
	$(function() {
		$('select[name=wftID]').change(function() {
			$('.ccm-workflow-type-form').hide();
			$('#ccm-workflow-type-' + $(this).val()).show();
		})
		$('#ccm-workflow-type-' + $('select[name=wftID]').val()).show();
	});
	</script>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Workflows'), false, 'span10 offset1')?>

	<a href="<?=View::url('/dashboard/workflow/list', 'add')?>" style="float: right" class="btn primary"><?=t("Add Workflow")?></a>
	
	<h4><?=count($workflows)?> <?
		if (count($workflows) == 1) {
			print t('Workflow');
		} else {
			print t('Workflows');
		}
	?></h4>
	<br/>
	<? foreach($workflows as $workflow) { ?>
	<div class="ccm-workflow">
		<a href="<?=$this->url('/dashboard/workflow/list', 'view_detail', $workflow->getWorkflowID())?>"><?=$workflow->getWorkflowName()?></a>
	</div>
	<? } ?>
		
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
<? } ?>