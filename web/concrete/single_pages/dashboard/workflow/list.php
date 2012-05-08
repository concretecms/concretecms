<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($wf)) { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Workflow Details'), false, false, false)?>

<form method="post" action="<?=$this->action('edit')?>" id="ccm-workflow-form">

<? Loader::element("workflow/type_form_required", array('wf' => $wf)); ?>

</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>




<? } else if ($this->controller->getTask() == 'add' || $this->controller->getTask() == 'submit_add') { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Workflow'), false, 'span12 offset2', false)?>

	<form method="post" class="" action="<?=$this->action('submit_add')?>" id="ccm-attribute-type-form">
	<?=Loader::helper('validation/token')->output('add_workflow')?>
	<div class="ccm-pane-body">

	<div class="clearfix">
	<?=$form->label('wfName', t('Name'))?>
	<div class="input">
		<?=$form->text('wfName', $wfName)?>
		<span class="help-inline"><?=t('Required')?></span>
	</div>
	</div>

	<div class="clearfix">
	<?=$form->label('wftID', t('Type'))?>
	<div class="input">
	
	<?=$form->select('wftID', $types)?>
	
	</div>
	</div>
	

	</div>
	<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/workflow/list')?>" class="btn"><?=t("Cancel")?></a>
	<input type="submit" name="submit" value="<?=t('Add')?>" class="ccm-button-right primary btn" />
	</div>	
	</form>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Workflows'), false, 'span12 offset2')?>

		<a href="<?=View::url('/dashboard/workflow/list', 'add')?>" style="float: right;z-index:999;position:relative;top:-5px" class="btn primary"><?=t("Add Workflow")?></a>
		
	<br/><br/>
		
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
<? } ?>