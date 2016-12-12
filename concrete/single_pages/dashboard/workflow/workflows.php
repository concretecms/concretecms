<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $c = Page::getCurrentPage(); ?>

<? if (isset($wf)) { ?>

<? if ($this->controller->getTask() == 'edit_details') { ?>

<form method="post" action="<?=$view->action('save_workflow_details')?>" method="post">
<input type="hidden" name="wfID" value="<?=$wf->getWorkflowID()?>" />
<?=Loader::helper('validation/token')->output('save_workflow_details')?>

<? Loader::element("workflow/edit_type_form_required", array('workflow' => $wf)); ?>

<div class="ccm-dashboard-form-actions-wrapper">
<div class="ccm-dashboard-form-actions">
	<a href="<?=URL::page($c, 'view_detail', $wf->getWorkflowID())?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
	<input type="submit" name="submit" value="<?=t('Save')?>" class="btn btn-primary pull-right" />
</div>
</div>
</form>

<? } else { ?>

	<? Loader::element("workflow/type_form_required", array('workflow' => $wf)); ?>

<? } ?>



<? } else if ($this->controller->getTask() == 'add' || $this->controller->getTask() == 'submit_add') { ?>

	<form method="post" action="<?=$view->action('submit_add')?>">
	<?=Loader::helper('validation/token')->output('add_workflow')?>
		<fieldset>
		
			<legend><?=t('Add Workflow')?></legend>
			
			<div class="form-group">
				<?=$form->label('wfName', t('Name'))?>
				<div class="input-group">
					<?=$form->text('wfName', $wfName)?>
					<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
				</div>
			</div>
			
			<div class="form-group">
				<?=$form->label('wftID', t('Type'))?>
				<div class="input-group">
					<?=$form->select('wftID', $types)?>
				</div>
			</div>

			<? foreach($typeObjects as $type) { ?>
				
				<div style="display: none" class="form-group ccm-workflow-type-form" id="ccm-workflow-type-<?=$type->getWorkflowTypeID()?>">
					<? 
					if ($type->getPackageID() > 0) { 
						@Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle()  . '/add_type_form', $type->getPackageHandle(), array('type' => $type));
					} else {
						@Loader::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/add_type_form', array('type' => $type));
					}
					?>
				</div>
			<? } ?>
		</fieldset>
		
		<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?=URL::page($c)?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
			<button type="submit" class="btn btn-primary pull-right"><?=t('Add')?></button>
		</div>
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

<? } else { ?>

	<div class="ccm-dashboard-header-buttons">
		<a href="<?=URL::to('/dashboard/workflow/workflows', 'add')?>" class="btn btn-primary"><?=t('Add Workflow')?></a>
	</div>
	
	<h4><?=t2('%d Workflow', '%d Workflows', count($workflows))?></h4>
	
	<ul class="item-select-list">
	<? foreach($workflows as $workflow) { ?>
		<li><a href="<?=$view->url('/dashboard/workflow/workflows', 'view_detail', $workflow->getWorkflowID())?>"><i class="fa fa-exchange"></i> <?=$workflow->getWorkflowDisplayName()?></a></li>
	<? } ?>
	</ul>

<? } ?>